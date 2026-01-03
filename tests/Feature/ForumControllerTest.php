<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class ForumControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_view_forum_index()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $response = $this->actingAs($user)
            ->get(route('forum.index'));

        $response->assertStatus(200);
        $response->assertViewHas('topics');
    }

    public function test_user_can_create_topic()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $response = $this->actingAs($user)
            ->post(route('forum.store'), [
                'title' => 'New Discussion',
                'description' => 'Let\'s talk about ethics'
            ]);

        $response->assertRedirect(route('forum.index'));
        $this->assertDatabaseHas('forum_topics', ['title' => 'New Discussion']);
    }

    public function test_user_can_post_reply()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        $topic = ForumTopic::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('forum.storePost', $topic), [
                'content' => 'I agree with this topic.'
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('forum_posts', [
            'topic_id' => $topic->id,
            'content' => 'I agree with this topic.'
        ]);
    }

    public function test_owner_can_delete_topic()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        $topic = ForumTopic::factory()->create([
            'profile_id' => $user->profile->id
        ]);

        $response = $this->actingAs($user)
            ->delete(route('forum.destroy', $topic));

        $response->assertRedirect(route('forum.index'));
        $this->assertDatabaseMissing('forum_topics', ['id' => $topic->id]);
    }
}
