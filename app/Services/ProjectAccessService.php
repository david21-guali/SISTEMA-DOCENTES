<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;

/**
 * Service to handle project access and ownership logic for filters.
 * Aiming for class CC < 10.
 */
class ProjectAccessService
{
    /**
     * Get a collection of projects the user is allowed to assign tasks to.
     * 
     * @param User $user
     * @return \Illuminate\Support\Collection<int, Project>
     */
    public function getProjectsForUser(User $user): \Illuminate\Support\Collection
    {
        if ($user->hasRole(['admin', 'coordinador'])) {
            return Project::orderBy('title')->get();
        }

        return $this->getProjectsForTeacher($user->profile->id ?? 0);
    }

    /**
     * Retrieve projects where a teacher is an owner or team member.
     * 
     * @param int $profileId
     * @return \Illuminate\Support\Collection<int, Project>
     */
    public function getProjectsForTeacher(int $profileId): \Illuminate\Support\Collection
    {
        return Project::where(function($query) use ($profileId) {
            /** @var \Illuminate\Database\Eloquent\Builder<\App\Models\Project> $query */
            $query->whereHas('team', fn($q) => $q->where('profiles.id', $profileId))
                  ->orWhere('profile_id', $profileId);
        })->orderBy('title')->get();
    }

    /**
     * Restrict task query to projects owned by or assigned to the user profile.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<\App\Models\Task> $query
     * @param int $profileId
     * @return void
     */
    public function applyOwnershipFilter($query, int $profileId): void
    {
        $query->where(function($q) use ($profileId) {
            $q->whereHas('project.team', fn($subQ) => $subQ->where('profiles.id', $profileId))
              ->orWhereHas('project', function($subQ) use ($profileId) {
                  /** @phpstan-ignore-next-line */
                  $subQ->where('profile_id', $profileId);
              });
        });
    }
}
