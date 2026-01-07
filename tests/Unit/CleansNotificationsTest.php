<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class CleansNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_model_cleans_notifications(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['title' => 'Sample Task']);

        // Create a fake notification pointing to this task
        $notification = DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TaskAssigned',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['task_id' => $task->id, 'message' => 'New Task'],
        ]);

        $this->assertDatabaseHas('notifications', ['id' => $notification->id]);

        // Delete the task, it should trigger the trait
        $task->delete();

        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }
}
