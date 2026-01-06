<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export handler for projects to Excel format.
 * Optimized for High Maintainability Index (MI >= 65).
 * @implements WithMapping<mixed>
 */
class ProjectsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @var array<string, mixed> Contextual filters for the export query.
     */
    protected $filters;

    /**
     * ProjectsExport constructor.
     * 
     * @param array<string, mixed> $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Fetch the filtered dataset for export.
     * 
     * @return \Illuminate\Support\Collection<int, \App\Models\Project>
     */
    public function collection()
    {
        $query = Project::with(['category', 'profile.user']);

        $this->applyContextualFilters($query);

        return $query->get();
    }

    /**
     * Define the data headers for the Excel sheet.
     * 
     * @return array<int, string>
     */
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

    /**
     * Transform an individual Project model into an exportable array.
     * 
     * @param mixed $project
     * @return array<int, mixed>
     */
    public function map($project): array
    {
        if (!($project instanceof Project)) {
            return [];
        }
        return [
            $project->id,
            $project->title,
            $this->getCategoryName($project),
            $this->getResponsibleName($project),
            $this->formatStatusLabel($project->status),
            $project->completion_percentage,
            $this->formatBudget($project->budget),
            $this->formatDate($project->start_date),
            $this->formatDate($project->end_date),
            $this->formatDateTime($project->created_at),
        ];
    }

    /**
     * Apply bold styling to the header row.
     * 
     * @param Worksheet $sheet
     * @return array<int, array<string, array<string, bool>>>
     */
    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    /**
     * Apply contextual filters to the project query.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<\App\Models\Project> $query
     * @return void
     */
    private function applyContextualFilters($query): void
    {
        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        if (isset($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }
    }

    /**
     * Get the category name or return a placeholder.
     * 
     * @param Project $project
     * @return string
     */
    private function getCategoryName($project): string
    {
        return $project->category->name ?? 'Sin categoría';
    }

    /**
     * Extract and normalize the responsible user name.
     * 
     * @param Project $project
     * @return string
     */
    private function getResponsibleName($project): string
    {
        return $project->profile->user->name ?? 'N/A';
    }

    /**
     * Convert the database status string into a human-readable label.
     * 
     * @param string $status
     * @return string
     */
    private function formatStatusLabel(string $status): string
    {
        return ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Standardize the budget display format.
     * 
     * @param mixed $budget
     * @return string
     */
    private function formatBudget($budget): string
    {
        return $budget ? '$' . number_format($budget, 2) : '-';
    }

    /**
     * Standardize the date format for Excel.
     * 
     * @param mixed $date
     * @return string
     */
    private function formatDate($date): string
    {
        return $date ? $date->format('d/m/Y') : '-';
    }

    /**
     * Standardize the creation date format with time.
     * 
     * @param mixed $date
     * @return string
     */
    private function formatDateTime($date): string
    {
        return $date ? $date->format('d/m/Y H:i') : '-';
    }
}
