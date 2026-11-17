<?php

use App\Models\Message;
use App\Models\Notification;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Hobby;
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
    Hobby::create(['name' => 'Fotografi']);
    $club = Club::factory()->create();
    ClubMember::create(['club_id' => $club->id, 'user_id' => $sender->id, 'role' => 'member']);
    ClubMember::create(['club_id' => $club->id, 'user_id' => $receiver->id, 'role' => 'member']);

    actingAs($sender)
        ->post(route('messages.store', [$club->id, $receiver->id]), [
            'content' => 'Halo bang, ada kabar?',
        ])
        ->assertRedirect(route('messages.show', [$club->id, $receiver->id]));

    expect(Notification::query()
        ->where('user_id', $receiver->id)
        ->where('type', 'message')
        ->exists())->toBeTrue();
});

it('marks all of the authenticated users notifications as read', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $unreadNotification = Notification::create([
        'user_id' => $user->id,
        'title' => 'Komentar baru',
        'content' => 'Ada komentar baru di postinganmu.',
        'type' => 'comment',
        'is_read' => false,
    ]);
    $otherUsersNotification = Notification::create([
        'user_id' => $otherUser->id,
        'title' => 'Pesan baru',
        'content' => 'Pesan pribadi.',
        'type' => 'message',
        'is_read' => false,
    ]);

    actingAs($user)
        ->patch(route('notifications.read-all'))
        ->assertRedirect();

    expect($unreadNotification->fresh()->is_read)->toBeTrue()
        ->and($otherUsersNotification->fresh()->is_read)->toBeFalse();
});

it('tracks and returns online presence for chat users', function () {
    $user = User::factory()->create();
    $offlineUser = User::factory()->create([
        'last_seen_at' => now()->subMinutes(5),
    ]);

    actingAs($user)
        ->post(route('presence.heartbeat'))
        ->assertNoContent();

    $this->get(route('presence.index', ['ids' => [$user->id, $offlineUser->id]]))
        ->assertOk()
        ->assertJsonPath('users.' . $user->id . '.online', true)
        ->assertJsonPath('users.' . $offlineUser->id . '.online', false);
});

it('shows notifications from every supported type', function () {
    $user = User::factory()->create();

    foreach (['message', 'report', 'account_status', 'comment', 'other'] as $type) {
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Notifikasi ' . $type,
            'content' => 'Aktivitas ' . $type,
            'type' => $type,
            'is_read' => false,
        ]);
    }

    actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Notifikasi message')
        ->assertSee('Notifikasi report')
        ->assertSee('Notifikasi account_status')
        ->assertSee('Notifikasi comment')
        ->assertSee('Notifikasi other');

    $response = $this->get(route('notifications.updates'))
        ->assertOk()
            ->assertJsonPath('unread_count', 5);

    expect($response->json('html'))->toBeString()
        ->and($response->json('page_html'))->toBeString()
        ->and($response->json('page_html'))->toContain('Notifikasi account_status');
});

it('returns new messages and marks their message notifications as read', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();
    Hobby::create(['name' => 'Membaca']);
    $club = Club::factory()->create();
    ClubMember::create(['club_id' => $club->id, 'user_id' => $sender->id, 'role' => 'member']);
    ClubMember::create(['club_id' => $club->id, 'user_id' => $receiver->id, 'role' => 'member']);
    $message = Message::create([
        'club_id' => $club->id,
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'content' => 'Pesan real-time',
        'is_read' => false,
        'send_at' => now(),
    ]);
    $notification = Notification::create([
        'user_id' => $receiver->id,
        'title' => 'Pesan baru',
        'content' => 'Pesan masuk',
        'type' => 'message',
        'source_id' => $message->id,
        'is_read' => false,
    ]);

    actingAs($receiver)
        ->get(route('messages.updates', [
            'club' => $club->id,
            'conversation' => $sender->id,
            'after' => now()->subMinute()->toIso8601String(),
        ]))
        ->assertOk()
        ->assertJsonPath('messages.0.id', $message->id)
        ->assertJsonPath('messages.0.content', 'Pesan real-time');

    expect($message->fresh()->is_read)->toBeTrue()
        ->and($notification->fresh()->is_read)->toBeTrue();
});

it('rejects messages outside a shared club and supports editing and deleting own messages', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();
    $outsider = User::factory()->create();
    Hobby::create(['name' => 'Bersepeda']);
    $club = Club::factory()->create();
    ClubMember::create(['club_id' => $club->id, 'user_id' => $sender->id, 'role' => 'member']);
    ClubMember::create(['club_id' => $club->id, 'user_id' => $receiver->id, 'role' => 'member']);

    actingAs($sender)
        ->post(route('messages.store', [$club->id, $outsider->id]), ['content' => 'Tidak boleh'])
        ->assertForbidden();

    $message = Message::create([
        'club_id' => $club->id,
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'content' => 'Versi awal',
        'is_read' => false,
        'send_at' => now(),
    ]);

    actingAs($sender)
        ->patch(route('messages.update', [$club->id, $receiver->id, $message->id]), ['content' => 'Sudah diedit'])
        ->assertRedirect();

    expect($message->fresh()->content)->toBe('Sudah diedit')
        ->and($message->fresh()->edited_at)->not->toBeNull();

    $this->delete(route('messages.destroy', [$club->id, $receiver->id, $message->id]))
        ->assertRedirect();

    expect($message->fresh()->content)->toBe('Pesan telah dihapus')
        ->and($message->fresh()->deleted_at)->not->toBeNull();
});
