<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index(?string $conversation = null)
    {
        $user = Auth::user();

        $messages = Message::query()
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('send_at', 'asc')
            ->get();

        $conversations = [];

        foreach ($messages as $message) {
            $otherUser = $message->sender_id === $user->id ? $message->receiver : $message->sender;

            if (!$otherUser) {
                continue;
            }

            $otherUserId = $otherUser->id;

            if (!isset($conversations[$otherUserId])) {
                $conversations[$otherUserId] = [
                    'id' => $otherUserId,
                    'name' => $otherUser->name,
                    'username' => '@' . Str::slug($otherUser->name),
                    'last_message' => '',
                    'time' => '',
                    'messages' => [],
                ];
            }

            $conversations[$otherUserId]['messages'][] = [
                'from' => $message->sender_id === $user->id ? 'me' : 'them',
                'text' => $message->content,
                'time' => $message->send_at ? $message->send_at->format('H:i') : now()->format('H:i'),
            ];

            $conversations[$otherUserId]['last_message'] = $message->content;
            $conversations[$otherUserId]['time'] = $message->send_at ? $message->send_at->diffForHumans() : 'baru';
        }

        $conversations = array_values($conversations);
        usort($conversations, function ($a, $b) {
            $timeA = $a['messages'][array_key_last($a['messages'])]['time'] ?? '';
            $timeB = $b['messages'][array_key_last($b['messages'])]['time'] ?? '';

            return $timeB <=> $timeA;
        });

        $selectedConversation = null;

        if (!empty($conversations)) {
            $selectedConversation = $conversation
                ? collect($conversations)->firstWhere('id', $conversation)
                : $conversations[0];

            if (!$selectedConversation) {
                $selectedConversation = $conversations[0];
            }
        }

        return view('messages.index', [
            'user' => $user,
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
        ]);
    }

    public function show(string $conversation)
    {
        return $this->index($conversation);
    }

    public function store(Request $request, string $conversation)
    {
        $user = Auth::user();
        $receiver = User::findOrFail($conversation);

        $message = trim((string) $request->input('message', ''));

        if ($message === '') {
            return redirect()->route('messages.index', ['conversation' => $receiver->id]);
        }

        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'content' => $message,
            'is_read' => false,
        ]);

        return redirect()->route('messages.index', ['conversation' => $receiver->id]);
    }
}
