<?php

namespace Tests\Unit;

use App\Models\Resource;
use App\Models\ResourceType;
use App\Models\Project;
use App\Services\ResourceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceServiceTest extends TestCase
{
    use RefreshDatabase;

    private ResourceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ResourceService();
    }

    public function test_create_resource_with_file(): void
    {
        Storage::fake('public');
        $type = ResourceType::factory()->create();
        $file = UploadedFile::fake()->create('resource.pdf', 500);

        $resource = $this->service->createResource([
            'name' => 'Test Resource',
            'resource_type_id' => $type->id,
            'cost' => 10.5
        ], $file);

        $this->assertNotNull($resource->file_path);
        Storage::disk('public')->assertExists($resource->file_path);
    }

    public function test_update_resource_with_file_replacement(): void
    {
        Storage::fake('public');
        $type = ResourceType::factory()->create();
        $oldFile = UploadedFile::fake()->create('old.pdf', 500);
        $resource = Resource::factory()->create([
            'resource_type_id' => $type->id,
            'file_path' => $oldFile->store('resources', 'public')
        ]);

        $newFile = UploadedFile::fake()->create('new.pdf', 500);
        $this->service->updateResource($resource, ['name' => 'Updated'], $newFile);

        $this->assertEquals('Updated', $resource->name);
        Storage::disk('public')->assertMissing('resources/' . $oldFile->hashName());
        Storage::disk('public')->assertExists($resource->file_path);
    }

    public function test_assign_to_project(): void
    {
        $project = Project::factory()->create();
        $resource = Resource::factory()->create();
        
        $this->service->assignToProject($project, [
            'resource_id' => $resource->id,
            'quantity' => 5,
            'assigned_date' => now()->toDateString(),
            'notes' => 'Some notes'
        ]);

        $this->assertCount(1, $project->resources);
        $this->assertEquals(5, $project->resources()->first()->pivot->quantity);
    }
}
