<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index()
    {
        $authId = Auth::id();
        $conversations = collect();
        $memberships = ClubMember::with('club')
            ->where('user_id', $authId)
            ->get();
        $clubIds = $memberships->pluck('club_id');
        $messages = Message::whereIn('club_id', $clubIds)
            ->where(fn ($query) => $query->where('sender_id', $authId)->orWhere('receiver_id', $authId))
            ->orderByDesc('send_at')
            ->get();
        $suggestedUsers = collect();

        foreach ($memberships as $membership) {
            $clubMembers = ClubMember::with('user')
                ->where('club_id', $membership->club_id)
                ->where('user_id', '!=', $authId)
                ->get();

            foreach ($clubMembers as $clubMember) {
                if (!$clubMember->user) {
                    continue;
                }

                $partnerId = $clubMember->user_id;
                $key = $membership->club_id . ':' . $partnerId;
                $conversationMessages = $messages->where('club_id', $membership->club_id)
                    ->filter(fn (Message $message) => in_array($authId, [$message->sender_id, $message->receiver_id], true)
                        && in_array($partnerId, [$message->sender_id, $message->receiver_id], true));
                $lastMessage = $conversationMessages->first();

                if ($lastMessage) {
                    $conversations->push((object) [
                        'key' => $key,
                        'user' => $clubMember->user,
                        'club' => $membership->club,
                        'last_message' => $lastMessage,
                        'unread_count' => $conversationMessages
                            ->where('sender_id', $partnerId)
                            ->where('receiver_id', $authId)
                            ->where('is_read', false)
                            ->count(),
                    ]);
                } else {
                    $suggestedUsers->push((object) [
                        'key' => $key,
                        'user' => $clubMember->user,
                        'club' => $membership->club,
                    ]);
                }
            }
        }

        $conversations = $conversations->sortByDesc(fn($c) => $c->last_message?->send_at)->values();
        $suggestedUsers = $suggestedUsers->unique('key')->take(6)->values();

        return view('messages.index', compact('conversations', 'suggestedUsers'));
    }

    public function show(string $clubId, string $conversation)
    {
        $authId = Auth::id();
        $club = Club::findOrFail($clubId);
        $otherUser = User::findOrFail($conversation);
        $this->authorizeConversation($club, $otherUser);

        // Mark unread messages from this user as read
        $unreadIds = Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $authId)
            ->where('club_id', $club->id)
            ->where('is_read', false)
            ->pluck('id');

        if ($unreadIds->isNotEmpty()) {
            Message::whereIn('id', $unreadIds)->update(['is_read' => true]);
            Notification::where('user_id', $authId)
                ->where('type', 'message')
                ->whereIn('source_id', $unreadIds)
                ->update(['is_read' => true]);
        }

        // Retrieve conversation messages
        $messages = Message::where('club_id', $club->id)
            ->where(function ($query) use ($authId, $otherUser) {
                $query->where(fn ($query) => $query->where('sender_id', $authId)->where('receiver_id', $otherUser->id))
                    ->orWhere(fn ($query) => $query->where('sender_id', $otherUser->id)->where('receiver_id', $authId));
            })
            ->orderBy('send_at', 'asc')
            ->get();

        return view('messages.show', compact('club', 'otherUser', 'messages'));
    }

    public function updates(Request $request, string $clubId, string $conversation): JsonResponse
    {
        $validated = $request->validate([
            'after' => ['nullable', 'date'],
        ]);

        $authId = Auth::id();
        $club = Club::findOrFail($clubId);
        $otherUser = User::findOrFail($conversation);
        $this->authorizeConversation($club, $otherUser);
        $messages = Message::where('club_id', $club->id)
            ->where(function ($query) use ($authId, $otherUser) {
                $query->where(fn ($query) => $query->where('sender_id', $authId)->where('receiver_id', $otherUser->id))
                    ->orWhere(fn ($query) => $query->where('sender_id', $otherUser->id)->where('receiver_id', $authId));
            })
            ->when($validated['after'] ?? null, function ($query, $after) {
                $after = Carbon::parse($after)->toDateTimeString();
                $query->where(fn ($query) => $query->where('send_at', '>=', $after)
                    ->orWhere('edited_at', '>=', $after)
                    ->orWhere('deleted_at', '>=', $after));
            })
            ->orderBy('send_at')
            ->orderBy('id')
            ->limit(100)
            ->get();

        $unreadIds = Message::where('club_id', $club->id)
            ->where('sender_id', $otherUser->id)
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->pluck('id');

        if ($unreadIds->isNotEmpty()) {
            Message::whereIn('id', $unreadIds)->update(['is_read' => true]);
            Notification::where('user_id', $authId)
                ->where('type', 'message')
                ->whereIn('source_id', $unreadIds)
                ->update(['is_read' => true]);
        }

        return response()->json([
            'messages' => $messages->map(fn (Message $message) => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'content' => $message->content,
                'send_at' => $message->send_at?->toIso8601String(),
                'is_read' => $message->is_read,
                'edited_at' => $message->edited_at?->toIso8601String(),
                'is_deleted' => $message->deleted_at !== null,
            ]),
        ]);
    }

    /**
     * Menyimpan pesan baru ke database.
     */
    public function store(Request $request, string $clubId, string $conversation)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $authId = Auth::id();
        $club = Club::findOrFail($clubId);
        $otherUser = User::findOrFail($conversation);
        $this->authorizeConversation($club, $otherUser);

        $chatData = [
            'club_id'     => $club->id,
            'sender_id'   => $authId,
            'receiver_id' => $otherUser->id,
            'content'     => trim($request->input('content')),
            'is_read'     => false,
            'send_at'     => now(),
        ];

        $chat = Message::create($chatData);

        Notification::create([
            'user_id' => $otherUser->id,
            'title' => 'Pesan baru',
            'content' => auth()->user()->name . ': ' . Str::limit($chatData['content'], 80),
            'type' => 'message',
            'source_id' => $chat->id,
            'is_read' => false,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'chat_message' => $chat,
            ]);
        }

        return redirect()->route('messages.show', [$club->id, $otherUser->id]);
    }

    public function update(Request $request, string $clubId, string $conversation, string $messageId)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $club = Club::findOrFail($clubId);
        $otherUser = User::findOrFail($conversation);
        $this->authorizeConversation($club, $otherUser);
        $message = Message::where('id', $messageId)
            ->where('club_id', $club->id)
            ->where('sender_id', Auth::id())
            ->where('receiver_id', $otherUser->id)
            ->whereNull('deleted_at')
            ->firstOrFail();
        $message->update([
            'content' => trim($validated['content']),
            'edited_at' => now(),
        ]);

        return redirect()->route('messages.show', [$club->id, $otherUser->id]);
    }

    public function destroy(string $clubId, string $conversation, string $messageId)
    {
        $club = Club::findOrFail($clubId);
        $otherUser = User::findOrFail($conversation);
        $this->authorizeConversation($club, $otherUser);
        $message = Message::where('id', $messageId)
            ->where('club_id', $club->id)
            ->where('sender_id', Auth::id())
            ->where('receiver_id', $otherUser->id)
            ->whereNull('deleted_at')
            ->firstOrFail();
        $message->update([
            'content' => 'Pesan telah dihapus',
            'deleted_at' => now(),
        ]);

        return redirect()->route('messages.show', [$club->id, $otherUser->id]);
    }

    private function authorizeConversation(Club $club, User $otherUser): void
    {
        $memberIds = ClubMember::where('club_id', $club->id)
            ->whereIn('user_id', [Auth::id(), $otherUser->id])
            ->pluck('user_id')
            ->unique();

        abort_unless($memberIds->contains(Auth::id()) && $memberIds->contains($otherUser->id), 403);
    }
}
