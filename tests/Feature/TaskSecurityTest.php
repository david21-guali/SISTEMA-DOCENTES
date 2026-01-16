<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class TaskSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_cannot_view_others_task(): void
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $otherUser = User::factory()->create();
        $otherUser->assignRole('docente');

        $project = Project::factory()->create(['profile_id' => $otherUser->profile->id]);
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($user)->get(route('tasks.show', $task));
        $response->assertForbidden();
    }

    public function test_assigned_user_can_view_task(): void
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);
        $task->assignees()->attach($user->profile->id);

        $response = $this->actingAs($user)->get(route('tasks.show', $task));
        $response->assertStatus(200);
    }

    public function test_user_cannot_edit_others_task(): void
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        // Intentar ver form de edición
        $response = $this->actingAs($user)->get(route('tasks.edit', $task));
        $response->assertForbidden();

        // Intentar actualizar
        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Hackeado',
            'assignees' => [$user->id]
        ]);
        $response->assertForbidden();
    }

    public function test_project_owner_can_edit_task(): void
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $project = Project::factory()->create(['profile_id' => $user->profile->id]);
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($user)->get(route('tasks.edit', $task));
        $response->assertStatus(200);
    }

    public function test_assigned_user_can_complete_task(): void
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id, 'status' => 'pendiente']);
        $task->assignees()->attach($user->profile->id);

        $response = $this->actingAs($user)->patch(route('tasks.complete', $task));
        $response->assertRedirect();
        $this->freshTaskStatus = $task->fresh()->status;
        $this->assertEquals('completada', $this->freshTaskStatus);
    }
}
