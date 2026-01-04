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
            ->get(route('users.index', [
                'role' => 'admin',
                'search' => $admin->name
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    public function test_admin_can_view_user_create_form()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->get(route('users.create'));

        $response->assertStatus(200);
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

    public function test_user_can_view_public_profile()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get(route('users.show', $otherUser));

        $response->assertStatus(200);
    }

    public function test_admin_can_edit_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->get(route('users.edit', $user));

        $response->assertStatus(200);
    }

    public function test_admin_can_update_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'name' => 'Updated Name',
                'email' => $user->email,
                'role' => 'coordinador'
            ]);

        $response->assertRedirect(route('users.index'));
        $this->assertEquals('Updated Name', $user->fresh()->name);
    }

    public function test_admin_can_manual_reset_password()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('users.manualReset', $user), [
                'password' => 'new-password'
            ]);

        $response->assertRedirect();
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_admin_cannot_delete_self()
    {
        $adminPassword = 'password';
        $admin = User::factory()->create(['password' => Hash::make($adminPassword)]);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->delete(route('users.destroy', $admin), [
                'admin_password' => $adminPassword
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
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

    public function test_admin_cannot_change_own_role()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->postJson(route('users.updateRole', $admin), [
                'role' => 'docente'
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_remove_last_admin_role()
    {
        // Clear admins and create exactly one
        User::role('admin')->get()->each(function($u) {
            $u->removeRole('admin');
        });

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $this->actingAs($admin);
        
        $response = $this->postJson(route('users.updateRole', $admin), [
            'role' => 'docente'
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_remove_only_admin_role()
    {
        // First delete all other admins created by factories in other tests 
        // (RefreshDatabase handles this if we are careful).
        User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->delete();
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $otherAdmin = User::factory()->create();
        $otherAdmin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->postJson(route('users.updateRole', $otherAdmin), [
                'role' => 'docente'
            ]);

        $response->assertStatus(200); // Should work because there are 2
        
        $response = $this->actingAs($admin)
            ->postJson(route('users.updateRole', $admin), [
                'role' => 'docente'
            ]);
        $response->assertStatus(403); // Cannot change own role
    }

    public function test_admin_cannot_manual_reset_own_password()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->post(route('users.manualReset', $admin), [
                'password' => 'new-password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
