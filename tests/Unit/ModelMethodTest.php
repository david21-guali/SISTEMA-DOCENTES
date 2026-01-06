<?php

namespace Tests\Unit;

use App\Models\Innovation;
use App\Models\Meeting;
use App\Models\Profile;
use App\Models\User;
use App\Models\Project;
use App\Models\Comment;
use App\Models\Evaluation;
use App\Models\InnovationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_innovation_color_helper()
    {
        $innovation = new Innovation(['status' => 'completada']);
        $this->assertEquals('primary', $innovation->status_color);

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

    public function test_attachment_helpers()
    {
        $attachment = new \App\Models\Attachment([
            'filename' => 'test.pdf',
            'original_name' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024 * 1024
        ]);

        $this->assertTrue($attachment->isPdf());
        $this->assertFalse($attachment->isImage());
        $this->assertEquals('1 MB', $attachment->human_size);
        $this->assertEquals('fas fa-file-pdf text-danger', $attachment->icon);
    }

    public function test_comment_relations()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);
        
        $comment = Comment::create([
            'project_id' => $project->id,
            'profile_id' => $user->profile->id,
            'content' => 'Top level'
        ]);

        $this->assertEquals($project->id, $comment->project->id);
        $this->assertEquals($user->profile->id, $comment->profile->id);
        $this->assertNull($comment->parent);
        $this->assertEquals(0, $comment->replies->count());
    }

    public function test_profile_more_relations()
    {
        $user = User::factory()->create();
        $profile = $user->profile;
        
        $innovation = Innovation::factory()->create(['profile_id' => $profile->id]);
        $this->assertEquals(1, $profile->innovations->count());
        
        $meeting = Meeting::factory()->create(['created_by' => $profile->id]);
        $this->assertEquals(1, $profile->createdMeetings->count());
        
        $profile->meetings()->attach($meeting->id, ['attendance' => 'confirmada']);
        $this->assertEquals(1, $profile->meetings->count());
    }

    public function test_message_and_innovation_type_relations()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        
        $message = \App\Models\Message::create([
            'sender_id' => $sender->profile->id,
            'receiver_id' => $receiver->profile->id,
            'content' => 'Hi',
        ]);

        $this->assertEquals($sender->profile->id, $message->sender->id);
        $this->assertEquals($receiver->profile->id, $message->receiver->id);

        $type = InnovationType::factory()->create();
        Innovation::factory()->create(['innovation_type_id' => $type->id]);
        $this->assertEquals(1, $type->innovations->count());
    }
}
