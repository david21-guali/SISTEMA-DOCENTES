<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_view_any_project()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->can('viewAny', Project::class));
    }

    public function test_teacher_can_view_own_project()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);

        $this->assertTrue($user->can('view', $project));
    }

    public function test_teacher_cannot_view_others_project()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['profile_id' => $otherUser->profile->id]);

        $this->assertFalse($user->can('view', $project));
    }

    public function test_coordinator_can_view_others_project()
    {
        $user = User::factory()->create();
        $user->assignRole('coordinador');
        
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['profile_id' => $otherUser->profile->id]);

        $this->assertTrue($user->can('view', $project));
    }

    public function test_teacher_can_update_own_project()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);

        $this->assertTrue($user->can('update', $project));
    }

    public function test_teacher_cannot_update_others_project()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['profile_id' => $otherUser->profile->id]);

        $this->assertFalse($user->can('update', $project));
    }

    public function test_teacher_cannot_delete_project_unless_admin()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);

        $this->assertFalse($user->can('delete', $project));
    }

    public function test_admin_can_delete_project()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $project = Project::factory()->create();

        $this->assertTrue($user->can('delete', $project));
    }

    public function test_upload_final_report_policy()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);

        // Assuming basic teacher can upload report for own project
        $this->assertTrue($user->can('uploadFinalReport', $project));
        
        $otherUser = User::factory()->create();
        $otherUser->assignRole('docente');
        $this->assertFalse($otherUser->can('uploadFinalReport', $project));
    }
}
