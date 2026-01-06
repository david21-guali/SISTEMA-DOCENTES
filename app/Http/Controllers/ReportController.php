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
    protected $reportService;
    protected $comparativeService;

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
    public function index()
    {
        $stats = $this->reportService->getDashboardStats();
        return view('app.back.reports.index', compact('stats'));
    }

    /**
     * Export projects to PDF.
     */
    public function projectsPdf(Request $request)
    {
        $projects = $this->getFilteredProjects($request);
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
        $tasks = $this->getFilteredTasks($request);
        $pdf = Pdf::loadView('app.back.reports.tasks-pdf', compact('tasks'));

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
        $innovations = $this->getFilteredInnovations($request);
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

    /**
     * Apply project filters for PDF export.
     * 
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    private function getFilteredProjects(Request $request)
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
    private function getFilteredTasks(Request $request)
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
    private function getFilteredInnovations(Request $request)
    {
        $query = Innovation::with(['profile.user', 'innovationType']);
        
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return $query->get();
    }

    public function teacherParticipation()
    {
        $data = $this->reportService->getTeacherParticipation();
        return view('app.back.reports.participation', $data);
    }

    /**
     * Display comparative reports between two periods.
     */
    public function comparative(Request $request)
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
