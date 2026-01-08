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
        $profile = $user->profile;

        Project::factory()->create(['profile_id' => $profile->id, 'end_date' => now()->addDays(5)]);
        Task::factory()->create(['assigned_to' => $profile->id, 'due_date' => now()->addDays(2), 'priority' => 'alta']);
        Meeting::factory()->create(['created_by' => $profile->id, 'meeting_date' => now()->addDays(1), 'status' => 'pendiente']);

        $response = $this->actingAs($user)
            ->get(route('calendar.events'));

        $response->assertStatus(200);
        // It might be more than 3 if factories created nested dependencies
        $response->assertJsonFragment(['type' => 'Proyecto']);
        $response->assertJsonFragment(['type' => 'Tarea']);
        $response->assertJsonFragment(['type' => 'Reunión']);
    }

    public function test_user_can_export_ics()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        // Ensure user has profile
        $profile = $user->profile; 

        // Create events
        Project::factory()->create(['profile_id' => $profile->id, 'title' => 'Proj ICS']);
        
        $response = $this->actingAs($user)
            ->get(route('calendar.export'));

        $response->assertStatus(200);
        $this->assertEquals('text/calendar; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertTrue(str_contains($response->content(), 'BEGIN:VCALENDAR'));
        $this->assertTrue(str_contains($response->content(), 'SUMMARY:Entrega: Proj ICS'));
    }
}
