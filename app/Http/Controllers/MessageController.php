<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $authId = Auth::id();

        // Get all partner IDs involved in messages with auth user
        $sentTo = Message::where('sender_id', $authId)->pluck('receiver_id');
        $receivedFrom = Message::where('receiver_id', $authId)->pluck('sender_id');
        $partnerIds = $sentTo->merge($receivedFrom)->unique()->values();

        $conversations = collect();

        foreach ($partnerIds as $partnerId) {
            $partner = User::find($partnerId);
            if (!$partner) {
                continue;
            }

            $lastMessage = Message::where(function ($q) use ($authId, $partnerId) {
                $q->where('sender_id', $authId)->where('receiver_id', $partnerId);
            })->orWhere(function ($q) use ($authId, $partnerId) {
                $q->where('sender_id', $partnerId)->where('receiver_id', $authId);
            })->orderByDesc('send_at')->first();

            $unreadCount = Message::where('sender_id', $partnerId)
                ->where('receiver_id', $authId)
                ->where('is_read', false)
                ->count();

            $conversations->push((object)[
                'user' => $partner,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
            ]);
        }

        // Sort by last message send_at desc
        $conversations = $conversations->sortByDesc(fn($c) => $c->last_message?->send_at)->values();

        // Suggested users to start new chat
        $suggestedUsers = User::where('id', '!=', $authId)
            ->whereNotIn('id', $partnerIds)
            ->limit(6)
            ->get();

        return view('messages.index', compact('conversations', 'suggestedUsers'));
    }

    public function show($conversation)
    {
        $authId = Auth::id();
        $otherUser = User::findOrFail($conversation);

        // Mark unread messages from this user as read
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Retrieve conversation messages
        $messages = Message::where(function ($q) use ($authId, $otherUser) {
            $q->where('sender_id', $authId)->where('receiver_id', $otherUser->id);
        })->orWhere(function ($q) use ($authId, $otherUser) {
            $q->where('sender_id', $otherUser->id)->where('receiver_id', $authId);
        })->orderBy('send_at', 'asc')->get();

        return view('messages.show', compact('otherUser', 'messages'));
    }

    public function store(Request $request, $conversation)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $authId = Auth::id();
        $otherUser = User::findOrFail($conversation);

        $message = Message::create([
            'sender_id' => $authId,
            'receiver_id' => $otherUser->id,
            'content' => trim($request->content),
            'is_read' => false,
            'send_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('messages.show', $otherUser->id);
    }
}
