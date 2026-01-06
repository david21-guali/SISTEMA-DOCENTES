<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\Innovation;
use App\Models\Meeting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Service to aggregate business intelligence metrics and timeline data.
 * Designed for High Maintainability Index (MI >= 65).
 */
class DashboardService
{
    /**
     * Aggregate system-wide statistics for the main dashboard.
     * 
     * @return array<string, int>
     */
    public function getGeneralStats(): array
    {
        return [
            'total_projects'        => Project::count(),
            'active_projects'       => Project::active()->count(),
            'finished_projects'     => Project::finished()->count(),
            'at_risk_projects'      => Project::atRisk()->count(),
            'total_tasks'           => Task::count(),
            'pending_tasks'         => Task::pending()->count(),
            'completed_tasks'       => Task::completed()->count(),
            'overdue_tasks'         => Task::overdue()->count(),
            'total_innovations'     => Innovation::count(),
            'completed_innovations' => Innovation::completed()->count(),
        ];
    }

    /**
     * Fetch project distribution grouped by categories.
     * 
     * @return Collection<string, mixed>
     */
    public function getProjectsByCategory(): Collection
    {
        return Project::select('categories.name', DB::raw('count(*) as total'))
            ->join('categories', 'projects.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->toBase()
            ->pluck('total', 'name');
    }


    /**
     * Get monthly project creation stats for the current year.
     * Handles SQL differences between SQLite and MySQL.
     * 
     * @return array<int, int>
     */
    public function getProjectsByMonth(): array
    {
        $isSqlite = DB::getDriverName() === 'sqlite';
        $expression = $isSqlite ? "strftime('%m', created_at)" : "MONTH(created_at)";

        $monthlyData = Project::select(
                DB::raw("CAST($expression AS UNSIGNED) as month"),
                DB::raw('count(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->toBase()
            ->pluck('total', 'month')
            ->toArray();
        
        return $this->fillMonthlySeries($monthlyData);
    }

    /**
     * Aggregate recent activities from projects, tasks, and meetings.
     * 
     * @return Collection<int, array{type: string, icon: string, color: string, title: string, description: string, user: string, date: mixed}>
     */
    public function getActivityTimeline(): Collection
    {
        $activities = new Collection();

        $activities = $activities->concat($this->getRecentProjectsActivity());
        $activities = $activities->concat($this->getCompletedTasksActivity());
        $activities = $activities->concat($this->getUpcomingMeetingsActivity());

        /** @phpstan-ignore-next-line */
        return $activities->sortByDesc('date')
            ->take(10)
            ->values();
    }

    /**
     * Helper to ensure all 12 months have a value (even 0).
     * 
     * @param array<int|string, mixed> $data
     * @return array<int, mixed>
     */
    private function fillMonthlySeries(array $data): array
    {
        $series = [];
        for ($i = 1; $i <= 12; $i++) {
            $series[] = $data[$i] ?? 0;
        }
        return $series;
    }

    /**
     * Get recent project creations.
     * 
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function getRecentProjectsActivity(): Collection
    {
        /** @phpstan-ignore-next-line */
        return Project::with('profile.user')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($project) => [
                'type'        => 'project',
                'icon'        => 'fas fa-folder-plus',
                'color'       => 'primary',
                'title'       => 'Nuevo proyecto',
                'description' => $project->title,
                'user'        => $project->profile->user->name ?? 'Sistema',
                'date'        => $project->created_at,
            ]);
    }

    /**
     * Fetch tasks completed in the last 7 days.
     * 
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function getCompletedTasksActivity(): Collection
    {
        /** @phpstan-ignore-next-line */
        return Task::with(['project', 'assignedProfile.user'])
            ->where('status', 'completada')
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn($task) => [
                'type'        => 'task',
                'icon'        => 'fas fa-check-circle',
                'color'       => 'success',
                'title'       => 'Tarea completada',
                'description' => $task->title,
                'user'        => $task->assignedProfile->user->name ?? 'Sin asignar',
                'date'        => $task->updated_at,
            ]);
    }

    /**
     * Fetch meetings scheduled for the next 7 days.
     * 
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function getUpcomingMeetingsActivity(): Collection
    {
        /** @phpstan-ignore-next-line */
        return Meeting::with('creator.user')
            ->where('meeting_date', '>=', Carbon::now())
            ->where('meeting_date', '<=', Carbon::now()->addDays(7))
            ->orderBy('meeting_date')
            ->take(5)
            ->get()
            ->map(fn($meeting) => [
                'type'        => 'meeting',
                'icon'        => 'fas fa-calendar-check',
                'color'       => 'info',
                'title'       => 'Reunión programada',
                'description' => $meeting->title,
                'user'        => $meeting->creator->user->name ?? 'Sistema',
                'date'        => $meeting->meeting_date,
            ]);
    }
}
