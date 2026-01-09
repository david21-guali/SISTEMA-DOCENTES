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
    public function __construct(
        protected \App\Services\ReportService $reportService,
        protected \App\Services\ComparativeReportService $comparativeService
    ) {}

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

    public function projectsPdf(Request $request): \Illuminate\Http\Response
    {
        $projects = $this->reportService->getFilteredProjects($request->all());
        return Pdf::loadView('app.back.reports.projects-pdf', ['projects' => $projects, 'stats' => $this->reportService->getProjectsStats($projects)])
            ->download('proyectos-' . date('Y-m-d') . '.pdf');
    }

    public function projectsExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new ProjectsExport($request->all()), 'proyectos-' . date('Y-m-d') . '.xlsx');
    }

    public function tasksPdf(Request $request): \Illuminate\Http\Response
    {
        $tasks = $this->reportService->getFilteredTasks($request->all());
        return Pdf::loadView('app.back.reports.tasks-pdf', ['tasks' => $tasks, 'stats' => $this->reportService->getTasksStats($tasks)])
            ->download('tareas-' . date('Y-m-d') . '.pdf');
    }

    public function tasksExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new TasksExport($request->all()), 'tareas-' . date('Y-m-d') . '.xlsx');
    }

    public function innovationsPdf(Request $request): \Illuminate\Http\Response
    {
        $innovations = $this->reportService->getFilteredInnovations($request->all());
        return Pdf::loadView('app.back.reports.innovations-pdf', ['innovations' => $innovations, 'stats' => $this->reportService->getInnovationsStats($innovations)])
            ->download('innovaciones-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export innovations data to an Excel file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function innovationsExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new InnovationsExport($request->all()), 'innovaciones-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Display teacher participation report.
     *
     * @return \Illuminate\View\View
     */
    public function teacherParticipation(): \Illuminate\View\View
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Acceso denegado');
        return view('app.back.reports.participation', $this->reportService->getTeacherParticipation());
    }

    /**
     * Display comparative stats report.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function comparative(Request $request): \Illuminate\View\View
    {
        abort_unless(auth()->user()->hasRole(['admin', 'coordinador']), 403);
        $periods = $this->comparativeService->getValidatedPeriods($request->all());
        return view('app.back.reports.comparative', [...$periods, ...$this->comparativeService->getComparativeData($periods)]);
    }

    /**
     * Generate a PDF for the comparative report.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function comparativePdf(Request $request): \Illuminate\Http\Response
    {
        $periods = $this->comparativeService->getValidatedPeriods($request->all());
        return Pdf::loadView('app.back.reports.comparative-pdf', [...$periods, ...$this->comparativeService->getComparativeData($periods)])
            ->setPaper('a4', 'landscape')->download('reporte-comparativo-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export the comparative report to Excel.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function comparativeExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new \App\Exports\ComparativeExport($this->comparativeService->getValidatedPeriods($request->all())), 'reporte-comparativo-' . date('Y-m-d') . '.xlsx');
    }
}
