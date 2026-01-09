<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectsExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    /** @var array<string, mixed> */
    protected $filters;

    /**
     * @param array<string, mixed> $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        $query = Project::with(['category', 'profile.user'])
            ->forUser(auth()->user());
        
        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (isset($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        $projects = $query->get();
        
        // Summary Stats
        $stats = [
            ['RESUMEN EJECUTIVO DE CARTERA'],
            ['Total Proyectos', $projects->count()],
            ['Avance Promedio Global', (round($projects->avg('completion_percentage') ?? 0, 1)) . '%'],
            ['Presupuesto Total Asignado', '$' . number_format($projects->sum('budget'), 2)],
            [''],
            ['ESTADO DE PROYECTOS'],
            ['En Planificación', $projects->where('status', 'planificacion')->count()],
            ['En Ejecución', $projects->where('status', 'en_progreso')->count()],
            ['Finalizados', $projects->where('status', 'completado')->count()],
            [''],
            ['DETALLE DEL PORTAFOLIO'],
        ];

        // Header Row for Table
        $rows = [
            ['#', 'Título del Proyecto', 'Categoría', 'Líder / Responsable', 'Estado Actual', 'Avance', 'Presupuesto', 'Fecha de Cierre']
        ];

        foreach ($projects as $index => $project) {
            $rows[] = [
                $index + 1,
                $project->title,
                $project->category->name ?? 'N/A',
                $project->profile->user->name ?? 'N/A',
                ucfirst(str_replace('_', ' ', $project->status)),
                $project->completion_percentage . '%',
                $project->budget ? '$' . number_format($project->budget, 2) : '-',
                $project->end_date instanceof \Carbon\Carbon ? $project->end_date->format('d/m/Y') : '-'
            ];
        }

        return array_merge($stats, $rows);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function headings(): array
    {
        return [
            ['REPORTE INSTITUCIONAL DE GESTIÓN DE PROYECTOS'],
            ['Fecha de Generación: ' . date('d/m/Y H:i')],
            [''],
        ];
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
            6 => ['font' => ['bold' => true]],
            8 => ['font' => ['bold' => true, 'size' => 12]], // Status header
            14 => ['font' => ['bold' => true, 'size' => 12]], // Table title
            15 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '3B82F6'], 'color' => ['rgb' => 'FFFFFF']]], // Table header
        ];
    }
}
