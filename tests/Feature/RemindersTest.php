<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Meeting;
use App\Models\Profile;
use App\Notifications\ProjectDeadlineApproaching;
use App\Notifications\TaskDeadlineApproaching;
use App\Notifications\MeetingReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_reminders_command()
    {
        Notification::fake();
        
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'profile_id' => $user->profile->id,
            'end_date' => now()->addDays(5),
            'status' => 'en_progreso'
        ]);

        Artisan::call('projects:send-reminders', ['--days' => 7]);

        Notification::assertSentTo(
            $user,
            ProjectDeadlineApproaching::class,
            function ($notification, $channels) use ($project) {
                return $notification->project->id === $project->id;
            }
        );
    }

    public function test_task_reminders_command()
    {
        Notification::fake();
        
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'assigned_to' => $user->profile->id,
            'due_date' => now()->addDays(1),
            'status' => 'en_progreso'
        ]);

        Artisan::call('tasks:send-reminders', ['--days' => 2]);

        Notification::assertSentTo(
            $user,
            TaskDeadlineApproaching::class,
            function ($notification, $channels) use ($task) {
                return $notification->task->id === $task->id;
            }
        );
    }

    public function test_meeting_reminders_command()
    {
        Notification::fake();
        
        $creator = User::factory()->create();
        $participantUser = User::factory()->create();
        
        $meeting = Meeting::factory()->create([
            'created_by' => $creator->profile->id,
            'meeting_date' => now()->addHours(5),
            'status' => 'pendiente'
        ]);

        $meeting->participants()->attach($participantUser->profile->id, ['attendance' => 'pendiente']);

        Artisan::call('meetings:send-reminders', ['--hours' => 10]);

        Notification::assertSentTo(
            $participantUser->profile,
            MeetingReminder::class
        );
    }
}
