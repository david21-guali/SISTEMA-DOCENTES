<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_forum_post_relations()
    {
        $user = User::factory()->create();
        $topic = ForumTopic::factory()->create(['profile_id' => $user->profile->id]);
        
        $post = ForumPost::create([
            'topic_id' => $topic->id,
            'profile_id' => $user->profile->id,
            'content' => 'Test post'
        ]);

        $this->assertEquals($topic->id, $post->topic->id);
        $this->assertEquals($user->profile->id, $post->profile->id);
    }

    public function test_message_relations()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        
        $message = Message::create([
            'sender_id' => $sender->profile->id,
            'receiver_id' => $receiver->profile->id,
            'content' => 'Hello',
            'read_at' => null
        ]);

        $this->assertEquals($sender->profile->id, $message->sender_id);
        $this->assertNull($message->read_at);
    }
}
