<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\InnovationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InnovationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_innovation(): void
    {
        // 1. Arrange
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $type = InnovationType::factory()->create(['name' => 'Tecnológica']);

        // 2. Act
        $response = $this->post(route('innovations.store'), [
            'title' => 'Innovación Educativa',
            'description' => 'Uso de IA en clases',
            'innovation_type_id' => $type->id,
            'methodology' => 'Aprendizaje Basado en Proyectos',
            'expected_results' => 'Mejorar retención',
            'actual_results' => 'En proceso',
        ]);

        // 3. Assert
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('innovations.index'));
        
        $this->assertDatabaseHas('innovations', [
            'title' => 'Innovación Educativa',
            'methodology' => 'Aprendizaje Basado en Proyectos',
        ]);
    }

    public function test_innovation_validation_flow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('innovations.store'), []);

        $response->assertSessionHasErrors(['title', 'innovation_type_id']);
    }
}
