<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\InnovationType;
use App\Models\Innovation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InnovationTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles if needed, or manual roles
    }

    public function test_admin_can_view_innovation_types(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        InnovationType::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('innovation-types.index'));

        $response->assertStatus(200);
        $response->assertViewHas('types');
    }

    public function test_authorized_user_can_store_innovation_type(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->postJson(route('innovation-types.store'), [
            'name' => 'New Type',
            'description' => 'A new type description'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('innovation_types', ['name' => 'New Type', 'slug' => 'new-type']);
    }

    public function test_non_authorized_user_cannot_store_innovation_type(): void
    {
        $user = User::factory()->create();
        // No role

        $response = $this->actingAs($user)->postJson(route('innovation-types.store'), [
            'name' => 'Should Fail',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_innovation_type(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $type = InnovationType::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->putJson(route('innovation-types.update', $type), [
            'name' => 'Updated Name',
            'description' => 'Updated description'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('innovation_types', ['id' => $type->id, 'name' => 'Updated Name']);
    }

    public function test_admin_can_delete_innovation_type_without_innovations(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $type = InnovationType::factory()->create();

        $response = $this->actingAs($admin)->deleteJson(route('innovation-types.destroy', $type));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('innovation_types', ['id' => $type->id]);
    }

    public function test_admin_cannot_delete_innovation_type_with_innovations(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $type = InnovationType::factory()->create();
        Innovation::factory()->create(['innovation_type_id' => $type->id]);

        $response = $this->actingAs($admin)->deleteJson(route('innovation-types.destroy', $type));

        $response->assertStatus(400);
        $this->assertDatabaseHas('innovation_types', ['id' => $type->id]);
    }
}
