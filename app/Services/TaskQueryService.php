<?php

namespace App\Services;

use App\Models\Task;

/**
 * Service to handle read-only operations for Tasks.
 * Optimized for maximum Maintainability Index (MI >= 65).
 */
class TaskQueryService
{
    /**
     * Retrieve a paginated list of tasks based on user permissions and filters.
     * 
     * @param \App\Models\User $user The user requesting the data.
     * @param array $filters Key-value pairs of filters (status, project_id, search).
     * @param int $perPage Number of items per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTasks(\App\Models\User $user, array $filters, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Task::with(['project', 'assignedProfile.user', 'assignees.user']);

        if (!$user->hasRole(['admin', 'coordinador'])) {
            (new ProjectAccessService())->applyOwnershipFilter($query, $user->profile->id ?? 0);
        }

        (new TaskFilterService())->applyFilters($query, $filters);

        return $query->latest()->paginate($perPage);
    }

    public function getProjectsForUser(\App\Models\User $user): \Illuminate\Support\Collection
    {
        return (new ProjectAccessService())->getProjectsForUser($user);
    }
}
