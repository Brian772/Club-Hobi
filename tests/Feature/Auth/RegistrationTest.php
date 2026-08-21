<?php

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
