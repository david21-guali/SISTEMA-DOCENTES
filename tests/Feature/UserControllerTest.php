<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_view_users_index()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    public function test_admin_can_store_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'New Teacher',
                'email' => 'teacher@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'docente'
            ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'teacher@example.com']);
    }

    public function test_admin_can_update_user_role()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create();
        $user->assignRole('docente');

        $response = $this->actingAs($admin)
            ->postJson(route('users.updateRole', $user), [
                'role' => 'coordinador'
            ]);

        $response->assertStatus(200);
        $this->assertTrue($user->fresh()->hasRole('coordinador'));
    }

    public function test_admin_can_delete_user_with_password_confirmation()
    {
        $adminPassword = 'admin-password';
        $admin = User::factory()->create([
            'password' => Hash::make($adminPassword)
        ]);
        $admin->assignRole('admin');
        
        $userToDelete = User::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('users.destroy', $userToDelete), [
                'admin_password' => $adminPassword
            ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }
}
