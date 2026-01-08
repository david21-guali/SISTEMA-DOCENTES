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
    protected \App\Services\ReportService $reportService;
    protected \App\Services\ComparativeReportService $comparativeService;

    public function __construct(
        \App\Services\ReportService $reportService,
        \App\Services\ComparativeReportService $comparativeService
    ) {
        $this->reportService = $reportService;
        $this->comparativeService = $comparativeService;
    }

    /**
     * Display reports dashboard.
     */
    /**
     * Display reports dashboard.
     */
    public function index(): \Illuminate\View\View
    {
        $stats = $this->reportService->getDashboardStats();
        return view('app.back.reports.index', compact('stats'));
    }

    /**
     * Export projects to PDF.
     */
    /**
     * Export projects to PDF.
     */
    public function projectsPdf(Request $request): \Illuminate\Http\Response
    {
        $projects = $this->getFilteredProjects($request);
        
        $stats = [
            'total' => $projects->count(),
            'active' => $projects->where('status', 'en_progreso')->count(),
            'finished' => $projects->where('status', 'completado')->count(),
            'pending' => $projects->where('status', 'planificacion')->count(),
            'total_budget' => $projects->sum('budget'),
            'avg_completion' => $projects->avg('completion_percentage') ?? 0,
        ];

        // Calculamos porcentajes para el gráfico visual (mínimo 1% si hay datos para que sea visible)
        $total = $stats['total'] > 0 ? $stats['total'] : 1;
        $stats['pct_pending'] = ($stats['pending'] / $total) * 100;
        $stats['pct_active'] = ($stats['active'] / $total) * 100;
        $stats['pct_finished'] = ($stats['finished'] / $total) * 100;

        $pdf = Pdf::loadView('app.back.reports.projects-pdf', compact('projects', 'stats'));
        
        return $pdf->download('proyectos-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export projects to Excel.
     */
    /**
     * Export projects to Excel.
     */
    public function projectsExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new ProjectsExport($request->all()), 'proyectos-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export tasks to PDF.
     */
    /**
     * Export tasks to PDF.
     */
    public function tasksPdf(Request $request): \Illuminate\Http\Response
    {
        $tasks = $this->getFilteredTasks($request);
        
        $stats = [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', 'pendiente')->count(),
            'completed' => $tasks->where('status', 'completada')->count(),
            'atrasada' => $tasks->where('status', 'atrasada')->count(),
            'avg_compliance' => $tasks->count() > 0 ? ($tasks->where('status', 'completada')->count() / $tasks->count()) * 100 : 0,
        ];

        // Porcentajes por prioridad para el gráfico
        $total = $stats['total'] > 0 ? $stats['total'] : 1;
        $stats['pct_alta'] = ($tasks->where('priority', 'alta')->count() / $total) * 100;
        $stats['pct_media'] = ($tasks->where('priority', 'media')->count() / $total) * 100;
        $stats['pct_baja'] = ($tasks->where('priority', 'baja')->count() / $total) * 100;

        $pdf = Pdf::loadView('app.back.reports.tasks-pdf', compact('tasks', 'stats'));

        return $pdf->download('tareas-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export tasks to Excel.
     */
    /**
     * Export tasks to Excel.
     */
    public function tasksExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new TasksExport($request->all()), 'tareas-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export innovations to PDF.
     */
    /**
     * Export innovations to PDF.
     */
    public function innovationsPdf(Request $request): \Illuminate\Http\Response
    {
        $innovations = $this->getFilteredInnovations($request);
        
        $stats = [
            'total' => $innovations->count(),
            'avg_impact' => $innovations->avg('impact_score') ?? 0,
            'best_impact' => $innovations->max('impact_score') ?? 0,
            'with_evidence' => $innovations->whereNotNull('evidence_files')->count(),
        ];

        // Segmentación por impacto para gráfico
        $total = $stats['total'] > 0 ? $stats['total'] : 1;
        $stats['pct_high'] = ($innovations->where('impact_score', '>=', 8)->count() / $total) * 100;
        $stats['pct_mid'] = ($innovations->whereBetween('impact_score', [5, 7.9])->count() / $total) * 100;
        $stats['pct_low'] = ($innovations->where('impact_score', '<', 5)->count() / $total) * 100;

        $pdf = Pdf::loadView('app.back.reports.innovations-pdf', compact('innovations', 'stats'));

        return $pdf->download('innovaciones-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export innovations to Excel.
     */
    /**
     * Export innovations to Excel.
     */
    public function innovationsExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new InnovationsExport($request->all()), 'innovaciones-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Apply project filters for PDF export.
     * 
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    /**
     * Apply project filters for PDF export.
     * @return \Illuminate\Support\Collection<int, Project>
     */
    private function getFilteredProjects(Request $request): \Illuminate\Support\Collection
    {
        $query = Project::with(['category', 'profile.user'])
            ->forUser(auth()->user());

        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        return $query->get();
    }

    /**
     * Apply task filters for PDF export.
     * 
     * @param Request $request
     * @return \Illuminate\Support\Collection<int, Task>
     */
    private function getFilteredTasks(Request $request): \Illuminate\Support\Collection
    {
        $query = Task::with(['project', 'assignedProfile.user'])
            ->forUser(auth()->user());
        
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return $query->get();
    }

    /**
     * Apply innovation filters for PDF export.
     * 
     * @param Request $request
     * @return \Illuminate\Support\Collection<int, Innovation>
     */
    private function getFilteredInnovations(Request $request): \Illuminate\Support\Collection
    {
        $query = Innovation::with(['profile.user', 'innovationType'])
            ->forUser(auth()->user());
        
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return $query->get();
    }

    public function teacherParticipation(): \Illuminate\View\View
    {
        // Solo para Admin
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Acceso denegado. Este reporte es exclusivo para administradores.');
        }

        $data = $this->reportService->getTeacherParticipation();
        return view('app.back.reports.participation', $data);
    }

    /**
     * Display comparative reports between two periods.
     */
    /**
     * Display comparative reports between two periods.
     */
    public function comparative(Request $request): \Illuminate\View\View
    {
        // Solo para Admin y Coordinador
        if (!auth()->user()->hasRole(['admin', 'coordinador'])) {
            abort(403, 'Acceso denegado. Este reporte no está disponible para su rol.');
        }

        $periods = $this->getComparisonPeriods($request);
        $data = $this->comparativeService->getComparativeData($periods);

        return view('app.back.reports.comparative', array_merge($periods, $data));
    }

    /**
     * Export comparative report to PDF.
     */
    public function comparativePdf(Request $request): \Illuminate\Http\Response
    {
        $periods = $this->getComparisonPeriods($request);
        $data = $this->comparativeService->getComparativeData($periods);
        
        $pdf = Pdf::loadView('app.back.reports.comparative-pdf', array_merge($periods, $data))
            ->setPaper('a4', 'landscape'); // Landscape to better fit comparative columns
        
        return $pdf->download('reporte-comparativo-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export comparative report to Excel.
     */
    public function comparativeExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $periods = $this->getComparisonPeriods($request);
        return Excel::download(new \App\Exports\ComparativeExport($periods), 'reporte-comparativo-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Helper to get and validate comparison periods.
     * 
     * @param Request $request
     * @return array{period1Start: string, period1End: string, period2Start: string, period2End: string}
     */
    private function getComparisonPeriods(Request $request): array
    {
        $request->validate([
            'period1_start' => 'nullable|date',
            'period1_end'   => 'nullable|date|after_or_equal:period1_start',
            'period2_start' => 'nullable|date',
            'period2_end'   => 'nullable|date|after_or_equal:period2_start',
        ]);

        return [
            'period1Start' => $request->period1_start ?? now()->subMonths(2)->startOfMonth()->format('Y-m-d'),
            'period1End'   => $request->period1_end ?? now()->subMonth()->endOfMonth()->format('Y-m-d'),
            'period2Start' => $request->period2_start ?? now()->startOfMonth()->format('Y-m-d'),
            'period2End'   => $request->period2_end ?? now()->format('Y-m-d'),
        ];
    }
}
