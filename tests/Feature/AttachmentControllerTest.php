<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Attachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

class AttachmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_upload_attachments_to_project()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('docente');
        $project = Project::factory()->create(['profile_id' => $user->profile->id]);

        $file1 = UploadedFile::fake()->create('document1.pdf', 100);
        $file2 = UploadedFile::fake()->image('image1.jpg');

        $response = $this->actingAs($user)
            ->post(route('attachments.store', ['type' => 'project', 'id' => $project->id]), [
                'files' => [$file1, $file2]
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseCount('attachments', 2);
        Storage::disk('public')->assertExists('attachments/projects/' . $project->id . '/' . $file1->hashName());
    }

    public function test_user_can_download_attachment()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $file = UploadedFile::fake()->create('report.pdf', 50);
        $path = $file->store('attachments/projects/1', 'public');
        
        $attachment = Attachment::create([
            'filename' => basename($path),
            'original_name' => 'report.pdf',
            'mime_type' => 'application/pdf',
            'size' => 50,
            'path' => $path,
            'uploaded_by' => $user->profile->id,
            'attachable_id' => 1,
            'attachable_type' => Project::class,
        ]);

        $response = $this->actingAs($user)
            ->get(route('attachments.download', $attachment));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=report.pdf');
    }

    public function test_user_can_delete_own_attachment()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('docente');
        
        $attachment = Attachment::create([
            'filename' => 'file.pdf',
            'original_name' => 'file.pdf',
            'mime_type' => 'application/pdf',
            'size' => 50,
            'path' => 'attachments/projects/1/file.pdf',
            'uploaded_by' => $user->profile->id,
            'attachable_id' => 1,
            'attachable_type' => Project::class,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('attachments.destroy', $attachment));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
    }
}
