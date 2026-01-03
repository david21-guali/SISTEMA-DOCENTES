<?php

namespace App\Exports;

use App\Models\Innovation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InnovationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Innovation::with(['profile.user', 'innovationType']);

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
            'Tipo',
            'Responsable',
            'Estado',
            'Impacto',
            'Archivos',
            'Creado',
        ];
    }

    public function map($innovation): array
    {
        return [
            $innovation->id,
            $innovation->title,
            $innovation->innovationType->name,
            $innovation->user->name ?? 'N/A',
            ucfirst(str_replace('_', ' ', $innovation->status)),
            $innovation->impact_score ? $innovation->impact_score . '/10' : '-',
            $innovation->evidence_files ? count($innovation->evidence_files) : 0,
            $innovation->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
