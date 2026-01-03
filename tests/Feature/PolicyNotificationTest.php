<?php

namespace Tests\Feature;

use App\Models\Innovation;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Meeting;
use App\Models\Comment;
use App\Notifications\AdminPasswordResetRequest;
use App\Notifications\MeetingCancellation;
use App\Notifications\MeetingInvitation;
use App\Notifications\MeetingReminder;
use App\Notifications\MeetingResponse;
use App\Notifications\NewCommentAdded;
use App\Notifications\PasswordResetByAdmin;
use App\Notifications\ProjectAssigned;
use App\Notifications\ProjectDeadlineApproaching;
use App\Notifications\ProjectStatusChanged;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskDeadlineApproaching;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class PolicyNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_innovation_policy()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $docente = User::factory()->create();
        $docente->assignRole('docente');
        
        $innovation = Innovation::factory()->create(['profile_id' => $docente->profile->id]);

        $this->assertTrue($admin->can('viewAny', Innovation::class));
        $this->assertTrue($admin->can('view', $innovation));
        $this->assertTrue($admin->can('create', Innovation::class));
        $this->assertTrue($admin->can('update', $innovation));
        $this->assertTrue($admin->can('delete', $innovation));
        $this->assertTrue($admin->can('restore', $innovation));
        $this->assertTrue($admin->can('forceDelete', $innovation));

        $this->assertTrue($docente->can('view', $innovation));
        $this->assertTrue($docente->can('update', $innovation));
        
        $otherDocente = User::factory()->create();
        $otherDocente->assignRole('docente');
        $this->assertFalse($otherDocente->can('update', $innovation));
    }

    public function test_all_notifications()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);
        $task = Task::factory()->create(['project_id' => $project->id]);
        $meeting = Meeting::factory()->create(['created_by' => $user->profile->id]);
        $comment = Comment::create(['project_id' => $project->id, 'profile_id' => $user->profile->id, 'content' => 'Test']);

        $notifications = [
            new AdminPasswordResetRequest($user),
            new MeetingCancellation($meeting),
            new MeetingInvitation($meeting),
            new MeetingReminder($meeting),
            new MeetingResponse($meeting, $user, 'confirmada'),
            new NewCommentAdded($comment),
            new PasswordResetByAdmin('password'),
            new ProjectAssigned($project),
            new ProjectDeadlineApproaching($project, 5),
            new ProjectStatusChanged($project, 'planificacion', 'en_progreso'),
            new TaskAssigned($task),
            new TaskDeadlineApproaching($task),
        ];

        foreach ($notifications as $notification) {
            $this->assertNotEmpty($notification->via($user));
            $this->assertIsArray($notification->toArray($user));
            if (method_exists($notification, 'toMail')) {
                $this->assertNotNull($notification->toMail($user));
            }
        }
    }
}
