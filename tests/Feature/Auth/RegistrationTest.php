<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $this->post('/register/step-1', [
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('register', ['step' => 2]));

    $this->post('/register/step-2', [
        'name' => 'Test User',
        'bio' => 'This is a test user.',
    ])->assertRedirect(route('register', ['step' => 3]));

    $response = $this->post('/register/step-3', [
        'hobbies' => [],
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('selected hobbies are persisted to the user profile after registration', function () {
    $this->post('/register/step-1', [
        'email' => 'hobby-user@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('register', ['step' => 2]));

    $this->post('/register/step-2', [
        'name' => 'Hobby User',
        'bio' => 'Likes collecting hobbies.',
    ])->assertRedirect(route('register', ['step' => 3]));

    $this->post('/register/step-3', [
        'hobbies' => ['Photography', 'Gaming'],
    ])->assertRedirect(route('dashboard', absolute: false));

    $user = auth()->user();

    expect($user->interests)->toContain('Photography')
        ->and($user->interests)->toContain('Gaming');
});

test('user avatar accessor falls back to null when no avatar is set', function () {
    $user = \App\Models\User::factory()->create([
        'avatar_url' => null,
    ]);

    expect($user->avatar_full_url)->toBeNull();
});

test('uploaded profile image is saved to the user and accessible', function () {
    Storage::fake('public');

    $this->post('/register/step-1', [
        'email' => 'avatar-user@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('register', ['step' => 2]));

    $file = UploadedFile::fake()->image('avatar.png', 400, 400);

    $this->post('/register/step-2', [
        'name' => 'Avatar User',
        'bio' => 'User with uploaded photo.',
        'avatar_url' => $file,
    ])->assertRedirect(route('register', ['step' => 3]));

    $user = auth()->user();

    expect($user)->not->toBeNull()
        ->and($user->avatar_url)->not->toBeNull()
        ->and($user->avatar_full_url)->toContain('/storage/avatars/');
});
