<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\Innovation;
use App\Models\User;
use Carbon\Carbon;

/**
 * Service to generate high-level administrative reports and statistics.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class ReportService
{
    /**
     * Aggregate system-wide statistics for the main dashboard.
     * 
     * @return array<string, int>
     */
    public function getDashboardStats(): array
    {
        return [
            'total_projects'        => $this->getTotalProjectsCount(),
            'active_projects'       => $this->getActiveProjectsCount(),
            'finished_projects'     => $this->getFinishedProjectsCount(),
            'at_risk_projects'      => $this->getAtRiskProjectsCount(),
            'total_tasks'           => $this->getTotalTasksCount(),
            'pending_tasks'         => $this->getPendingTasksCount(),
            'overdue_tasks'         => $this->getOverdueTasksCount(),
            'total_innovations'     => $this->getTotalInnovationsCount(),
            'completed_innovations' => $this->getCompletedInnovationsCount(),
        ];
    }

    /**
     * Retrieve participation metrics for teachers and other user roles.
     * 
     * @return array{teachers: \Illuminate\Support\Collection<int, User>, otherUsers: \Illuminate\Support\Collection<int, User>}
     */
    public function getTeacherParticipation(): array
    {
        $teachers = $this->getTeachersWithMetrics();

        return [
            'teachers'   => $teachers,
            'otherUsers' => $this->getExclucedUsers($teachers)
        ];
    }

    /**
     * Fetch teachers and admins with their task and comment activity counts.
     * 
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function getTeachersWithMetrics()
    {
        return User::whereHas('roles', function($q) {
                /** @phpstan-ignore-next-line */
                $q->where('name', 'LIKE', '%docente%');
                /** @phpstan-ignore-next-line */
                $q->orWhere('name', 'LIKE', '%admin%');
            })
            ->with(['roles', 'profile' => function($q) {
                $q->withCount(['assignedTasks', 'comments', 'innovations']);
            }])
            ->get()
            ->map(function($user) {
                return $this->attachMetricCounts($user);
            })
            ->sortByDesc('assigned_tasks_count');
    }

    /**
     * Attach activity counters to a user object.
     * 
     * @param User $user
     * @return User
     */
    private function attachMetricCounts(User $user)
    {
        $user->setAttribute('assigned_tasks_count', $user->profile->assigned_tasks_count ?? 0);
        $user->setAttribute('comments_count', $user->profile->comments_count ?? 0);
        $user->setAttribute('innovations_count', $user->profile->innovations_count ?? 0);
        
        return $user;
    }

    /**
     * Get users not included in the teacher participation list.
     * 
     * @param \Illuminate\Support\Collection<int, User> $teachers
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function getExclucedUsers($teachers)
    {
        return User::whereNotIn('id', $teachers->pluck('id'))
            ->with('roles')
            ->get();
    }

    /**
     * @return int Total projects in the system.
     */
    private function getTotalProjectsCount(): int
    {
        return Project::count();
    }

    /**
     * @return int Projects that are currently in progress.
     */
    private function getActiveProjectsCount(): int
    {
        return Project::query()->where('status', 'en_progreso')->count();
    }

    /**
     * @return int Projects that have reached completion.
     */
    private function getFinishedProjectsCount(): int
    {
        return Project::finished()->count();
    }

    /**
     * @return int Projects marked as "at risk".
     */
    private function getAtRiskProjectsCount(): int
    {
        return Project::atRisk()->count();
    }

    /**
     * @return int Total tasks across all projects.
     */
    private function getTotalTasksCount(): int
    {
        return Task::count();
    }

    /**
     * @return int Tasks still awaiting completion.
     */
    private function getPendingTasksCount(): int
    {
        return Task::pending()->count();
    }

    /**
     * @return int Tasks that have passed their due date.
     */
    private function getOverdueTasksCount(): int
    {
        return Task::overdue()->count();
    }

    /**
     * @return int Total innovation initiatives.
     */
    private function getTotalInnovationsCount(): int
    {
        return Innovation::count();
    }

    /**
     * @return int Innovations that are fully reviewed or completed.
     */
    private function getCompletedInnovationsCount(): int
    {
        return Innovation::completed()->count();
    }
}
