<?php

use App\Models\User;

it('user can open message page and send a message to another user', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $this->actingAs($sender);

    $this->get('/messages/' . $receiver->id)
        ->assertOk();

    $this->post('/messages/' . $receiver->id, [
        'message' => 'Halo, mau gabung diskusi minggu depan?',
    ])->assertRedirect('/messages/' . $receiver->id);

    $this->assertDatabaseHas('messages', [
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'content' => 'Halo, mau gabung diskusi minggu depan?',
    ]);
});
