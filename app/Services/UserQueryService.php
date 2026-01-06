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
     * Retrieve paginated users based on filtering criteria.
     * 
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, User>
     */
    public function getUsers(array $filters, int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        /** @var \Illuminate\Database\Eloquent\Builder<\App\Models\User> $query */
        $query = User::with('roles');

        $this->applyRoleFilter($query, $filters['role'] ?? null);
        $this->applySearchFilter($query, $filters['search'] ?? null);

        return $query->orderBy('name')->paginate($perPage);
    }

    /**
     * Get statistics about user roles and statuses.
     * 
     * @return array<string, int>
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
     * Get all available roles.
     * 
     * @return \Illuminate\Support\Collection<int, \App\Models\Role>
     */
    public function getRoles(): \Illuminate\Support\Collection
    {
        return \App\Models\Role::orderBy('name')->get();
    }

    /**
     * Get detailed project/task stats for a specific user profile.
     * 
     * @param User $user
     * @return array<string, int>
     */
    public function getUserProfileStats(User $user): array
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
     * Filter query by role name.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<User> $query
     * @param string|null $role
     * @return void
     */
    private function applyRoleFilter($query, ?string $role): void
    {
        if (!empty($role)) {
            /** @phpstan-ignore-next-line */
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }
    }

    /**
     * Filter query by search term (name or email).
     * 
     * @param \Illuminate\Database\Eloquent\Builder<User> $query
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
        return User::whereHas('roles', function($q) use ($role) {
            /** @phpstan-ignore-next-line */
            $q->where('name', $role);
        })->count();
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
     * Return zeroed stats structure.
     * 
     * @return array<string, int>
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
