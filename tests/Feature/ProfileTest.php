<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_user_can_update_avatar()
    {
        $user = User::factory()->create();
        \Illuminate\Support\Facades\Storage::fake('public');
        
        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)
            ->patch('/profile', [
                'name' => 'Profile Owner',
                'email' => $user->email,
                'avatar' => $file
            ]);

        $response->assertRedirect('/profile');
        $user->refresh();
        $this->assertNotNull($user->profile->avatar);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($user->profile->avatar);
    }

    public function test_user_can_delete_avatar()
    {
        $user = User::factory()->create();
        $user->profile->update(['avatar' => 'avatars/old.jpg']);
        \Illuminate\Support\Facades\Storage::fake('public');
        \Illuminate\Support\Facades\Storage::disk('public')->put('avatars/old.jpg', 'content');

        $response = $this->actingAs($user)
            ->delete(route('profile.avatar.destroy'));

        $response->assertRedirect('/profile');
        $user->refresh();
        $this->assertNull($user->profile->avatar);
    }

    public function test_user_can_update_notification_preferences()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'form_type' => 'notifications',
                'notification_preferences' => [
                    'meetings' => 'on',
                    'projects' => 'on'
                ]
            ]);

        $response->assertRedirect('/profile');
        $user->refresh();
        $prefs = $user->profile->notification_preferences;
        $this->assertTrue($prefs['meetings']);
        $this->assertTrue($prefs['projects']);
        $this->assertFalse($prefs['tasks']); // Not sent, so should be false
    }
}
