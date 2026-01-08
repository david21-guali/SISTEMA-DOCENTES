<?php

namespace App\Exports;

use App\Models\Innovation;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InnovationsExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
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
        $query = Innovation::with(['profile.user', 'innovationType'])
            ->forUser(auth()->user());
        
        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        $innovations = $query->get();
        
        // Summary Stats
        $stats = [
            ['INDICADORES DE IMPACTO ACADÉMICO'],
            ['Total Innovaciones', $innovations->count()],
            ['Impacto Promedio', (round($innovations->avg('impact_score') ?? 0, 1)) . '/10'],
            ['Con Evidencias Registradas', $innovations->whereNotNull('evidence_files')->count()],
            [''],
            ['DETALLE DE INICIATIVAS'],
        ];

        // Header Row for Table
        $rows = [
            ['#', 'Título de la Innovación', 'Tipo / Categoría', 'Autor Responsable', 'Estado Actual', 'Puntaje Impacto', 'Fecha Registro']
        ];

        foreach ($innovations as $index => $innovation) {
            $rows[] = [
                $index + 1,
                $innovation->title,
                $innovation->innovationType->name ?? 'N/A',
                $innovation->profile->user->name ?? 'N/A',
                ucfirst(str_replace('_', ' ', $innovation->status)),
                $innovation->impact_score ? $innovation->impact_score . '/10' : '-',
                $innovation->created_at->format('d/m/Y')
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
            ['REPORTE DE INNOVACIÓN PEDAGÓGICA'],
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
            8 => ['font' => ['bold' => true, 'size' => 12]],
            9 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '06B6D4'], 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}
