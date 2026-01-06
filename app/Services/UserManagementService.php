<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PasswordResetByAdmin;

/**
 * Specialized service for user role management and password resets.
 * Aiming for class CC < 10.
 */
class UserManagementService
{
    /**
     * Update user role ensuring system stability.
     * 
     * @param User $user
     * @param string $role
     * @return string|bool
     */
    public function updateRole(User $user, string $role): string|bool
    {
        if ($user->id === Auth::id()) {
            return 'No puedes modificar tu propio rol.';
        }

        if ((new UserSecurityService())->isIllegalAdminDowngrade($user, $role)) {
            return 'Debe existir al menos un administrador activo en el sistema.';
        }

        $user->syncRoles([$role]);
        return true;
    }

    /**
     * Manually reset a user's password and notify them.
     * 
     * @param User $user
     * @param string $pass
     * @return string|bool
     */
    public function resetPassword(User $user, string $pass): string|bool
    {
        if ($user->id === Auth::id()) {
            return 'Usa la configuración de tu perfil para cambiar tu contraseña.';
        }

        $user->update(['password' => Hash::make($pass)]);
        $user->notify(new PasswordResetByAdmin($pass));

        return true;
    }
}
