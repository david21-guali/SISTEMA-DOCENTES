<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PasswordResetByAdmin;

/**
 * Service to handle security and validation for users.
 * Aiming for very low Class Cyclomatic Complexity.
 */
class UserSecurityService
{
    /**
     * Validate if it is safe to delete a user.
     * 
     * @param User $user
     * @param string $pass
     * @return string|bool
     */
    public function validateDeletionSafety(User $user, string $pass): string|bool
    {
        if (!Hash::check($pass, Auth::user()->password)) {
            return 'La contraseña de administrador es incorrecta.';
        }

        return $this->checkIntegrityRules($user);
    }

    /**
     * Check if user deletion violates business integrity rules.
     * 
     * @param User $user
     * @return string|bool
     */
    private function checkIntegrityRules(User $user): string|bool
    {
        if ($user->id === Auth::id()) {
            return 'Por seguridad, no puedes eliminar tu propia cuenta.';
        }

        if ($this->isRestrictedAdminDeletion($user)) {
            return 'No se puede eliminar al único administrador del sistema.';
        }

        return true;
    }

    /**
     * Detect if user is attempting to remove the last admin via role change.
     * 
     * @param User $user
     * @param string $newRole
     * @return bool
     */
    public function isIllegalAdminDowngrade(User $user, string $newRole): bool
    {
        return $user->hasRole('admin') && $newRole !== 'admin' && $this->isAdminCountLow();
    }

    /**
     * Determine if admin deletion is restricted due to low count.
     * 
     * @param User $user
     * @return bool
     */
    private function isRestrictedAdminDeletion(User $user): bool
    {
        return $user->hasRole('admin') && $this->isAdminCountLow();
    }

    /**
     * Check if there is only one admin left.
     * 
     * @return bool
     */
    private function isAdminCountLow(): bool
    {
        return User::whereHas('roles', function($q) {
            /** @phpstan-ignore-next-line */
            $q->where('name', 'admin');
        })->count() <= 1;
    }
}
