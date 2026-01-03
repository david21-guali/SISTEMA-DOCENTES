<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Innovation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class AdvancedReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_view_participation_report()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $response = $this->actingAs($user)
            ->get(route('reports.participation'));

        $response->assertStatus(200);
        $response->assertViewIs('app.back.reports.participation');
    }

    public function test_user_can_view_comparative_report()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        // Create some data for periods
        Project::factory()->create(['created_at' => now()->subMonth()]);
        Project::factory()->create(['created_at' => now()]);

        $response = $this->actingAs($user)
            ->get(route('reports.comparative'));

        $response->assertStatus(200);
        $response->assertViewHasAll(['period1Stats', 'period2Stats', 'changes']);
    }

    public function test_user_can_export_innovations_excel()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $response = $this->actingAs($user)
            ->get(route('reports.innovations.excel'));

        $response->assertStatus(200);
        $contentDisposition = strtolower($response->headers->get('Content-Disposition'));
        $this->assertTrue(
            str_contains($contentDisposition, 'innovaciones') || 
            str_contains($contentDisposition, 'excel')
        );
    }
}
