<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set up roles and default user if necessary, or just create a user with a profile
        $this->user = User::factory()->create();
        $this->profile = $this->user->profile;
    }

    public function test_user_can_view_projects_index()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('projects.index'));
        $response->assertStatus(200);
        $response->assertViewIs('app.back.projects.index');
    }

    public function test_user_can_create_project()
    {
        $this->actingAs($this->user);
        $category = \App\Models\Category::factory()->create();
        
        $projectData = [
            'title' => 'Test Project',
            'description' => 'Test Description',
            'objectives' => 'Test Objectives',
            'category_id' => $category->id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonth()->format('Y-m-d'),
            'status' => 'planificacion',
            'impact_description' => 'Test Impact',
            'team_members' => [$this->user->id],
        ];

        $response = $this->post(route('projects.store'), $projectData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['title' => 'Test Project']);
    }

    public function test_user_can_view_project_details()
    {
        $project = Project::factory()->create(['profile_id' => $this->profile->id]);
        
        $this->actingAs($this->user);
        $response = $this->get(route('projects.show', $project));
        
        $response->assertStatus(200);
        $response->assertViewHas('project', $project);
    }

    public function test_user_can_update_project()
    {
        $project = Project::factory()->create(['profile_id' => $this->profile->id]);
        
        $this->actingAs($this->user);
        
        $updateData = [
            'title' => 'Updated Project Title',
            'description' => $project->description,
            'category_id' => $project->category_id,
            'start_date' => $project->start_date->format('Y-m-d'),
            'end_date' => $project->end_date->format('Y-m-d'),
            'status' => 'en_progreso',
            'team_members' => [$this->user->id],
        ];

        $response = $this->put(route('projects.update', $project), $updateData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'title' => 'Updated Project Title']);
    }

    public function test_user_can_delete_project()
    {
        $project = Project::factory()->create(['profile_id' => $this->profile->id]);
        
        $this->actingAs($this->user);
        $response = $this->delete(route('projects.destroy', $project));
        
        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
