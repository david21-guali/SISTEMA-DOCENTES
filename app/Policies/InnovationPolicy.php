<?php

namespace App\Policies;

use App\Models\Innovation;
use App\Models\User;

class InnovationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Innovation $innovation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('docente') || $user->hasRole('coordinador');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Innovation $innovation): bool
    {
        return $user->hasRole('admin') || $user->hasRole('coordinador') || $user->profile->id === $innovation->profile_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Innovation $innovation): bool
    {
        return $user->hasRole('admin') || $user->hasRole('coordinador') || $user->profile->id === $innovation->profile_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Innovation $innovation): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Innovation $innovation): bool
    {
        return $user->hasRole('admin');
    }
}
