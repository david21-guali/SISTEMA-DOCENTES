<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Innovation;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::active()->count(),
            'finished_projects' => Project::finished()->count(),
            'at_risk_projects' => Project::atRisk()->count(),
            'total_tasks' => Task::count(),
            'pending_tasks' => Task::pending()->count(),
            'completed_tasks' => Task::completed()->count(),
            'overdue_tasks' => Task::overdue()->count(),
            'total_innovations' => Innovation::count(),
            'completed_innovations' => Innovation::completed()->count(),
        ];

        // Proyectos por categoría (para gráfico)
        $projectsByCategory = Project::select('categories.name', DB::raw('count(*) as total'))
            ->join('categories', 'projects.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->pluck('total', 'name');

        // Proyectos por mes (últimos 6 meses)
        // Proyectos por mes (Año actual completo)
        $monthlyData = Project::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
        
        $projectsByMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            $projectsByMonth[] = $monthlyData[$i] ?? 0;
        }

        // Proyectos recientes
        $recentProjects = Project::with(['category', 'profile.user'])
            ->latest()
            ->take(5)
            ->get();

        // Notificaciones recientes
        $notifications = auth()->user()->unreadNotifications->take(5);

        // Datos para gráfico de tareas
        $taskStats = [
            'pending' => $stats['pending_tasks'],
            'completed' => $stats['completed_tasks'],
            'overdue' => $stats['overdue_tasks'],
        ];

        // Línea de tiempo de actividad global (últimos 10 eventos)
        $activityTimeline = collect();

        // Proyectos creados recientemente
        $recentProjectsActivity = Project::with('profile.user')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project',
                    'icon' => 'fas fa-folder-plus',
                    'color' => 'primary',
                    'title' => 'Nuevo proyecto creado',
                    'description' => $project->title,
                    'user' => $project->profile->user->name ?? 'Sistema',
                    'date' => $project->created_at,
                ];
            });

        // Tareas completadas recientemente
        $completedTasksActivity = Task::with(['project', 'assignedProfile.user'])
            ->where('status', 'completada')
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($task) {
                return [
                    'type' => 'task',
                    'icon' => 'fas fa-check-circle',
                    'color' => 'success',
                    'title' => 'Tarea completada',
                    'description' => $task->title,
                    'user' => $task->assignedProfile->user->name ?? 'Sin asignar',
                    'date' => $task->updated_at,
                ];
            });

        // Reuniones programadas próximas
        $upcomingMeetingsActivity = Meeting::with('creator')
            ->where('meeting_date', '>=', Carbon::now())
            ->where('meeting_date', '<=', Carbon::now()->addDays(7))
            ->orderBy('meeting_date')
            ->take(5)
            ->get()
            ->map(function ($meeting) {
                return [
                    'type' => 'meeting',
                    'icon' => 'fas fa-calendar-check',
                    'color' => 'info',
                    'title' => 'Reunión programada',
                    'description' => $meeting->title,
                    'user' => $meeting->creator->name ?? 'Sistema',
                    'date' => $meeting->meeting_date,
                ];
            });

        // Combinar y ordenar por fecha
        $activityTimeline = $recentProjectsActivity
            ->concat($completedTasksActivity)
            ->concat($upcomingMeetingsActivity)
            ->sortByDesc('date')
            ->take(10)
            ->values();

        return view('app.back.dashboard', compact(
            'stats', 
            'projectsByCategory', 
            'projectsByMonth', 
            'recentProjects', 
            'notifications',
            'taskStats',
            'activityTimeline'
        ));
    }
}
