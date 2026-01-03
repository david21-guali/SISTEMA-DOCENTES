<?php

namespace Tests\Feature;

use App\Models\Innovation;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InnovationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->profile = Profile::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_user_can_view_innovations_index()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('innovations.index'));
        $response->assertStatus(200);
        $response->assertViewIs('app.back.innovations.index');
    }

    public function test_user_can_create_innovation()
    {
        $this->actingAs($this->user);
        $type = \App\Models\InnovationType::factory()->create();
        
        $innovationData = [
            'title' => 'Test Innovation',
            'description' => 'Test Description',
            'innovation_type_id' => $type->id,
            'methodology' => 'Test Methodology',
            'expected_results' => 'Test Expected Results',
            'actual_results' => 'Test Actual Results',
            'impact_score' => 8,
        ];

        $response = $this->post(route('innovations.store'), $innovationData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('innovations', ['title' => 'Test Innovation']);
    }

    public function test_user_can_view_innovation_details()
    {
        $innovation = Innovation::factory()->create(['profile_id' => $this->profile->id]);
        
        $this->actingAs($this->user);
        $response = $this->get(route('innovations.show', $innovation));
        
        $response->assertStatus(200);
        $response->assertViewHas('innovation', $innovation);
    }
}
