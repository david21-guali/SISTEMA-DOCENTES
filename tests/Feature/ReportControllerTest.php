<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->profile = Profile::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_user_can_view_reports_index()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertViewIs('app.back.reports.index');
    }

    public function test_user_can_view_projects_report()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('reports.projects.pdf'));
        $response->assertStatus(200);
    }

    public function test_user_can_view_tasks_report()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('reports.tasks.pdf'));
        $response->assertStatus(200);
    }
}
