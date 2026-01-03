<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_view_notifications_index()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $response = $this->actingAs($user)
            ->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertViewIs('notifications.index');
    }

    public function test_user_can_mark_notification_as_read_and_redirect()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);

        $user->notifications()->create([
            'id' => 'test-id',
            'type' => 'App\Notifications\ProjectAssigned',
            'data' => [
                'project_id' => $project->id,
                'title' => 'New Project',
            ],
        ]);

        $response = $this->actingAs($user)
            ->get(route('notifications.read', 'test-id'));

        $response->assertRedirect(route('projects.show', $project));
        $this->assertNotNull($user->notifications()->first()->read_at);
    }

    public function test_user_can_mark_all_as_read()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $user->notifications()->create([
            'id' => 'id-1',
            'type' => 'test',
            'data' => [],
        ]);

        $response = $this->actingAs($user)
            ->post(route('notifications.markAllRead'));

        $response->assertStatus(302);
        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    public function test_user_can_delete_notification()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $user->notifications()->create([
            'id' => 'id-to-delete',
            'type' => 'test',
            'data' => [],
        ]);

        $response = $this->actingAs($user)
            ->delete(route('notifications.destroy', 'id-to-delete'));

        $response->assertStatus(302);
        $this->assertEquals(0, $user->notifications()->count());
    }

    public function test_user_can_delete_all_read_notifications()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $user->notifications()->create([
            'id' => 'read-id',
            'type' => 'test',
            'data' => [],
            'read_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->delete(route('notifications.destroyAllRead'));

        $response->assertStatus(302);
        $this->assertEquals(0, $user->readNotifications()->count());
    }
}
