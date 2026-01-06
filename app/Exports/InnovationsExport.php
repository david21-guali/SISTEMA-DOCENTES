<?php

namespace App\Exports;

use App\Models\Innovation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export handler for pedagogical innovations to Excel format.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class InnovationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @var array Contextual filters for the export query.
     */
    protected $filters;

    /**
     * Initialize the export with specific search/status filters.
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
        $query = Innovation::with(['profile.user', 'innovationType']);

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
            'Tipo',
            'Responsable',
            'Estado',
            'Impacto',
            'Archivos',
            'Creado',
        ];
    }

    /**
     * Transform an individual Innovation model into an exportable array.
     * 
     * @param mixed $innovation
     * @return array
     */
    public function map($innovation): array
    {
        return [
            $innovation->id,
            $innovation->title,
            $this->getInnovationTypeName($innovation),
            $this->getResponsibleName($innovation),
            $this->formatStatusLabel($innovation->status),
            $this->formatImpactScore($innovation->impact_score),
            $this->countEvidenceFiles($innovation),
            $this->formatDate($innovation->created_at),
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
     * Internal helper to filter innovations by their current status.
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
     * Extract and normalize the responsible user name.
     * 
     * @param Innovation $innovation
     * @return string
     */
    private function getResponsibleName($innovation): string
    {
        return $innovation->user->name ?? 'N/A';
    }

    /**
     * Extract the innovation type name or return a placeholder.
     * 
     * @param Innovation $innovation
     * @return string
     */
    private function getInnovationTypeName($innovation): string
    {
        return $innovation->innovationType->name ?? 'Sin tipo';
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
     * Format the numeric impact score for the export.
     * 
     * @param mixed $score
     * @return string
     */
    private function formatImpactScore($score): string
    {
        return $score ? $score . '/10' : '-';
    }

    /**
     * Calculate the number of associated evidence files.
     * 
     * @param Innovation $innovation
     * @return int
     */
    private function countEvidenceFiles($innovation): int
    {
        return $innovation->evidence_files ? count($innovation->evidence_files) : 0;
    }

    /**
     * Standardize the creation date format for Excel.
     * 
     * @param mixed $date
     * @return string
     */
    private function formatDate($date): string
    {
        return $date ? $date->format('d/m/Y H:i') : '-';
    }
}
