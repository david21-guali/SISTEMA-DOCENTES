<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TasksExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
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
        $query = Task::with(['project', 'assignedProfile.user'])
            ->forUser(auth()->user());
        
        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        $tasks = $query->get();
        
        // Summary Stats
        $stats = [
            ['ANÁLISIS DE PRODUCTIVIDAD OPERATIVA'],
            ['Total Tareas', $tasks->count()],
            ['Tareas Completadas', $tasks->where('status', 'completada')->count()],
            ['Tasa de Cumplimiento', (round($tasks->count() > 0 ? ($tasks->where('status', 'completada')->count() / $tasks->count()) * 100 : 0, 1)) . '%'],
            [''],
            ['DESGLOSE POR PRIORIDAD'],
            ['Prioridad Alta', $tasks->where('priority', 'alta')->count()],
            ['Prioridad Media', $tasks->where('priority', 'media')->count()],
            ['Prioridad Baja', $tasks->where('priority', 'baja')->count()],
            [''],
            ['DETALLE DE ACTIVIDADES'],
        ];

        // Header Row for Table
        $rows = [
            ['#', 'Título de la Tarea', 'Proyecto Relacionado', 'Personal Asignado', 'Prioridad', 'Estado Actual', 'Fecha Límite']
        ];

        foreach ($tasks as $index => $task) {
            $rows[] = [
                $index + 1,
                $task->title,
                $task->project->title ?? 'N/A',
                $task->assignedUser->name ?? 'Sin asignar',
                ucfirst($task->priority),
                ucfirst($task->status),
                $task->due_date ? $task->due_date->format('d/m/Y') : '-'
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
            ['REPORTE DE CUMPLIMIENTO DE TAREAS'],
            ['Generado el: ' . date('d/m/Y H:i')],
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
            8 => ['font' => ['bold' => true]], // Priority header
            12 => ['font' => ['bold' => true, 'size' => 12]],
            13 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F59E0B'], 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}
