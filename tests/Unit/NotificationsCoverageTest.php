<?php

namespace Tests\Unit;

use App\Models\Innovation;
use App\Models\User;
use App\Notifications\InnovationReviewRequested;
use App\Notifications\InnovationStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_innovation_review_requested_notification_structure(): void
    {
        $innovation = Innovation::factory()->create(['title' => 'Test Innovation']);
        $notification = new InnovationReviewRequested($innovation);

        $notifiable = User::factory()->create();
        $data = $notification->toArray($notifiable);

        $this->assertEquals($innovation->id, $data['innovation_id']);
        $this->assertStringContainsString('revisión', $data['message']);
    }

    public function test_innovation_status_changed_notification_structure(): void
    {
        $innovation = Innovation::factory()->create(['title' => 'Test Innovation', 'status' => 'aprobada']);
        $notification = new InnovationStatusChanged($innovation);

        $notifiable = User::factory()->create();
        $data = $notification->toArray($notifiable);

        $this->assertEquals($innovation->id, $data['innovation_id']);
        $this->assertStringContainsString('Aprobada', $data['message']);
    }
}
