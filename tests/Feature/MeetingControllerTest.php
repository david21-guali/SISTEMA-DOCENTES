<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->profile = $this->user->profile;
        $this->project = Project::factory()->create(['profile_id' => $this->profile->id]);
    }

    public function test_user_can_view_meetings_index()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('meetings.index'));
        $response->assertStatus(200);
        $response->assertViewIs('app.back.meetings.index');
    }

    public function test_user_can_create_meeting()
    {
        $this->actingAs($this->user);
        
        $otherUser = User::factory()->create();
        
        // IMPORTANT: Add user to project team to pass validation
        $this->project->team()->attach($otherUser->profile->id);

        $meetingData = [
            'title' => 'Test Meeting',
            'description' => 'Test Description',
            'project_id' => $this->project->id,
            'meeting_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'location' => 'Test Location',
            'type' => 'virtual',
            'participants' => [$otherUser->id],
            'status' => 'pendiente', // Adding required status
        ];

        $response = $this->post(route('meetings.store'), $meetingData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('meetings', ['title' => 'Test Meeting']);
    }

    public function test_user_can_update_attendance()
    {
        $meeting = Meeting::factory()->create(['created_by' => $this->profile->id]);
        $meeting->participants()->attach($this->profile->id, ['attendance' => 'pendiente']);
        
        $this->actingAs($this->user);
        
        $attendanceData = [
            'attendance' => 'confirmada',
        ];

        $response = $this->post(route('meetings.updateAttendance', $meeting), $attendanceData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('meeting_profile', [
            'meeting_id' => $meeting->id,
            'profile_id' => $this->profile->id,
            'attendance' => 'confirmada'
        ]);
    }

    public function test_user_can_view_meeting_details()
    {
        $meeting = Meeting::factory()->create(['created_by' => $this->profile->id]);
        $this->actingAs($this->user);
        $response = $this->get(route('meetings.show', $meeting));
        $response->assertStatus(200);
    }

    public function test_user_can_update_meeting()
    {
        $meeting = Meeting::factory()->create(['created_by' => $this->profile->id]);
        $this->actingAs($this->user);
        
        $otherUser = User::factory()->create();
        
        // IMPORTANT: Add user to project team to pass validation
        $this->project->team()->attach($otherUser->profile->id);

        $response = $this->put(route('meetings.update', $meeting), [
            'title' => 'Updated Title',
            'description' => 'Updated Desc',
            'project_id' => $this->project->id,
            'meeting_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'location' => 'Online',
            'type' => 'virtual',
            'status' => 'pendiente',
            'participants' => [$otherUser->id]
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('meetings', ['title' => 'Updated Title']);
    }

    public function test_user_can_cancel_meeting()
    {
        $meeting = Meeting::factory()->create(['created_by' => $this->profile->id]);
        $this->actingAs($this->user);
        
        $response = $this->post(route('meetings.cancel', $meeting), [
            'cancellation_reason' => 'Busy'
        ]);
        
        $response->assertRedirect();
        $meeting->refresh();
        $this->assertEquals('cancelada', $meeting->status);
    }

    public function test_user_can_delete_meeting()
    {
        $meeting = Meeting::factory()->create(['created_by' => $this->profile->id]);
        $this->actingAs($this->user);
        
        $response = $this->delete(route('meetings.destroy', $meeting));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('meetings', ['id' => $meeting->id]);
    }
}
