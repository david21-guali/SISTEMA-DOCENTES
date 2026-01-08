<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
        $this->profile = $this->user->profile;
    }

    public function test_user_can_view_projects_index()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('projects.index'));
        $response->assertStatus(200);
        $response->assertViewIs('app.back.projects.index');
    }

    public function test_project_index_filtering_for_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $this->actingAs($admin);
        $response = $this->get(route('projects.index'));
        $response->assertStatus(200);
        $response->assertViewHas('projects');
    }

    public function test_project_store_with_attachments()
    {
        $this->actingAs($this->user);
        Storage::fake('public');
        $category = \App\Models\Category::factory()->create();
        
        $file = UploadedFile::fake()->create('project.pdf', 200);

        $projectData = [
            'title' => 'Project with Attachments',
            'description' => 'Test',
            'objectives' => 'Test',
            'category_id' => $category->id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonth()->format('Y-m-d'),
            'impact_description' => 'Test',
            'team_members' => [$this->user->id],
            'attachments' => [$file],
        ];

        $response = $this->post(route('projects.store'), $projectData);
        $response->assertRedirect();
        
        $project = Project::where('title', 'Project with Attachments')->first();
        $this->assertEquals(1, $project->attachments->count());
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

    public function test_upload_final_report()
    {
        $this->actingAs($this->user);
        \Illuminate\Support\Facades\Storage::fake('public');
        
        $project = Project::factory()->create(['profile_id' => $this->profile->id]);
        $file = \Illuminate\Http\UploadedFile::fake()->create('report.pdf', 500);

        $response = $this->post(route('projects.uploadReport', $project), [
            'file' => $file,
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('resources', ['name' => 'Informe Final - ' . $project->title]);
    }

    public function test_unauthorized_report_upload()
    {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);
        
        $project = Project::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->create('hacker_report.pdf', 500);

        $response = $this->post(route('projects.uploadReport', $project), [
            'file' => $file,
        ]);
        
        $response->assertStatus(403);
    }
}
