<?php

use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use function Pest\Laravel\actingAs;

it('creates a message with a uuid id', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $message = Message::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'content' => 'halo',
        'is_read' => false,
        'send_at' => now(),
    ]);

    expect($message->id)->not->toBeEmpty()
        ->and($message->sender_id)->toBe($sender->id)
        ->and($message->receiver_id)->toBe($receiver->id)
        ->and($message->content)->toBe('halo');
});

it('creates a notification when a message is sent', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    actingAs($sender)
        ->post(route('messages.store', $receiver->id), [
            'content' => 'Halo bang, ada kabar?',
        ])
        ->assertRedirect(route('messages.show', $receiver->id));

    expect(Notification::query()
        ->where('user_id', $receiver->id)
        ->where('type', 'message')
        ->exists())->toBeTrue();
});
