<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\Innovation;
use Carbon\Carbon;

/**
 * Service to generate comparative analysis between two time periods.
 * Designed for High Maintainability Index (MI >= 65).
 */
class ComparativeReportService
{
    /**
     * Generate comparative datasets for two different periods and historical graph data.
     * 
     * @param array{period1Start: string, period1End: string, period2Start: string, period2End: string} $periods
     * @return array{period1Stats: array<string, int|float>, period2Stats: array<string, int|float>, changes: array<string, float>, projectsByMonth: array<string, int>, tasksByMonth: array<string, int>, innovationsByMonth: array<string, int>}
     */
    public function getComparativeData(array $periods): array
    {
        $period1Results = $this->calculateStatsForPeriod($periods['period1Start'], $periods['period1End']);
        $period2Results = $this->calculateStatsForPeriod($periods['period2Start'], $periods['period2End']);

        $graphData = $this->getMonthlyGraphData();

        return [
            'period1Stats' => $period1Results,
            'period2Stats' => $period2Results,
            'changes'      => $this->mapPercentageChanges($period1Results, $period2Results),
            'projectsByMonth'    => array_combine($graphData['labels'], $graphData['projects']),
            'tasksByMonth'       => array_combine($graphData['labels'], $graphData['tasks']),
            'innovationsByMonth' => array_combine($graphData['labels'], $graphData['innovations']),
        ];
    }

    /**
     * Aggregate all entity-specific metrics for a given date range.
     * 
     * @param string $start
     * @param string $end
     * @return array<string, int|float>
     */
    private function calculateStatsForPeriod(string $start, string $end): array
    {
        $projectStats = $this->getProjectStats($start, $end);
        $taskStats    = $this->getTaskStats($start, $end);
        $innovStats   = $this->getInnovationStats($start, $end);

        return array_merge($projectStats, $taskStats, $innovStats);
    }

    /**
     * Calculate project metrics for a date range.
     * 
     * @param string $start
     * @param string $end
     * @return array<string, int>
     */
    private function getProjectStats(string $start, string $end): array
    {
        return [
            'projects_created'  => Project::whereBetween('created_at', [$start, $end])->count(),
            'projects_finished' => Project::finished()->whereBetween('updated_at', [$start, $end])->count(),
            'projects_active'   => Project::active()->whereBetween('created_at', [$start, $end])->count(),
            'projects_at_risk'  => Project::atRisk()->whereBetween('created_at', [$start, $end])->count(),
        ];
    }

    /**
     * Calculate task metrics for a date range.
     * 
     * @param string $start
     * @param string $end
     * @return array<string, int>
     */
    private function getTaskStats(string $start, string $end): array
    {
        return [
            'tasks_created'   => Task::whereBetween('created_at', [$start, $end])->count(),
            'tasks_completed' => Task::completed()->whereBetween('completion_date', [$start, $end])->count(),
            'tasks_overdue'   => Task::whereBetween('due_date', [$start, $end])
                                     ->where('status', '!=', 'completada')
                                     ->count(),
        ];
    }

    /**
     * Calculate innovation metrics for a date range.
     * 
     * @param string $start
     * @param string $end
     * @return array<string, int|float>
     */
    private function getInnovationStats(string $start, string $end): array
    {
        return [
            'innovations_created'   => Innovation::whereBetween('created_at', [$start, $end])->count(),
            'innovations_completed' => Innovation::where('status', 'completada')
                                                 ->whereBetween('updated_at', [$start, $end])
                                                 ->count(),
            'avg_impact_score'      => (float) (Innovation::whereBetween('created_at', [$start, $end])->avg('impact_score') ?? 0),
        ];
    }

    /**
     * Map percentage changes between two datasets.
     * 
     * @param array<string, int|float> $old
     * @param array<string, int|float> $new
     * @return array<string, float>
     */
    private function mapPercentageChanges(array $old, array $new): array
    {
        $changes = [];
        foreach ($old as $key => $value) {
            $changes[$key] = $this->calculatePercentageChange($value, $new[$key]);
        }
        return $changes;
    }

    /**
     * Calculate the percentage increase or decrease between two numbers.
     * 
     * @param float $oldValue
     * @param float $newValue
     * @return float
     */
    private function calculatePercentageChange(float $oldValue, float $newValue): float
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        $difference = $newValue - $oldValue;
        return round(($difference / $oldValue) * 100, 1);
    }

    /**
     * Generate historical activity data for the last 12 months.
     * 
     * @return array{projects: array<int, int>, tasks: array<int, int>, innovations: array<int, int>, labels: array<int, string>}
     */
    private function getMonthlyGraphData(): array
    {
        $dataset = ['projects' => [], 'tasks' => [], 'innovations' => [], 'labels' => []];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthData = $this->getMetricsForSingleMonth($date);
            
            $dataset['labels'][]      = $date->translatedFormat('M');
            $dataset['projects'][]    = $monthData['projects'];
            $dataset['tasks'][]       = $monthData['tasks'];
            $dataset['innovations'][] = $monthData['innovations'];
        }

        return $dataset;
    }

    /**
     * Extract key metrics for a specific calendar month.
     * 
     * @param Carbon $month
     * @return array{projects: int, tasks: int, innovations: int}
     */
    private function getMetricsForSingleMonth($month): array
    {
        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        return [
            'projects'    => Project::whereBetween('created_at', [$start, $end])->count(),
            'tasks'       => Task::whereBetween('created_at', [$start, $end])->count(),
            'innovations' => Innovation::whereBetween('created_at', [$start, $end])->count(),
        ];
    }
}
