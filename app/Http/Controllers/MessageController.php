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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index()
    {
        $authId = Auth::id();
        $conversations = collect();
        $clubIds = ClubMember::where('user_id', $authId)
            ->pluck('club_id');
        $clubMembers = ClubMember::with(['user', 'club'])
            ->whereIn('club_id', $clubIds)
            ->where('user_id', '!=', $authId)
            ->get();
        $latestOutgoing = Message::query()
            ->select('club_id')
            ->selectRaw('receiver_id AS partner_id')
            ->selectRaw('MAX(send_at) AS latest_send_at')
            ->whereIn('club_id', $clubIds)
            ->where('sender_id', $authId)
            ->groupBy('club_id')
            ->groupBy('receiver_id');
        $latestIncoming = Message::query()
            ->select('club_id')
            ->selectRaw('sender_id AS partner_id')
            ->selectRaw('MAX(send_at) AS latest_send_at')
            ->whereIn('club_id', $clubIds)
            ->where('receiver_id', $authId)
            ->groupBy('club_id')
            ->groupBy('sender_id');
        $latestPerConversation = $latestOutgoing->unionAll($latestIncoming);
        $latestMessages = Message::query()
            ->select('messages.*')
            ->joinSub($latestPerConversation, 'latest_message', function ($join) use ($authId) {
                $join->on('messages.club_id', '=', 'latest_message.club_id')
                    ->on('messages.send_at', '=', 'latest_message.latest_send_at')
                    ->where(function ($join) use ($authId) {
                        $join->where(function ($join) use ($authId) {
                            $join->where('messages.sender_id', $authId)
                                ->whereColumn('messages.receiver_id', 'latest_message.partner_id');
                        })->orWhere(function ($join) use ($authId) {
                            $join->where('messages.receiver_id', $authId)
                                ->whereColumn('messages.sender_id', 'latest_message.partner_id');
                        });
                    });
            })
            ->orderByDesc('messages.send_at')
            ->orderByDesc('messages.id')
            ->get();
        $latestMessagesByConversation = collect();
        foreach ($latestMessages as $message) {
            $partnerId = $message->sender_id === $authId ? $message->receiver_id : $message->sender_id;
            $key = $message->club_id . ':' . $partnerId;
            if (!$latestMessagesByConversation->has($key)) {
                $latestMessagesByConversation->put($key, $message);
            }
        }
        $unreadCounts = Message::query()
            ->select('club_id', 'sender_id')
            ->selectRaw('COUNT(*) AS unread_count')
            ->whereIn('club_id', $clubIds)
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->groupBy('club_id', 'sender_id')
            ->get()
            ->keyBy(fn (Message $message) => $message->club_id . ':' . $message->sender_id);
        $suggestedUsers = collect();

        foreach ($clubMembers as $clubMember) {
            if (!$clubMember->user || !$clubMember->club) {
                continue;
            }

            $partnerId = $clubMember->user_id;
            $key = $clubMember->club_id . ':' . $partnerId;
            $lastMessage = $latestMessagesByConversation->get($key);

            if ($lastMessage) {
                $conversations->push((object) [
                    'key' => $key,
                    'user' => $clubMember->user,
                    'club' => $clubMember->club,
                    'last_message' => $lastMessage,
                    'unread_count' => (int) ($unreadCounts->get($key)?->unread_count ?? 0),
                ]);
            } else {
                $suggestedUsers->push((object) [
                    'key' => $key,
                    'user' => $clubMember->user,
                    'club' => $clubMember->club,
                ]);
            }
        }

        $conversations = $conversations->sortByDesc(fn ($conversation) => $conversation->last_message?->send_at)->values();
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
                    ->orWhere('deleted_at', '>=', $after)
                    ->orWhere('viewed_at', '>=', $after));
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
                ...$this->messagePayload($message),
            ]),
            'read_message_ids' => Message::where('club_id', $club->id)
                ->where('sender_id', $authId)
                ->where('receiver_id', $otherUser->id)
                ->where('is_read', true)
                ->pluck('id'),
        ]);
    }

    public function image(string $clubId, string $conversation, string $messageId)
    {
        $club = Club::findOrFail($clubId);
        $otherUser = User::findOrFail($conversation);
        $this->authorizeConversation($club, $otherUser);

        $disk = Storage::disk('local');
        $message = Message::where('id', $messageId)
            ->where('club_id', $club->id)
            ->whereNull('deleted_at')
            ->whereNotNull('photo_path')
            ->where(function ($query) use ($otherUser) {
                $query->where(fn ($query) => $query->where('sender_id', Auth::id())->where('receiver_id', $otherUser->id))
                    ->orWhere(fn ($query) => $query->where('sender_id', $otherUser->id)->where('receiver_id', Auth::id()));
            })
            ->firstOrFail();

        if ($message->view_once) {
            abort_unless($message->receiver_id === Auth::id(), 403);
        }

        abort_unless($disk->exists($message->photo_path), 404);
        if ($message->view_once) {
            $claimed = Message::whereKey($message->id)
                ->whereNull('viewed_at')
                ->update(['viewed_at' => now()]);
            abort_unless($claimed === 1, 410, 'Foto sekali lihat sudah dibuka.');
        }

        $response = response()->file($disk->path($message->photo_path), [
            'Content-Type' => $disk->mimeType($message->photo_path) ?? 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ]);
        if ($message->view_once) {
            $response->deleteFileAfterSend(true);
        }

        return $response;
    }

    /**
     * Menyimpan pesan baru ke database.
     */
    public function store(Request $request, string $clubId, string $conversation)
    {
        $validated = $request->validate([
            'content' => ['required_without:photo', 'nullable', 'string', 'max:5000', 'not_regex:/^\s*$/'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'view_once' => ['nullable', 'boolean'],
        ]);
        $content = trim($validated['content'] ?? '');

        $authId = Auth::id();
        $club = Club::findOrFail($clubId);
        $otherUser = User::findOrFail($conversation);
        $this->authorizeConversation($club, $otherUser);

        $photoPath = $request->file('photo')?->store('chat-images', 'local');
        if ($request->hasFile('photo') && !$photoPath) {
            abort(500, 'Foto gagal disimpan.');
        }

        $chatData = [
            'club_id'     => $club->id,
            'sender_id'   => $authId,
            'receiver_id' => $otherUser->id,
            'content'     => $content,
            'photo_path'  => $photoPath,
            'view_once'   => $photoPath !== null && $request->boolean('view_once'),
            'is_read'     => false,
            'send_at'     => now(),
        ];

        $chat = Message::create($chatData);

        Notification::create([
            'user_id' => $otherUser->id,
            'title' => 'Pesan baru',
            'content' => auth()->user()->name . ': ' . ($content !== '' ? Str::limit($content, 80) : 'Mengirim foto'),
            'type' => 'message',
            'source_id' => $chat->id,
            'is_read' => false,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'chat_message' => $this->messagePayload($chat),
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
        abort_if($message->is_read, 403, 'Pesan yang sudah dibaca tidak dapat diedit.');

        $updated = Message::whereKey($message->id)
            ->where('is_read', false)
            ->update([
                'content' => trim($validated['content']),
                'edited_at' => now(),
            ]);
        abort_unless($updated === 1, 403, 'Pesan yang sudah dibaca tidak dapat diedit.');

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

    private function messagePayload(Message $message): array
    {
        return [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'content' => $message->content,
            'photo_url' => $message->photo_path && !$message->deleted_at
                && !$message->view_once
                ? route('messages.image', [$message->club_id, $message->sender_id === Auth::id() ? $message->receiver_id : $message->sender_id, $message->id])
                : null,
            'view_once' => $message->view_once,
            'view_once_url' => $message->photo_path && $message->view_once && !$message->deleted_at
                ? route('messages.image', [$message->club_id, $message->sender_id === Auth::id() ? $message->receiver_id : $message->sender_id, $message->id])
                : null,
            'viewed_at' => $message->viewed_at?->toIso8601String(),
            'send_at' => $message->send_at?->toIso8601String(),
            'is_read' => $message->is_read,
            'edited_at' => $message->edited_at?->toIso8601String(),
            'is_deleted' => $message->deleted_at !== null,
        ];
    }
}
