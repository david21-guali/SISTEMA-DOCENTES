<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();
        
        // Ensure profile exists (it should, but just in case)
        if (!$user->profile) {
            $user->profile()->create();
            $user->refresh();
        }

        // Initialize profile data from validated input
        $profileData = $validated;
        
        // Remove user fields from profile data
        unset($profileData['name'], $profileData['email']);

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->profile->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile->avatar);
            }
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            // Keep existing avatar if no new one is uploaded
            unset($profileData['avatar']);
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Procesar las preferencias de notificación si el formulario enviado es de ese tipo
        if ($request->form_type === 'notifications') {
            $prefs = $request->input('notification_preferences', []);
            
            // Lista completa de llaves que permitimos guardar para evitar inyecciones de datos no deseados
            $allKeys = [
                'meetings', 
                'projects', 
                'tasks', 
                'resources', 
                'reminders', 
                'innovations', 
                'forum', 
                'messages', 
                'email_enabled'
            ];
            
            $finalPrefs = [];
            foreach ($allKeys as $key) {
                // Si el checkbox está marcado llega en el request, de lo contrario lo seteamos como false
                $finalPrefs[$key] = isset($prefs[$key]);
            }
            $profileData['notification_preferences'] = $finalPrefs;
        }

        $user->profile->update($profileData);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's avatar.
     */
    public function destroyAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile && $user->profile->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile->avatar);
            $user->profile->update(['avatar' => null]);
        }

        return Redirect::route('profile.edit')->with('status', 'avatar-deleted');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
