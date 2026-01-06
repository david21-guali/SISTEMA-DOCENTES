<?php

namespace App\Services;

use App\Models\User;

/**
 * Service to handle read-only operations for Users and their statistics.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class UserQueryService
{
    /**
     * Retrieve a paginated list of users based on role and search filters.
     * 
     * @param array $filters Query parameters (role, search).
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUsers(array $filters, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = User::with('roles');

        $this->applyRoleFilter($query, $filters['role'] ?? null);
        $this->applySearchFilter($query, $filters['search'] ?? null);

        return $query->orderBy('name')->paginate($perPage);
    }

    /**
     * Get aggregated system-wide user statistics by role.
     * 
     * @return array
     */
    public function getUserStats(): array
    {
        return [
            'total'         => User::count(),
            'admins'        => $this->countByRole('admin'),
            'coordinadores' => $this->countByRole('coordinador'),
            'docentes'      => $this->countByRole('docente'),
        ];
    }

    /**
     * Fetch all available security roles in alphabetical order.
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getRoles(): \Illuminate\Support\Collection
    {
        return \App\Models\Role::orderBy('name')->get();
    }

    /**
     * Aggregate activity statistics for a specific user profile.
     * 
     * @param \App\Models\User $user
     * @return array
     */
    public function getUserProfileStats(\App\Models\User $user): array
    {
        if (!$user->profile) {
            return $this->getEmptyStats();
        }

        return [
            'projects'        => $this->countUserProjects($user),
            'tasks'           => $this->countUserTasks($user),
            'completed_tasks' => $this->countUserCompletedTasks($user),
            'innovations'     => $this->countUserInnovations($user),
        ];
    }

    /**
     * Restrict query by a specific role name.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $role
     * @return void
     */
    private function applyRoleFilter($query, ?string $role): void
    {
        if (!empty($role)) {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }
    }

    /**
     * Search users by name or email.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $search
     * @return void
     */
    private function applySearchFilter($query, ?string $search): void
    {
        if (empty($search)) {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%');
        });
    }

    /**
     * Count users assigned to a specific role.
     * 
     * @param string $role
     * @return int
     */
    private function countByRole(string $role): int
    {
        return User::whereHas('roles', fn($q) => $q->where('name', $role))->count();
    }

    /**
     * Count projects associated with a user profile.
     * 
     * @param User $user
     * @return int
     */
    private function countUserProjects(User $user): int
    {
        return $user->profile->projects()->count();
    }

    /**
     * Count total tasks assigned to a user profile.
     * 
     * @param User $user
     * @return int
     */
    private function countUserTasks(User $user): int
    {
        return $user->profile->assignedTasks()->count();
    }

    /**
     * Count completed tasks for a user profile.
     * 
     * @param User $user
     * @return int
     */
    private function countUserCompletedTasks(User $user): int
    {
        return $user->profile->assignedTasks()
            ->where('status', 'completada')
            ->count();
    }

    /**
     * Count innovations submitted by a user profile.
     * 
     * @param User $user
     * @return int
     */
    private function countUserInnovations(User $user): int
    {
        return $user->profile->innovations()->count();
    }

    /**
     * Return a set of zeroed statistics for users without a profile.
     * 
     * @return array
     */
    private function getEmptyStats(): array
    {
        return [
            'projects'        => 0, 
            'tasks'           => 0, 
            'completed_tasks' => 0, 
            'innovations'     => 0
        ];
    }
}
