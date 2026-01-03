<?php

namespace Tests\Unit;

use App\Models\Innovation;
use App\Models\Meeting;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_innovation_color_helper()
    {
        $innovation = new Innovation(['status' => 'completada']);
        $this->assertEquals('success', $innovation->status_color);

        $innovation->status = 'en_revision';
        $this->assertEquals('warning', $innovation->status_color);
    }

    public function test_meeting_date_helpers()
    {
        $meeting = new Meeting([
            'meeting_date' => now()->addDay(),
            'start_time' => '10:00',
            'end_time' => '11:00'
        ]);

        $this->assertFalse($meeting->is_past);
        $this->assertNotNull($meeting->formatted_date);
    }
}
