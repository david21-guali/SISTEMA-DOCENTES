<?php

namespace Tests\Unit;

use App\Models\Innovation;
use App\Models\Meeting;
use App\Models\Profile;
use App\Models\User;
use App\Models\Project;
use App\Models\Comment;
use App\Models\Evaluation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_innovation_color_helper()
    {
        $innovation = new Innovation(['status' => 'completada']);
        $this->assertEquals('success', $innovation->status_color);

        $innovation->status = 'en_revision';
        $this->assertEquals('warning', $innovation->status_color);
    }

    public function test_meeting_date_helpers()
    {
        $meeting = new Meeting([
            'meeting_date' => now()->addDay(),
            'start_time' => '10:00',
            'end_time' => '11:00'
        ]);

        $this->assertFalse($meeting->is_past);
        $this->assertNotNull($meeting->formatted_date);
    }

    public function test_comment_scopes()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);
        
        $comment = \App\Models\Comment::create([
            'project_id' => $project->id,
            'profile_id' => $user->profile->id,
            'content' => 'Top level'
        ]);

        \App\Models\Comment::create([
            'project_id' => $project->id,
            'profile_id' => $user->profile->id,
            'content' => 'Reply',
            'parent_id' => $comment->id
        ]);

        $this->assertEquals(1, \App\Models\Comment::topLevel()->count());
    }

    public function test_evaluation_helpers()
    {
        $evaluation = new \App\Models\Evaluation([
            'innovation_score' => 4,
            'relevance_score' => 5,
            'final_score' => 9.0
        ]);

        $this->assertEquals(4.5, $evaluation->average_rubric_score);
        $this->assertEquals('success', $evaluation->score_color);
    }

    public function test_profile_attributes()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $profile = $user->profile;
        $this->assertEquals('John Doe', $profile->name);
        
        $project = Project::factory()->create(['profile_id' => $profile->id]);
        $this->assertEquals(1, $profile->projects()->count());
    }
}
