<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export handler for tasks to Excel format.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class TasksExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @var array Contextual filters for the export query.
     */
    protected $filters;

    /**
     * TasksExport constructor.
     * 
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Fetch the filtered dataset for export.
     * 
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Task::with(['project', 'assignedProfile.user']);

        $this->applyContextualFilters($query);

        return $query->get();
    }

    /**
     * Define the data headers for the Excel sheet.
     * 
     * @return array
     */
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

    /**
     * Transform an individual Task model into an exportable array.
     * 
     * @param mixed $task
     * @return array
     */
    public function map($task): array
    {
        return [
            $task->id,
            $task->title,
            $this->getProjectTitle($task),
            $this->getAssignedUserName($task),
            $this->formatStatusLabel($task->status),
            $this->formatPriorityLabel($task->priority),
            $this->formatDate($task->due_date),
            $this->formatDateTime($task->completion_date),
        ];
    }

    /**
     * Apply bold styling to the header row.
     * 
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    /**
     * Apply contextual filters to the task query.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function applyContextualFilters($query): void
    {
        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
    }

    /**
     * Get the associated project title or return a placeholder.
     * 
     * @param Task $task
     * @return string
     */
    private function getProjectTitle($task): string
    {
        return $task->project->title ?? 'N/A';
    }

    /**
     * Extract and normalize the assigned user name.
     * 
     * @param Task $task
     * @return string
     */
    private function getAssignedUserName($task): string
    {
        return $task->assignedUser->name ?? 'Sin asignar';
    }

    /**
     * Capitalize and normalize the status label.
     * 
     * @param string $status
     * @return string
     */
    private function formatStatusLabel(string $status): string
    {
        return ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Capitalize and normalize the priority label.
     * 
     * @param string $priority
     * @return string
     */
    private function formatPriorityLabel(string $priority): string
    {
        return ucfirst($priority);
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
     * Standardize the date format with time for completion info.
     * 
     * @param mixed $date
     * @return string
     */
    private function formatDateTime($date): string
    {
        return $date ? $date->format('d/m/Y H:i') : '-';
    }
}
