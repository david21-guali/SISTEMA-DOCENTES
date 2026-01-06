<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Innovation;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Controller to handle the main administrative dashboard.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class DashboardController extends Controller
{
    /**
     * @var \App\Services\DashboardService Service for dashboard data aggregation.
     */
    protected $dashboardService;

    /**
     * DashboardController constructor.
     * 
     * @param \App\Services\DashboardService $dashboardService
     */
    public function __construct(\App\Services\DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the dashboard view with all relevant statistics and metrics.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats             = $this->dashboardService->getGeneralStats();
        $activityTimeline  = $this->dashboardService->getActivityTimeline();
        $projectsByCategory = $this->dashboardService->getProjectsByCategory();
        $projectsByMonth   = $this->dashboardService->getProjectsByMonth();
        
        return view('app.back.dashboard', [
            'stats'              => $stats,
            'projectsByCategory' => $projectsByCategory,
            'projectsByMonth'    => $projectsByMonth,
            'recentProjects'     => $this->getRecentProjects(),
            'notifications'      => $this->getLatestNotifications(),
            'taskStats'          => $this->formatTaskStats($stats),
            'activityTimeline'   => $activityTimeline
        ]);
    }

    /**
     * Retrieve a small set of the most recently created projects.
     * 
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project>
     */
    private function getRecentProjects()
    {
        return Project::with(['category', 'profile.user'])
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * Fetch the most recent unread notifications for the authenticated user.
     * 
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Notifications\DatabaseNotification>
     */
    private function getLatestNotifications()
    {
        return auth()->user()->unreadNotifications->take(5);
    }

    /**
     * Extract task-specific statistics from the general stats array.
     * 
     * @param array $stats
     * @return array
     */
    private function formatTaskStats(array $stats): array
    {
        return [
            'pending'   => $stats['pending_tasks'],
            'completed' => $stats['completed_tasks'],
            'overdue'   => $stats['overdue_tasks'],
        ];
    }
}
