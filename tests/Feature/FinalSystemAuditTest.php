<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Comment;
use App\Models\Innovation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskStatusChanged;
use App\Notifications\NewCommentAdded;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class FinalSystemAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_task_observer_notifies_team_on_status_change(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $user->assignRole('docente');

        $project = Project::factory()->create();
        $project->team()->attach($user->profile->id);
        
        $task = Task::factory()->create(['project_id' => $project->id, 'status' => 'pendiente']);
        
        $task->status = 'en_progreso';
        $task->save();
        
        Notification::assertSentTo($user, TaskStatusChanged::class);
    }

    public function test_comment_observer_notifies_owner(): void
    {
        Notification::fake();
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        
        $innovation = Innovation::factory()->create(['profile_id' => $owner->profile->id]);
        
        Comment::factory()->create([
            'profile_id' => $viewer->profile->id,
            'commentable_type' => Innovation::class,
            'commentable_id' => $innovation->id,
            'content' => 'Test comment'
        ]);
        
        Notification::assertSentTo($owner, NewCommentAdded::class);
    }

    public function test_project_final_report_upload_secutity(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        
        $project = Project::factory()->create(['profile_id' => $owner->profile->id]);
        $file = UploadedFile::fake()->create('report.pdf', 500);

        // Attacker cannot upload
        $this->actingAs($attacker)
            ->post(route('projects.uploadReport', $project), ['file' => $file])
            ->assertForbidden();

        // Owner can upload
        $this->actingAs($owner)
            ->post(route('projects.uploadReport', $project), ['file' => $file])
            ->assertRedirect();
            
        $this->assertNotNull($project->fresh()->final_report);
    }

    public function test_task_saved_recalculates_project_progress(): void
    {
        $project = Project::factory()->create();
        $task1 = Task::factory()->create(['project_id' => $project->id, 'status' => 'pendiente']);
        $task2 = Task::factory()->create(['project_id' => $project->id, 'status' => 'completada']);
        
        // El observer de Task llama a recalculateProgress en 'saved'
        $task1->save(); 
        
        $this->assertEquals(50, $project->fresh()->completion_percentage);
        
        $task1->status = 'completada';
        $task1->save();
        
        $this->assertEquals(100, $project->fresh()->completion_percentage);
    }
}
