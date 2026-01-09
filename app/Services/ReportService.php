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
     * Get projects filtered by request parameters.
     * 
     * @param array<string, mixed> $filters
     * @return \Illuminate\Support\Collection<int, Project>
     */
    public function getFilteredProjects(array $filters): \Illuminate\Support\Collection
    {
        return Project::with(['category', 'profile.user'])
            ->forUser(auth()->user())
            ->when(data_get($filters, 'status'), fn($q, $s) => $q->where('status', $s))
            ->when(data_get($filters, 'category_id'), fn($q, $c) => $q->where('category_id', $c))
            ->get();
    }

    /**
     * Get tasks filtered by request parameters.
     * 
     * @param array<string, mixed> $filters
     * @return \Illuminate\Support\Collection<int, Task>
     */
    public function getFilteredTasks(array $filters): \Illuminate\Support\Collection
    {
        return Task::with(['project', 'assignedProfile.user'])
            ->forUser(auth()->user())
            ->when(data_get($filters, 'status'), fn($q, $s) => $q->where('status', $s))
            ->get();
    }

    /**
     * Get innovations filtered by request parameters.
     * 
     * @param array<string, mixed> $filters
     * @return \Illuminate\Support\Collection<int, Innovation>
     */
    public function getFilteredInnovations(array $filters): \Illuminate\Support\Collection
    {
        return Innovation::with(['profile.user', 'innovationType'])
            ->forUser(auth()->user())
            ->when(data_get($filters, 'status'), fn($q, $s) => $q->where('status', $s))
            ->get();
    }

    /**
     * Aggregate system-wide statistics for the main dashboard.
     * 
     * @return array<string, int>
     */
    public function getDashboardStats(): array
    {
        return [
            'total_projects'        => Project::count(),
            'active_projects'       => Project::where('status', 'en_progreso')->count(),
            'finished_projects'     => Project::finished()->count(),
            'at_risk_projects'      => Project::atRisk()->count(),
            'total_tasks'           => Task::count(),
            'pending_tasks'         => Task::pending()->count(),
            'overdue_tasks'         => Task::overdue()->count(),
            'total_innovations'     => Innovation::count(),
            'completed_innovations' => Innovation::completed()->count(),
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
     * Calculate summary statistics for a collection of projects.
     * 
     * @param \Illuminate\Support\Collection<int, Project> $items
     * @return array<string, int|float>
     */
    public function getProjectsStats(\Illuminate\Support\Collection $items): array
    {
        $total = $items->count() ?: 1;
        
        return [
            'total'          => $items->count(),
            'active'         => ($active = $items->where('status', 'en_progreso')->count()),
            'finished'       => ($finished = $items->where('status', 'completado')->count()),
            'pending'        => ($pending = $items->where('status', 'planificacion')->count()),
            'total_budget'   => $items->sum('budget'),
            'avg_completion' => $items->avg('completion_percentage') ?? 0,
            'pct_pending'    => ($pending / $total) * 100,
            'pct_active'     => ($active / $total) * 100,
            'pct_finished'   => ($finished / $total) * 100,
        ];
    }

    /**
     * Calculate summary statistics for a collection of tasks.
     * 
     * @param \Illuminate\Support\Collection<int, Task> $items
     * @return array<string, int|float>
     */
    public function getTasksStats(\Illuminate\Support\Collection $items): array
    {
        $total = $items->count() ?: 1;

        return [
            'total'          => $items->count(),
            'pending'        => $items->where('status', 'pendiente')->count(),
            'completed'      => ($completed = $items->where('status', 'completada')->count()),
            'atrasada'       => $items->where('status', 'atrasada')->count(),
            'avg_compliance' => ($completed / $total) * 100,
            'pct_alta'       => ($items->where('priority', 'alta')->count() / $total) * 100,
            'pct_media'      => ($items->where('priority', 'media')->count() / $total) * 100,
            'pct_baja'       => ($items->where('priority', 'baja')->count() / $total) * 100,
        ];
    }

    /**
     * Calculate summary statistics for a collection of innovations.
     * 
     * @param \Illuminate\Support\Collection<int, Innovation> $items
     * @return array<string, int|float>
     */
    public function getInnovationsStats(\Illuminate\Support\Collection $items): array
    {
        $total = $items->count() ?: 1;

        return [
            'total'         => $items->count(),
            'avg_impact'    => $items->avg('impact_score') ?? 0,
            'best_impact'   => $items->max('impact_score') ?? 0,
            'with_evidence' => $items->whereNotNull('evidence_files')->count(),
            'pct_high'      => ($items->where('impact_score', '>=', 8)->count() / $total) * 100,
            'pct_mid'       => ($items->whereBetween('impact_score', [5, 7.9])->count() / $total) * 100,
            'pct_low'       => ($items->where('impact_score', '<', 5)->count() / $total) * 100,
        ];
    }
}
