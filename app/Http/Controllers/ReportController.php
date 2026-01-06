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
        $pdf = Pdf::loadView('app.back.reports.projects-pdf', compact('projects'));
        
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
        $pdf = Pdf::loadView('app.back.reports.tasks-pdf', compact('tasks'));

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
        $pdf = Pdf::loadView('reports.innovations-pdf', compact('innovations'));

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
     * 
     * @param Request $request
     * @return \Illuminate\Support\Collection<int, Project>
     */
    private function getFilteredProjects(Request $request): \Illuminate\Support\Collection
    {
        $query = Project::with(['category', 'profile.user']);

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
     * @return \Illuminate\Support\Collection
     */
    /**
     * Apply task filters for PDF export.
     * 
     * @param Request $request
     * @return \Illuminate\Support\Collection<int, Task>
     */
    private function getFilteredTasks(Request $request): \Illuminate\Support\Collection
    {
        $query = Task::with(['project', 'assignedProfile.user']);
        
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return $query->get();
    }

    /**
     * Apply innovation filters for PDF export.
     * 
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    /**
     * Apply innovation filters for PDF export.
     * 
     * @param Request $request
     * @return \Illuminate\Support\Collection<int, Innovation>
     */
    private function getFilteredInnovations(Request $request): \Illuminate\Support\Collection
    {
        $query = Innovation::with(['profile.user', 'innovationType']);
        
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return $query->get();
    }

    public function teacherParticipation(): \Illuminate\View\View
    {
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
        $periods = [
            'period1Start' => $request->period1_start ?? now()->subMonths(2)->startOfMonth()->format('Y-m-d'),
            'period1End' => $request->period1_end ?? now()->subMonth()->endOfMonth()->format('Y-m-d'),
            'period2Start' => $request->period2_start ?? now()->startOfMonth()->format('Y-m-d'),
            'period2End' => $request->period2_end ?? now()->format('Y-m-d'),
        ];

        $data = $this->comparativeService->getComparativeData($periods);

        return view('app.back.reports.comparative', array_merge($periods, $data));
    }
}
