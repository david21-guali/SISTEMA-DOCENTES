<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Profile;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EvaluationTest extends TestCase
{
    use RefreshDatabase;

    public function test_coordinator_can_create_evaluation(): void
    {
        // 1. Arrange: Crear usuario coordinador y roles
        $this->seed(\Database\Seeders\RolePermissionSeeder::class); // Asegurar Roles
        
        $coordinator = User::factory()->create();
        $coordinator->assignRole('coordinador');
        $this->actingAs($coordinator);
        
        $category = Category::create(['name' => 'General', 'color' => '#000']);
        $project = Project::create([
            'title' => 'Project to Evaluate',
            'description' => 'Desc',
            'objectives' => 'Obj',
            'impact_description' => 'Impact',
            'category_id' => $category->id,
            'status' => 'en_progreso',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'profile_id' => $coordinator->profile->id // Self-owned for simplicity
        ]);

        // 2. Act
        $response = $this->post(route('evaluations.store', $project), [
            'innovation_score' => 5,
            'relevance_score' => 4,
            'results_score' => 3,
            'impact_score' => 5,
            'methodology_score' => 4,
            'final_score' => 8.5,
            'strengths' => 'Muy buen proyecto',
            'status' => 'finalizada',
        ]);

        // 3. Assert
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('projects.show', $project));
        
        $this->assertDatabaseHas('evaluations', [
            'project_id' => $project->id,
            'final_score' => 8.5,
            'status' => 'finalizada',
        ]);
    }

    public function test_non_coordinator_cannot_evaluate(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        
        $user = User::factory()->create(); 
        $user->assignRole('docente'); // Rol normal
        $this->actingAs($user);

        $category = Category::create(['name' => 'General', 'color' => '#000']);
        $project = Project::create([
            'title' => 'Project to Evaluate',
            'description' => 'Desc',
            'objectives' => 'Obj',
            'impact_description' => 'Impact',
            'category_id' => $category->id,
            'status' => 'en_progreso',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'profile_id' => $user->profile->id
        ]);

        $response = $this->get(route('evaluations.create', $project));

        // Debe redirigir con error
        $response->assertRedirect(route('projects.show', $project));
        $response->assertSessionHas('error');
    }
}
