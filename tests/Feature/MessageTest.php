<?php

use App\Models\Message;
use App\Models\Notification;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Hobby;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

it('sends and displays a photo in a conversation', function () {
    Storage::fake('local');
    $sender = User::factory()->create();
    $receiver = User::factory()->create();
    $outsider = User::factory()->create();
    Hobby::create(['name' => 'Fotografi']);
    $club = Club::factory()->create();
    ClubMember::create(['club_id' => $club->id, 'user_id' => $sender->id, 'role' => 'member']);
    ClubMember::create(['club_id' => $club->id, 'user_id' => $receiver->id, 'role' => 'member']);

    $response = actingAs($sender)->postJson(route('messages.store', [$club->id, $receiver->id]), [
        'photo' => UploadedFile::fake()->image('photo.jpg'),
    ]);

    $response->assertOk()
        ->assertJsonPath('chat_message.content', '')
        ->assertJsonPath('chat_message.sender_id', $sender->id);

    $message = Message::query()->firstOrFail();
    expect($message->photo_path)->not->toBeNull()
        ->and($response->json('chat_message.photo_url'))->toBe(route('messages.image', [$club->id, $receiver->id, $message->id]));
    Storage::disk('local')->assertExists($message->photo_path);

    actingAs($receiver)
        ->get(route('messages.image', [$club->id, $sender->id, $message->id]))
        ->assertOk();

    actingAs($outsider)
        ->get(route('messages.image', [$club->id, $sender->id, $message->id]))
        ->assertForbidden();

    actingAs($receiver)
        ->get(route('messages.show', [$club->id, $sender->id]))
        ->assertOk()
        ->assertSee(route('messages.image', [$club->id, $sender->id, $message->id]));
});

it('allows a recipient to view a one-time photo only once', function () {
    Storage::fake('local');
    $sender = User::factory()->create();
    $receiver = User::factory()->create();
    Hobby::create(['name' => 'Fotografi']);
    $club = Club::factory()->create();
    ClubMember::create(['club_id' => $club->id, 'user_id' => $sender->id, 'role' => 'member']);
    ClubMember::create(['club_id' => $club->id, 'user_id' => $receiver->id, 'role' => 'member']);

    $response = actingAs($sender)->postJson(route('messages.store', [$club->id, $receiver->id]), [
        'photo' => UploadedFile::fake()->image('sekali-lihat.jpg'),
        'view_once' => '1',
    ]);

    $response->assertOk()
        ->assertJsonPath('chat_message.view_once', true)
        ->assertJsonPath('chat_message.photo_url', null);
    $message = Message::query()->firstOrFail();

    actingAs($sender)
        ->get(route('messages.image', [$club->id, $receiver->id, $message->id]))
        ->assertForbidden();

    actingAs($receiver)
        ->get(route('messages.show', [$club->id, $sender->id]))
        ->assertOk()
        ->assertSee('Lihat foto sekali');

    $this->get(route('messages.image', [$club->id, $sender->id, $message->id]))
        ->assertOk();
    $this->get(route('messages.show', [$club->id, $sender->id]))
        ->assertOk()
        ->assertSee('Foto sekali lihat sudah dibuka');
    $this->get(route('messages.image', [$club->id, $sender->id, $message->id]))
        ->assertStatus(410);

    expect($message->fresh()->viewed_at)->not->toBeNull();
});

it('rejects empty or non-image chat uploads', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();
    Hobby::create(['name' => 'Fotografi']);
    $club = Club::factory()->create();
    ClubMember::create(['club_id' => $club->id, 'user_id' => $sender->id, 'role' => 'member']);
    ClubMember::create(['club_id' => $club->id, 'user_id' => $receiver->id, 'role' => 'member']);

    actingAs($sender)
        ->post(route('messages.store', [$club->id, $receiver->id]))
        ->assertRedirect();

    $this->post(route('messages.store', [$club->id, $receiver->id]), [
        'photo' => UploadedFile::fake()->create('document.txt', 10, 'text/plain'),
    ])->assertRedirect();

    expect(Message::query()->where('sender_id', $sender->id)->count())->toBe(0);
});

it('lists the latest message and unread count for each conversation', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();
    Hobby::create(['name' => 'Berkebun']);
    $club = Club::factory()->create();
    ClubMember::create(['club_id' => $club->id, 'user_id' => $sender->id, 'role' => 'member']);
    ClubMember::create(['club_id' => $club->id, 'user_id' => $receiver->id, 'role' => 'member']);

    Message::create([
        'club_id' => $club->id,
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'content' => 'Pesan lama',
        'is_read' => true,
        'send_at' => now()->subMinutes(2),
    ]);
    $latestMessage = Message::create([
        'club_id' => $club->id,
        'sender_id' => $receiver->id,
        'receiver_id' => $sender->id,
        'content' => 'Pesan terbaru',
        'is_read' => false,
        'send_at' => now(),
    ]);

    $response = actingAs($sender)->get(route('messages.index'));

    $response->assertOk();
    $conversation = $response->viewData('conversations')->first();

    expect($conversation->last_message->id)->toBe($latestMessage->id)
        ->and($conversation->unread_count)->toBe(1);
});

it('shows chat actions and lets a member report the other user', function () {
    $reporter = User::factory()->create();
    $reportedUser = User::factory()->create();
    Hobby::create(['name' => 'Melukis']);
    $club = Club::factory()->create();
    ClubMember::create(['club_id' => $club->id, 'user_id' => $reporter->id, 'role' => 'member']);
    ClubMember::create(['club_id' => $club->id, 'user_id' => $reportedUser->id, 'role' => 'member']);

    actingAs($reporter)
        ->get(route('messages.show', [$club->id, $reportedUser->id]))
        ->assertOk()
        ->assertSee('Lihat Profil')
        ->assertSee('Keluar Obrolan')
        ->assertSee('Laporkan Pengguna');

    actingAs($reporter)
        ->from(route('messages.show', [$club->id, $reportedUser->id]))
        ->post(route('reports.store'), [
            'contentType' => 'user',
            'contentId' => $reportedUser->id,
            'reportedUserId' => $reportedUser->id,
            'reason' => 'Mengirim pesan yang melanggar aturan.',
        ])
        ->assertRedirect(route('messages.show', [$club->id, $reportedUser->id]))
        ->assertSessionHasNoErrors();

    expect(Report::query()
        ->where('reporter_id', $reporter->id)
        ->where('reported_user_id', $reportedUser->id)
        ->where('content_type', 'user')
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

    actingAs($sender)
        ->get(route('messages.updates', [
            'club' => $club->id,
            'conversation' => $receiver->id,
            'after' => now()->subMinute()->toIso8601String(),
        ]))
        ->assertOk()
        ->assertJsonPath('messages.0.is_read', false)
        ->assertJsonPath('read_message_ids', []);

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

    actingAs($sender)
        ->get(route('messages.updates', [
            'club' => $club->id,
            'conversation' => $receiver->id,
            'after' => now()->subMinute()->toIso8601String(),
        ]))
        ->assertOk()
        ->assertJsonPath('read_message_ids.0', $message->id);

    actingAs($sender)
        ->get(route('messages.show', [$club->id, $receiver->id]))
        ->assertOk()
        ->assertSee('data-message-status data-is-read="true"', false)
        ->assertSee('datetime="' . $message->send_at->toIso8601String() . '"', false)
        ->assertSee('text-sky-500', false);
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

    $message->update(['is_read' => true]);
    $this->patch(route('messages.update', [$club->id, $receiver->id, $message->id]), ['content' => 'Tidak boleh diedit'])
        ->assertForbidden();
    expect($message->fresh()->content)->toBe('Sudah diedit');

    $this->delete(route('messages.destroy', [$club->id, $receiver->id, $message->id]))
        ->assertRedirect();

    expect($message->fresh()->content)->toBe('Pesan telah dihapus')
        ->and($message->fresh()->deleted_at)->not->toBeNull();
});
