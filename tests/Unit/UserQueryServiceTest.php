<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Profile;
use App\Services\UserQueryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserQueryServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserQueryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserQueryService();
    }

    public function test_get_users_with_filters(): void
    {
        User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
        User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);

        $results = $this->service->getUsers(['search' => 'Alice']);
        $this->assertCount(1, $results);
        $this->assertEquals('Alice', $results->first()->name);
    }

    public function test_get_user_profile_stats_for_empty_profile(): void
    {
        $user = User::factory()->create();
        // Delete profile if factory creates it
        $user->profile()->delete();
        $user->refresh();

        $stats = $this->service->getUserProfileStats($user);

        $this->assertEquals(0, $stats['projects']);
        $this->assertEquals(0, $stats['tasks']);
    }

    public function test_get_user_profile_stats_with_data(): void
    {
        $user = User::factory()->has(Profile::factory())->create();
        
        $stats = $this->service->getUserProfileStats($user);
        
        $this->assertArrayHasKey('projects', $stats);
        $this->assertArrayHasKey('tasks', $stats);
    }
}
