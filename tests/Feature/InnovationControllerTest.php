<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Innovation;
use App\Models\InnovationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class InnovationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        
        // Ensure Innovation types exist
        InnovationType::firstOrCreate([
            'name' => 'Investigación pedagógica'
        ], [
            'slug' => 'investigacion-pedagogica',
            'weight' => 1.5
        ]);
    }

    public function test_user_can_view_innovation_index()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $response = $this->actingAs($user)->get(route('innovations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('app.back.innovations.index');
    }

    public function test_user_can_create_innovation_with_files()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('docente');
        $type = InnovationType::first();

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($user)->post(route('innovations.store'), [
            'title' => 'Nueva Innovación',
            'description' => 'Descripción de prueba',
            'methodology' => 'Metodología Ágil',
            'expected_results' => 'Resultados esperados altos',
            'actual_results' => 'Resultados obtenidos satisfactorios',
            'impact_score' => 8,
            'innovation_type_id' => $type->id,
            'status' => 'en_proceso',
            'files' => [$file], // This might need to be 'evidence_files' based on controller, checking...
            'evidence_files' => [$file],
        ]);

        $response->assertRedirect(route('innovations.index'));
        $this->assertDatabaseHas('innovations', ['title' => 'Nueva Innovación']);
        
        $innovation = Innovation::where('title', 'Nueva Innovación')->first();
        $this->assertTrue($innovation->attachments->count() > 0);
    }

    public function test_admin_can_approve_innovation()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $owner = User::factory()->create();
        $owner->assignRole('docente');
        
        $innovation = Innovation::factory()->create([
            'profile_id' => $owner->profile->id,
            'status' => 'en_revision' // Start as revision
        ]);

        $response = $this->actingAs($user)->post(route('innovations.approve', $innovation), [
            'review_notes' => 'Buen trabajo'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('innovations', [
            'id' => $innovation->id,
            'status' => 'aprobada',
            'review_notes' => 'Buen trabajo'
        ]);
    }

    public function test_user_can_delete_own_innovation()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $innovation = Innovation::factory()->create([
            'profile_id' => $user->profile->id,
        ]);

        $response = $this->actingAs($user)->delete(route('innovations.destroy', $innovation));

        $response->assertRedirect(route('innovations.index'));
        $this->assertDatabaseMissing('innovations', ['id' => $innovation->id]);
    }
}
