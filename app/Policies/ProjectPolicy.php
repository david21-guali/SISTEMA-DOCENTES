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
        if ($user->hasRole(['admin', 'coordinador'])) return true;

        return $user->hasPermissionTo('view-projects') && $this->isMemberOrOwner($user, $project);
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
        if ($user->hasRole(['admin', 'coordinador'])) return true;

        return $user->hasPermissionTo('edit-projects') && $this->isMemberOrOwner($user, $project);
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

    /**
     * Determine whether the user can upload a final report.
     */
    public function uploadFinalReport(User $user, Project $project): bool
    {
        return $user->hasRole(['admin', 'coordinador']) || $this->isMemberOrOwner($user, $project);
    }

    /**
     * Check if user is the owner or a team member.
     */
    private function isMemberOrOwner(User $user, Project $project): bool
    {
        if (!$user->profile) return false;
        return $user->profile->id === $project->profile_id || $project->team->contains('user_id', $user->id);
    }
}
