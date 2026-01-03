<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_view_chat_index()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $response = $this->actingAs($user)
            ->get(route('chat.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    public function test_user_can_send_message()
    {
        $user1 = User::factory()->create();
        $user1->assignRole('docente');
        $user2 = User::factory()->create();
        $user2->assignRole('docente');

        $response = $this->actingAs($user1)
            ->post(route('chat.store', $user2), [
                'content' => 'Hello teacher!'
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $user1->profile->id,
            'receiver_id' => $user2->profile->id,
            'content' => 'Hello teacher!'
        ]);
    }

    public function test_user_can_view_conversation()
    {
        $user1 = User::factory()->create();
        $user1->assignRole('docente');
        $user2 = User::factory()->create();
        $user2->assignRole('docente');

        Message::create([
            'sender_id' => $user2->profile->id,
            'receiver_id' => $user1->profile->id,
            'content' => 'Hi there',
        ]);

        $response = $this->actingAs($user1)
            ->get(route('chat.show', $user2));

        $response->assertStatus(200);
        $response->assertViewHas('messages');
        $this->assertNotNull(Message::first()->read_at);
    }
}
