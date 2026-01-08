<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-projects');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('view-projects');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-projects');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // Admins can update any project
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owners can update their own projects if they have the permission
        // Or team members if we want to allow that? Role docente usually has edit-projects.
        // User request specifically said docentes shouldn't CREATE, but they can edit their own/assigned?
        // RolePermissionSeeder says docente has edit-projects.
        
        return $user->hasPermissionTo('edit-projects') && 
               ($user->profile->id === $project->profile_id || $project->team->contains('user_id', $user->id));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasPermissionTo('delete-projects') && $user->profile->id === $project->profile_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }
}
