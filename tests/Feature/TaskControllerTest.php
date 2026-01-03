<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->profile = $this->user->profile;
        $this->project = Project::factory()->create(['profile_id' => $this->profile->id]);
    }

    public function test_user_can_view_tasks_index()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('tasks.index'));
        $response->assertStatus(200);
        $response->assertViewIs('app.back.tasks.index');
    }

    public function test_user_can_create_task()
    {
        $this->actingAs($this->user);
        
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'project_id' => $this->project->id,
            'assignees' => [$this->user->id],
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'priority' => 'media',
        ];

        $response = $this->post(route('tasks.store'), $taskData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }

    public function test_user_can_view_task_details()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'assigned_to' => $this->profile->id
        ]);
        
        $this->actingAs($this->user);
        $response = $this->get(route('tasks.show', $task));
        
        $response->assertStatus(200);
        $response->assertViewHas('task', $task);
    }

    public function test_user_can_update_task_status()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'assigned_to' => $this->profile->id,
            'status' => 'pendiente'
        ]);
        
        $this->actingAs($this->user);
        
        $response = $this->patch(route('tasks.complete', $task));
        
        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'completada']);
    }

    public function test_user_can_delete_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'assigned_to' => $this->profile->id
        ]);
        
        $this->actingAs($this->user);
        $response = $this->delete(route('tasks.destroy', $task));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
