<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Resource;
use App\Models\ResourceType;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class ResourceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_view_resources_index()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');

        $response = $this->actingAs($user)
            ->get(route('resources.index'));

        $response->assertStatus(200);
        $response->assertViewHas('resources');
    }

    public function test_user_can_store_resource()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('docente');
        $type = ResourceType::factory()->create();

        $file = UploadedFile::fake()->create('guide.pdf', 100);

        $response = $this->actingAs($user)
            ->post(route('resources.store'), [
                'name' => 'Test Resource',
                'resource_type_id' => $type->id,
                'cost' => 500.50,
                'file' => $file
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('resources', ['name' => 'Test Resource', 'cost' => 500.50]);
        Storage::disk('public')->assertExists('resources/' . $file->hashName());
    }

    public function test_user_can_assign_resource_to_project()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);
        $resource = Resource::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('projects.resources.assign', $project), [
                'resource_id' => $resource->id,
                'quantity' => 10,
                'assigned_date' => now()->format('Y-m-d'),
                'notes' => 'Some notes'
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('project_resource', [
            'project_id' => $project->id,
            'resource_id' => $resource->id,
            'quantity' => 10
        ]);
    }

    public function test_user_can_remove_resource_from_project()
    {
        $user = User::factory()->create();
        $user->assignRole('docente');
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);
        $resource = Resource::factory()->create();
        $project->resources()->attach($resource->id, ['quantity' => 1, 'assigned_date' => now()]);

        $response = $this->actingAs($user)
            ->delete(route('projects.resources.remove', [$project, $resource]));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('project_resource', [
            'project_id' => $project->id,
            'resource_id' => $resource->id
        ]);
    }

    public function test_user_can_download_resource()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $path = Storage::disk('public')->put('resources/manual.pdf', 'content');
        $resource = Resource::factory()->create([
            'name' => 'Resource',
            'file_path' => 'resources/manual.pdf'
        ]);

        $response = $this->actingAs($user)
            ->get(route('resources.download', $resource));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=Resource.pdf');
    }

    public function test_admin_can_edit_resource()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $resource = Resource::factory()->create();

        $response = $this->actingAs($user)->get(route('resources.edit', $resource));

        $response->assertStatus(200);
        $response->assertViewHas('resource');
    }

    public function test_admin_can_update_resource()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('admin');
        $resource = Resource::factory()->create();
        $type = ResourceType::factory()->create();

        $response = $this->actingAs($user)->put(route('resources.update', $resource), [
            'name' => 'Updated Name',
            'resource_type_id' => $type->id,
            'cost' => 100
        ]);

        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('resources', ['id' => $resource->id, 'name' => 'Updated Name']);
    }

    public function test_admin_can_delete_resource()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $resource = Resource::factory()->create();

        $response = $this->actingAs($user)->delete(route('resources.destroy', $resource));

        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseMissing('resources', ['id' => $resource->id]);
    }
}
