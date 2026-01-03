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
        
        $innovationData = [
            'title' => 'Test Innovation',
            'description' => 'Test Description',
            'innovation_type_id' => 1, // Assumes type exists or use factory
            'status' => 'propuesta',
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
