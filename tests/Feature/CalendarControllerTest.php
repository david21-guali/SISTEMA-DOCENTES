<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Meeting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class CalendarControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_view_calendar_index()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $response = $this->actingAs($user)
            ->get(route('calendar.index'));

        $response->assertStatus(200);
        $response->assertViewIs('calendar.index');
    }

    public function test_user_can_fetch_calendar_events_json()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        Project::factory()->create(['end_date' => now()->addDays(5)]);
        Task::factory()->create(['due_date' => now()->addDays(2), 'priority' => 'alta']);
        Meeting::factory()->create(['meeting_date' => now()->addDays(1), 'status' => 'pendiente']);

        $response = $this->actingAs($user)
            ->get(route('calendar.events'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonFragment(['type' => 'Proyecto']);
        $response->assertJsonFragment(['type' => 'Tarea']);
        $response->assertJsonFragment(['type' => 'Reunión']);
    }
}
