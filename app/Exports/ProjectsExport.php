<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Project::with(['category', 'profile.user']);

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (isset($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Categoría',
            'Responsable',
            'Estado',
            'Avance (%)',
            'Presupuesto',
            'Fecha Inicio',
            'Fecha Fin',
            'Creado',
        ];
    }

    public function map($project): array
    {
        return [
            $project->id,
            $project->title,
            $project->category->name,
            $project->profile->user->name,
            ucfirst(str_replace('_', ' ', $project->status)),
            $project->completion_percentage,
            $project->budget ? '$' . number_format($project->budget, 2) : '-',
            $project->start_date->format('d/m/Y'),
            $project->end_date->format('d/m/Y'),
            $project->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
