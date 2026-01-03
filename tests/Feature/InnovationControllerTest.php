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

    public function test_user_can_view_best_practices()
    {
        Innovation::factory()->count(3)->create(['status' => 'completada', 'impact_score' => 9]);
        $this->actingAs($this->user);
        $response = $this->get(route('innovations.best-practices'));
        $response->assertStatus(200);
    }

    public function test_user_can_update_innovation()
    {
        $innovation = Innovation::factory()->create(['profile_id' => $this->profile->id]);
        $type = \App\Models\InnovationType::factory()->create();
        $this->actingAs($this->user);
        
        $response = $this->put(route('innovations.update', $innovation), [
            'title' => 'Updated Title',
            'description' => 'Updated Desc',
            'innovation_type_id' => $type->id,
            'methodology' => 'Updated Meth',
            'expected_results' => 'Updated Exp',
            'actual_results' => 'Updated Act',
            'status' => 'en_implementacion',
            'impact_score' => 9,
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('innovations', ['title' => 'Updated Title']);
    }

    public function test_user_can_delete_innovation()
    {
        $innovation = Innovation::factory()->create(['profile_id' => $this->profile->id]);
        $this->actingAs($this->user);
        
        $response = $this->delete(route('innovations.destroy', $innovation));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('innovations', ['id' => $innovation->id]);
    }

    public function test_user_can_delete_evidence()
    {
        $innovation = Innovation::factory()->create(['profile_id' => $this->profile->id]);
        $attachment = $innovation->attachments()->create([
            'filename' => 'test.pdf',
            'original_name' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
            'path' => 'test.pdf',
            'uploaded_by' => $this->profile->id,
        ]);
        
        $this->actingAs($this->user);
        $response = $this->delete(route('innovations.evidence.destroy', [$innovation, $attachment->id]));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
    }
}
