<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_store_category()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->post(route('categories.store'), [
                'name' => 'Test Category',
                'color' => '#FF5733',
                'description' => 'Test Description'
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
    }

    public function test_admin_can_store_category_via_json()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->postJson(route('categories.store'), [
                'name' => 'Test API Category',
                'color' => '#00FF00',
                'description' => 'API Description'
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('categories', ['name' => 'Test API Category']);
    }

    public function test_admin_can_delete_unused_category()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson(route('categories.destroy', $category));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_non_admin_cannot_delete_category()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        $category = Category::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson(route('categories.destroy', $category));

        $response->assertStatus(403);
    }
}
