<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TasksExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Task::with(['project', 'assignedProfile.user']);

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Proyecto',
            'Asignado a',
            'Estado',
            'Prioridad',
            'Fecha Límite',
            'Completada',
        ];
    }

    public function map($task): array
    {
        return [
            $task->id,
            $task->title,
            $task->project->title,
            $task->assignedUser ? $task->assignedUser->name : 'Sin asignar',
            ucfirst($task->status),
            ucfirst($task->priority),
            $task->due_date->format('d/m/Y'),
            $task->completion_date ? $task->completion_date->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
