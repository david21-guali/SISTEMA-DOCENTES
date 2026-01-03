<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Innovation;
use App\Models\Meeting;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_dashboard_is_accessible_and_shows_data()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        // Create some data for graphs and stats
        $category = Category::factory()->create(['name' => 'Research']);
        Project::factory()->count(3)->create([
           'category_id' => $category->id,
           'profile_id' => $user->profile->id
        ]);
        
        Task::factory()->count(2)->create([
           'project_id' => Project::first()->id,
        ]);

        Innovation::factory()->count(1)->create([
            'profile_id' => $user->profile->id
        ]);

        Meeting::factory()->create([
            'created_by' => $user->profile->id
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('app.back.dashboard');
        $response->assertViewHasAll([
            'stats',
            'projectsByCategory',
            'projectsByMonth',
            'recentProjects',
            'taskStats',
            'activityTimeline'
        ]);
    }
}
