<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Evaluation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class EvaluationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_coordinator_can_edit_evaluation()
    {
        $user = User::factory()->create();
        $user->assignRole('coordinador');
        $evaluation = Evaluation::factory()->create(['evaluator_id' => $user->profile->id]);

        $response = $this->actingAs($user)
            ->get(route('evaluations.edit', $evaluation));

        $response->assertStatus(200);
        $response->assertViewIs('evaluations.edit');
    }

    public function test_admin_cannot_remove_last_admin_role()
    {
        // Ensure only one admin exists
        User::role('admin')->get()->each(function($u) {
            $u->removeRole('admin');
        });

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $this->actingAs($admin);
        
        // Attempt to remove the admin role from the only admin
        $response = $this->postJson(route('users.updateRole', $admin), [
            'role' => 'docente'
        ]);

        // Should be forbidden or have an error
        $response->assertStatus(403);
    }

    public function test_coordinator_can_update_evaluation()
    {
        $user = User::factory()->create();
        $user->assignRole('coordinador');
        $evaluation = Evaluation::factory()->create(['evaluator_id' => $user->profile->id]);

        $response = $this->actingAs($user)
            ->put(route('evaluations.update', $evaluation), [
                'innovation_score' => 5,
                'relevance_score' => 5,
                'results_score' => 5,
                'impact_score' => 5,
                'methodology_score' => 5,
                'final_score' => 10,
                'status' => 'finalizada',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('evaluations', [
            'id' => $evaluation->id,
            'final_score' => 10,
            'status' => 'finalizada'
        ]);
    }

    public function test_coordinator_can_delete_evaluation()
    {
        $user = User::factory()->create();
        $user->assignRole('coordinador');
        $evaluation = Evaluation::factory()->create(['evaluator_id' => $user->profile->id]);

        $response = $this->actingAs($user)
            ->delete(route('evaluations.destroy', $evaluation));

        $response->assertRedirect();
        $this->assertDatabaseMissing('evaluations', ['id' => $evaluation->id]);
    }

    // test_fix_storage_link_route removed as the route/method no longer exists
}
