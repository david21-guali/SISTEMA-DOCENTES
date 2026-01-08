<?php

namespace App\Exports;

use App\Services\ComparativeReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export handler for comparative reports between two periods.
 */
class ComparativeExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    /** @var array{period1Start: string, period1End: string, period2Start: string, period2End: string} */
    protected $periods;

    /** @var ComparativeReportService */
    protected $comparativeService;

    /**
     * @param array{period1Start: string, period1End: string, period2Start: string, period2End: string} $periods
     */
    public function __construct(array $periods)
    {
        $this->periods = $periods;
        $this->comparativeService = app(ComparativeReportService::class);
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        $data = $this->comparativeService->getComparativeData($this->periods);
        $p1 = $data['period1Stats'];
        $p2 = $data['period2Stats'];
        $changes = $data['changes'];

        return [
            ['PROYECTOS', '', '', ''],
            ['Proyectos Creados', $p1['projects_created'], $p2['projects_created'], $changes['projects_created'] . '%'],
            ['Proyectos Finalizados', $p1['projects_finished'], $p2['projects_finished'], $changes['projects_finished'] . '%'],
            ['Proyectos Activos', $p1['projects_active'], $p2['projects_active'], $changes['projects_active'] . '%'],
            ['Proyectos en Riesgo', $p1['projects_at_risk'], $p2['projects_at_risk'], $changes['projects_at_risk'] . '%'],
            ['', '', '', ''],
            ['TAREAS', '', '', ''],
            ['Tareas Creadas', $p1['tasks_created'], $p2['tasks_created'], $changes['tasks_created'] . '%'],
            ['Tareas Completadas', $p1['tasks_completed'], $p2['tasks_completed'], $changes['tasks_completed'] . '%'],
            ['Tareas Vencidas', $p1['tasks_overdue'], $p2['tasks_overdue'], $changes['tasks_overdue'] . '%'],
            ['', '', '', ''],
            ['INNOVACIÓN', '', '', ''],
            ['Innovaciones Registradas', $p1['innovations_created'], $p2['innovations_created'], $changes['innovations_created'] . '%'],
            ['Innovaciones Completadas', $p1['innovations_completed'], $p2['innovations_completed'], $changes['innovations_completed'] . '%'],
            ['Puntaje de Impacto Promedio', $p1['avg_impact_score'], $p2['avg_impact_score'], $changes['avg_impact_score'] . '%'],
        ];
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function headings(): array
    {
        return [
            ['REPORTE COMPARATIVO DE GESTIÓN'],
            ['Generado el: ' . date('d/m/Y H:i')],
            [''],
            ['Métrica', 'Período 1 (' . $this->periods['period1Start'] . ' al ' . $this->periods['period1End'] . ')', 'Período 2 (' . $this->periods['period2Start'] . ' al ' . $this->periods['period2End'] . ')', 'Variación (%)'],
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array<int, mixed>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            4 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E9ECEF']]],
            5 => ['font' => ['bold' => true]], // PROYECTOS
            11 => ['font' => ['bold' => true]], // TAREAS
            16 => ['font' => ['bold' => true]], // INNOVACIÓN
        ];
    }
}
