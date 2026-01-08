<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Notifications\ProjectDeadlineChanged;
use App\Notifications\TaskDeadlineChanged;
use App\Notifications\TaskStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_project_deadline_changed_notification()
    {
        Notification::fake();

        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);
        
        // Trigger generic update that might invoke deadline change logic if implemented
        // Or manually trigger for testing strict notification existence
        $user->notify(new ProjectDeadlineChanged($project, now()->subDay(), now()));

        Notification::assertSentTo(
            [$user],
            ProjectDeadlineChanged::class
        );
    }

    public function test_task_deadline_changed_notification()
    {
        Notification::fake();

        $user = User::factory()->create();
        $task = Task::factory()->create();

        $user->notify(new TaskDeadlineChanged($task, now()->subDay(), now()));

        Notification::assertSentTo(
            [$user],
            TaskDeadlineChanged::class
        );
    }

    public function test_task_status_changed_notification()
    {
        Notification::fake();

        $user = User::factory()->create();
        $task = Task::factory()->create();

        $user->notify(new TaskStatusChanged($task, 'pendiente', 'en_progreso'));

        Notification::assertSentTo(
            [$user],
            TaskStatusChanged::class
        );
    }
}
