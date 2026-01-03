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
        $this->user = User::factory()->create();
        $this->profile = Profile::factory()->create(['user_id' => $this->user->id]);
        $this->project = Project::factory()->create(['profile_id' => $this->profile->id]);
    }

    public function test_user_can_store_comment()
    {
        $this->actingAs($this->user);
        
        $commentData = [
            'content' => 'Test Comment',
        ];

        $response = $this->post(route('comments.store', $this->project), $commentData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('comments', ['content' => 'Test Comment']);
    }

    public function test_user_can_delete_own_comment()
    {
        $comment = Comment::create([
            'content' => 'Test Comment',
            'project_id' => $this->project->id,
            'profile_id' => $this->profile->id,
        ]);
        
        $this->actingAs($this->user);
        $response = $this->delete(route('comments.destroy', $comment));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
