<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjectsExport;
use App\Exports\TasksExport;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_export_projects_excel()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        Project::factory()->create(['profile_id' => $user->profile->id]);

        $response = $this->actingAs($user)
            ->get(route('reports.projects.excel'));

        $response->assertStatus(200);
        $this->assertTrue(str_contains(strtolower($response->headers->get('Content-Disposition')), 'xlsx'));
    }

    public function test_user_can_export_tasks_excel()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        Task::factory()->create(['assigned_to' => $user->profile->id]);

        $response = $this->actingAs($user)
            ->get(route('reports.tasks.excel'));

        $response->assertStatus(200);
        $this->assertTrue(str_contains(strtolower($response->headers->get('Content-Disposition')), 'xlsx'));
    }
}
