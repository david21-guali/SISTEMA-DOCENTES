<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Services\TaskFilterService;
use Illuminate\Database\Eloquent\Builder;
use Mockery;
use Tests\TestCase;

class TaskFilterServiceTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    private TaskFilterService $service;
    private $query;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TaskFilterService();
        $this->query = Mockery::mock(Builder::class);
    }

    public function test_apply_filters_with_status(): void
    {
        $this->query->shouldReceive('where')
            ->once()
            ->with('status', 'completada')
            ->andReturnSelf();
            
        // Other filters should be called but skip because empty
        $this->service->applyFilters($this->query, ['status' => 'completada']);
        $this->assertTrue(true);
    }

    public function test_apply_filters_with_overdue_status(): void
    {
        $this->query->shouldReceive('where')
            ->once()
            ->with('status', 'pendiente')
            ->andReturnSelf();
            
        $this->query->shouldReceive('where')
            ->once()
            ->with('due_date', '<', Mockery::any())
            ->andReturnSelf();

        $this->service->applyFilters($this->query, ['status' => 'atrasada']);
        $this->assertTrue(true);
    }

    public function test_apply_filters_with_project(): void
    {
        $this->query->shouldReceive('where')
            ->once()
            ->with('project_id', 5)
            ->andReturnSelf();

        $this->service->applyFilters($this->query, ['project_id' => 5]);
        $this->assertTrue(true);
    }

    public function test_apply_filters_with_search(): void
    {
        $this->query->shouldReceive('where')
            ->once()
            ->with('title', 'LIKE', '%test%')
            ->andReturnSelf();

        $this->service->applyFilters($this->query, ['search' => 'test']);
        $this->assertTrue(true);
    }

    public function test_apply_filters_with_empty_filters(): void
    {
        // Should not call where at all
        $this->query->shouldNotReceive('where');
        $this->service->applyFilters($this->query, []);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
