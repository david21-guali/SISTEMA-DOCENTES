<?php

namespace Tests\Unit;

use App\Models\Resource;
use App\Models\ResourceType;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_resource_relations(): void
    {
        $type = ResourceType::factory()->create();
        $resource = Resource::factory()->create(['resource_type_id' => $type->id]);

        $this->assertInstanceOf(ResourceType::class, $resource->type);
        $this->assertEquals($type->id, $resource->type->id);
    }

    public function test_resource_projects_relation(): void
    {
        $resource = Resource::factory()->create();
        $project = Project::factory()->create();
        
        $resource->projects()->attach($project->id, [
            'quantity' => 2,
            'assigned_date' => now(),
        ]);

        $this->assertCount(1, $resource->projects);
    }

    public function test_resource_type_slug_attribute(): void
    {
        $type = ResourceType::factory()->create(['slug' => 'test-slug']);
        $resource = Resource::factory()->create(['resource_type_id' => $type->id]);

        $this->assertEquals('test-slug', $resource->type_slug);
    }
}
