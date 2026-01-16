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
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('docente');
        $this->profile = $this->user->profile;
        $this->project = Project::factory()->create(['profile_id' => $this->profile->id]);
    }

    public function test_user_can_view_tasks_index()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('tasks.index', ['status' => 'atrasada']));
        $response->assertStatus(200);
        $response->assertViewIs('app.back.tasks.index');
    }

    public function test_user_can_view_task_create_form()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('tasks.create'));
        $response->assertStatus(200);
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

    public function test_task_creation_validation_failures()
    {
        $this->actingAs($this->user);
        
        // Test date out of range
        $response = $this->post(route('tasks.store'), [
            'project_id' => $this->project->id,
            'title' => 'Invalid Date Task',
            'description' => 'Test',
            'assignees' => [$this->user->id],
            'due_date' => now()->addYears(1)->format('Y-m-d'), // Far into the future
            'priority' => 'media',
        ]);
        $response->assertSessionHasErrors('due_date');

        // Test non-team member assignment
        $project = Project::factory()->create(); // Different creator, empty team
        $response = $this->post(route('tasks.store'), [
            'project_id' => $project->id,
            'title' => 'Invalid Assignee Task',
            'description' => 'Test',
            'assignees' => [$this->user->id],
            'due_date' => now()->addDays(1)->format('Y-m-d'),
            'priority' => 'media',
        ]);
        $response->assertSessionHas('swal_error');
    }

    public function test_task_creation_with_attachments()
    {
        $this->actingAs($this->user);
        \Illuminate\Support\Facades\Storage::fake('public');
        
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->post(route('tasks.store'), [
            'project_id' => $this->project->id,
            'title' => 'Task with File',
            'description' => 'Test Description',
            'assignees' => [$this->user->id],
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'priority' => 'media',
            'attachments' => [$file],
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', ['title' => 'Task with File']);
        $task = Task::where('title', 'Task with File')->first();
        $this->assertEquals(1, $task->attachments->count());
    }

    public function test_user_can_view_task_edit_form()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'assigned_to' => $this->profile->id
        ]);
        
        $this->actingAs($this->user);
        $response = $this->get(route('tasks.edit', $task));
        $response->assertStatus(200);
    }

    public function test_user_can_update_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'assigned_to' => $this->profile->id
        ]);
        
        $this->actingAs($this->user);
        
        $response = $this->put(route('tasks.update', $task), [
            'project_id' => $this->project->id,
            'title' => 'Updated Task Title',
            'description' => 'Updated Description',
            'assignees' => [$this->user->id],
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'en_progreso',
            'priority' => 'alta',
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', ['title' => 'Updated Task Title', 'status' => 'en_progreso']);
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
