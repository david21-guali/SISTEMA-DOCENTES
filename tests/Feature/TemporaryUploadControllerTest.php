<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TemporaryUploadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_temporary_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('temp.upload'), [
            'file' => UploadedFile::fake()->create('document.pdf', 500)
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'path', 'name', 'id']);
        
        $path = $response->json('path');
        Storage::disk('public')->assertExists($path);
    }

    public function test_upload_fails_without_file(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson(route('temp.upload'), []);
        $response->assertStatus(422);
    }

    public function test_user_can_delete_temporary_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $path = 'temp/testfile.txt';
        Storage::disk('public')->put($path, 'content');

        $response = $this->actingAs($user)->deleteJson(route('temp.delete'), [
            'path' => $path
        ]);

        $response->assertStatus(200);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_delete_fails_for_invalid_path(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('temp.delete'), [
            'path' => 'invalid/path.txt'
        ]);

        $response->assertStatus(404);
    }
}
