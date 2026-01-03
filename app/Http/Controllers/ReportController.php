<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Innovation;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjectsExport;
use App\Exports\TasksExport;
use App\Exports\InnovationsExport;

class ReportController extends Controller
{
    /**
     * Display reports dashboard.
     */
    public function index()
    {
        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::active()->count(),
            'finished_projects' => Project::finished()->count(),
            'at_risk_projects' => Project::atRisk()->count(),
            'total_tasks' => Task::count(),
            'pending_tasks' => Task::pending()->count(),
            'overdue_tasks' => Task::overdue()->count(),
            'total_innovations' => Innovation::count(),
            'completed_innovations' => Innovation::completed()->count(),
        ];

        return view('app.back.reports.index', compact('stats'));
    }

    /**
     * Export projects to PDF.
     */
    public function projectsPdf(Request $request)
    {
        $query = Project::with(['category', 'profile.user']);

        // Filtros opcionales
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $projects = $query->get();

        $pdf = Pdf::loadView('app.back.reports.projects-pdf', compact('projects'));
        return $pdf->download('proyectos-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export projects to Excel.
     */
    public function projectsExcel(Request $request)
    {
        return Excel::download(new ProjectsExport($request->all()), 'proyectos-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export tasks to PDF.
     */
    public function tasksPdf(Request $request)
    {
        $query = Task::with(['project', 'assignedProfile.user']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $tasks = $query->get();

        $pdf = Pdf::loadView('reports.tasks-pdf', compact('tasks'));
        return $pdf->download('tareas-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export tasks to Excel.
     */
    public function tasksExcel(Request $request)
    {
        return Excel::download(new TasksExport($request->all()), 'tareas-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export innovations to PDF.
     */
    public function innovationsPdf(Request $request)
    {
        $query = Innovation::with(['profile.user', 'innovationType']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $innovations = $query->get();

        $pdf = Pdf::loadView('reports.innovations-pdf', compact('innovations'));
        return $pdf->download('innovaciones-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export innovations to Excel.
     */
    public function innovationsExcel(Request $request)
    {
        return Excel::download(new InnovationsExport($request->all()), 'innovaciones-' . date('Y-m-d') . '.xlsx');
    }

    public function teacherParticipation()
    {
        // 1. Usuarios que SI son docentes (o admins)
        $teachers = User::whereHas('roles', function($q) {
                $q->where('name', 'LIKE', '%docente%')
                  ->orWhere('name', 'LIKE', '%admin%');
            })
            ->with(['roles', 'profile.assignedTasks', 'profile.comments'])
            ->get()
            ->map(function($user) {
                // Store counts in custom attributes (not readonly properties)
                /** @phpstan-ignore-next-line */
                $user->tasks_count = $user->profile ? $user->profile->assignedTasks->count() : 0;
                /** @phpstan-ignore-next-line */
                $user->user_comments_count = $user->profile ? $user->profile->comments->count() : 0;
                return $user;
            })
            ->sortByDesc('tasks_count');

        // 2. Otros usuarios (para detectar por qué no salen)
        $teacherIds = $teachers->pluck('id');
        $otherUsers = User::whereNotIn('id', $teacherIds)
            ->with('roles')
            ->get();

        return view('app.back.reports.participation', compact('teachers', 'otherUsers'));
    }

    /**
     * Display comparative reports between two periods.
     */
    public function comparative(Request $request)
    {
        // Períodos predeterminados: último mes vs mes anterior
        $period1Start = $request->period1_start ?? now()->subMonths(2)->startOfMonth()->format('Y-m-d');
        $period1End = $request->period1_end ?? now()->subMonth()->endOfMonth()->format('Y-m-d');
        $period2Start = $request->period2_start ?? now()->startOfMonth()->format('Y-m-d');
        $period2End = $request->period2_end ?? now()->format('Y-m-d');

        // Helper para calcular estadísticas por período
        $getStats = function ($startDate, $endDate) {
            return [
                // Proyectos
                'projects_created' => Project::whereBetween('created_at', [$startDate, $endDate])->count(),
                'projects_finished' => Project::where('status', 'finalizado')
                    ->whereBetween('updated_at', [$startDate, $endDate])->count(),
                'projects_active' => Project::where('status', 'en_progreso')
                    ->whereBetween('created_at', [$startDate, $endDate])->count(),
                'projects_at_risk' => Project::where('status', 'en_riesgo')
                    ->whereBetween('created_at', [$startDate, $endDate])->count(),
                
                // Tareas
                'tasks_created' => Task::whereBetween('created_at', [$startDate, $endDate])->count(),
                'tasks_completed' => Task::where('status', 'completada')
                    ->whereBetween('completion_date', [$startDate, $endDate])->count(),
                'tasks_overdue' => Task::where('status', '!=', 'completada')
                    ->where('due_date', '<', now())
                    ->whereBetween('created_at', [$startDate, $endDate])->count(),
                
                // Innovaciones
                'innovations_created' => Innovation::whereBetween('created_at', [$startDate, $endDate])->count(),
                'innovations_completed' => Innovation::where('status', 'completada')
                    ->whereBetween('updated_at', [$startDate, $endDate])->count(),
                'avg_impact_score' => Innovation::whereBetween('created_at', [$startDate, $endDate])
                    ->whereNotNull('impact_score')->avg('impact_score') ?? 0,
            ];
        };

        // Calcular estadísticas para ambos períodos
        $period1Stats = $getStats($period1Start, $period1End);
        $period2Stats = $getStats($period2Start, $period2End);

        // Calcular cambios porcentuales
        $calculateChange = function ($old, $new) {
            if ($old == 0) return $new > 0 ? 100 : 0;
            return round((($new - $old) / $old) * 100, 1);
        };

        $changes = [];
        foreach ($period1Stats as $key => $value) {
            $changes[$key] = $calculateChange($value, $period2Stats[$key]);
        }

        // Datos para gráficos: proyectos por mes (últimos 12 meses)
        $projectsByMonth = [];
        $tasksByMonth = [];
        $innovationsByMonth = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $monthLabel = $monthStart->translatedFormat('M Y');
            
            $projectsByMonth[$monthLabel] = Project::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $tasksByMonth[$monthLabel] = Task::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $innovationsByMonth[$monthLabel] = Innovation::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        }

        return view('app.back.reports.comparative', compact(
            'period1Start', 'period1End', 'period2Start', 'period2End',
            'period1Stats', 'period2Stats', 'changes',
            'projectsByMonth', 'tasksByMonth', 'innovationsByMonth'
        ));
    }
}
