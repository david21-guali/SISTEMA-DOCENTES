<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Profile;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_project(): void
    {
        // 1. Arrange: Crear usuario, perfil y categoría
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        
        $category = Category::create(['name' => 'Investigación', 'color' => '#FF0000']);
        $profile = $user->profile;

        // 2. Act: Enviar formulario de creación con TODOS los campos requeridos
        $response = $this->post(route('projects.store'), [
            'title' => 'Nuevo Proyecto Test',
            'description' => 'Descripción de prueba',
            'objectives' => 'Objetivos claros del proyecto',
            'impact_description' => 'Impacto social alto',
            'category_id' => $category->id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYear()->format('Y-m-d'),
            'status' => 'en_progreso',
            'budget' => 5000,
            'team_members' => [$user->id], // El creador suele ser parte del equipo
            'profile_id' => $profile->id,
        ]);

        // 3. Assert: Verificar redirección y base de datos
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('projects.index'));
        
        $this->assertDatabaseHas('projects', [
            'title' => 'Nuevo Proyecto Test',
            'budget' => 5000,
        ]);
    }

    public function test_project_requires_validation(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        // Enviar formulario vacío
        $response = $this->post(route('projects.store'), []);

        // Verificar que falle por falta de título, fecha, equipo, etc.
        $response->assertSessionHasErrors(['title', 'end_date', 'team_members']);
    }
}
