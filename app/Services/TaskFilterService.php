<?php

namespace App\Services;

use App\Models\Task;

/**
 * Service to handle task filtering logic.
 * Aiming for class CC < 10.
 */
class TaskFilterService
{
    /**
     * Apply contextual filters to the task query.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    public function applyFilters($query, array $filters): void
    {
        $this->filterByStatus($query, $filters['status'] ?? null);
        $this->filterByProject($query, $filters['project_id'] ?? null);
        $this->filterBySearch($query, $filters['search'] ?? null);
    }

    /**
     * Apply status filter.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $status
     * @return void
     */
    private function filterByStatus($query, ?string $status): void
    {
        if (empty($status)) return;

        if ($status === 'atrasada') {
            $query->overdue();
            return;
        }

        $query->where('status', $status);
    }

    /**
     * Filter tasks by a specific project ID.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $projectId
     * @return void
     */
    private function filterByProject($query, $projectId): void
    {
        if (!empty($projectId)) {
            $query->where('project_id', $projectId);
        }
    }

    /**
     * Filter tasks by title or metadata.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $searchTerm
     * @return void
     */
    private function filterBySearch($query, ?string $searchTerm): void
    {
        if (!empty($searchTerm)) {
            $query->where('title', 'LIKE', '%' . $searchTerm . '%');
        }
    }
}
