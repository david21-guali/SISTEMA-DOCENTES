<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PasswordResetByAdmin;

/**
 * Service to handle user write operations.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class UserActionService
{
    /**
     * Create a new user and assign a role.
     * 
     * @param array<string, mixed> $data Contains name, email, password and role.
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);

        return $user;
    }

    /**
     * Update an existing user's basic info and role.
     * 
     * @param User $user
     * @param array<string, mixed> $data
     * @return void
     */
    public function updateUser(User $user, array $data): void
    {
        $payload = ['name' => $data['name'], 'email' => $data['email']];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);
        $user->syncRoles([$data['role']]);
    }

    /**
     * Delete a user after verifying admin credentials and system integrity.
     * 
     * @param User $user The user to be deleted.
     * @param string $adminPass Current admin password for confirmation.
     * @return string|bool True on success, error message on failure.
     */
    public function deleteUser(User $user, string $adminPass): string|bool
    {
        $error = (new UserSecurityService())->validateDeletionSafety($user, $adminPass);
        
        if ($error !== true) {
            return $error;
        }

        return $user->delete() ? true : 'Error al eliminar el registro.';
    }
}
