<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Profile;
use App\Services\NotificationPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPreferenceServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationPreferenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NotificationPreferenceService();
    }

    public function test_get_channels_with_default_preferences(): void
    {
        $user = User::factory()->create();
        
        $channels = $this->service->getChannels($user, 'meetings');
        
        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
    }

    public function test_get_channels_with_type_disabled(): void
    {
        $user = User::factory()->create();
        $user->profile->update([
            'notification_preferences' => ['meetings' => false]
        ]);
        
        $channels = $this->service->getChannels($user, 'meetings');
        
        $this->assertEmpty($channels);
    }

    public function test_get_channels_with_email_disabled(): void
    {
        $user = User::factory()->create();
        $user->profile->update([
            'notification_preferences' => ['email_enabled' => false]
        ]);
        
        $channels = $this->service->getChannels($user, 'meetings');
        
        $this->assertContains('database', $channels);
        $this->assertNotContains('mail', $channels);
    }
}
