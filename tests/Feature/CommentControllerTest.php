<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->profile = $this->user->profile;
        $this->project = Project::factory()->create(['profile_id' => $this->profile->id]);
    }

    public function test_user_can_store_comment()
    {
        $this->actingAs($this->user);
        
        $commentData = [
            'content' => 'Test Comment',
            'commentable_id' => $this->project->id,
            'commentable_type' => 'project',
        ];

        $response = $this->post(route('comments.store'), $commentData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('comments', ['content' => 'Test Comment']);
    }

    public function test_user_can_delete_own_comment()
    {
        $comment = Comment::create([
            'content' => 'Test Comment',
            'commentable_id' => $this->project->id,
            'commentable_type' => Project::class,
            'profile_id' => $this->profile->id,
        ]);
        
        $this->actingAs($this->user);
        $response = $this->delete(route('comments.destroy', $comment));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
