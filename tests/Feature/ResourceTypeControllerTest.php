<?php

namespace Tests\Feature;

use App\Models\ResourceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class ResourceTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_authorized_user_can_store_resource_type()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->postJson(route('resource-types.store'), [
                'name' => 'Video',
                'description' => 'Video resources'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('resource_types', ['name' => 'Video']);
    }

    public function test_admin_can_delete_unused_resource_type()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $type = ResourceType::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson(route('resource-types.destroy', $type));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('resource_types', ['id' => $type->id]);
    }

    public function test_non_admin_cannot_delete_resource_type()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        $type = ResourceType::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson(route('resource-types.destroy', $type));

        $response->assertStatus(403);
    }
}
