<?php

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Hobby;
use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('another member profile shows shared profile details without exposing email', function () {
    $viewer = User::factory()->create();
    $member = User::factory()->create([
        'name' => 'Rani Komunitas',
        'email' => 'rani@example.com',
        'bio' => 'Senang berbagi ide kreatif.',
        'interests' => 'Fotografi, Membaca',
    ]);

    $response = $this
        ->actingAs($viewer)
        ->get(route('profile.show', $member));

    $response->assertOk()
        ->assertSee('Rani Komunitas')
        ->assertSee('Senang berbagi ide kreatif.')
        ->assertSee('Fotografi')
        ->assertSee('Membaca')
        ->assertDontSee('rani@example.com');
});

test('profile links back to the shared chat and lists community membership', function () {
    $viewer = User::factory()->create();
    $member = User::factory()->create();
    $hobby = Hobby::create(['name' => 'Fotografi']);
    $club = Club::factory()->create([
        'name' => 'Klub Fotografi',
        'hobby_id' => $hobby->id,
        'created_by' => $viewer->id,
    ]);

    ClubMember::create(['club_id' => $club->id, 'user_id' => $viewer->id]);
    ClubMember::create(['club_id' => $club->id, 'user_id' => $member->id]);

    $response = $this
        ->actingAs($viewer)
        ->get(route('profile.show', $member));

    $response->assertOk()
        ->assertSee('Klub Fotografi')
        ->assertSee('Lanjutkan obrolan')
        ->assertSee(route('messages.show', [$club->id, $member->id]));
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
