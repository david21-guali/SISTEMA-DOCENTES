<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

/**
 * Service to handle high-level read operations for Projects.
 * Optimized for maximum Maintainability Index (MI >= 65).
 */
class ProjectQueryService
{
    /**
     * Retrieve a paginated list of projects with eager loading and filters.
     * 
     * @param array $filters Key-value pairs of filters (status, category_id, search).
     * @param int $perPage Number of items to show per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getProjects(array $filters, int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Project::with(['category', 'profile.user', 'team']);

        $this->authorizeAccess($query);
        $this->applyContextualFilters($query, $filters);

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get aggregate statistics for projects.
     * 
     * @return array
     */
    public function getStats(): array
    {
        $user = Auth::user();
        $query = Project::query();

        if (!$user->hasRole(['admin', 'coordinador'])) {
            $query->forUser($user);
        }

        return [
            'total'         => (clone $query)->count(),
            'activos'       => (clone $query)->where('status', 'activo')->count(),
            'revision'      => (clone $query)->where('status', 'revision')->count(),
            'finalizado'    => (clone $query)->where('status', 'finalizado')->count(),
            'en_progreso'   => (clone $query)->where('status', 'en_progreso')->count(),
            'en_riesgo'     => (clone $query)->where('status', 'en_riesgo')->count(),
            'planificacion' => (clone $query)->where('status', 'planificacion')->count(),
        ];
    }

    /**
     * Restrict the query results based on user roles and ownership.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function authorizeAccess($query): void
    {
        $user = Auth::user();

        if (!$user->hasRole(['admin', 'coordinador'])) {
            $query->forUser($user);
        }
    }

    /**
     * Run all applicable filters on the project query.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    private function applyContextualFilters($query, array $filters): void
    {
        $this->filterByStatus($query, $filters['status'] ?? null);
        $this->filterByCategory($query, $filters['category_id'] ?? null);
        $this->filterBySearch($query, $filters['search'] ?? null);
    }

    /**
     * Filter projects by their current status.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $status
     * @return void
     */
    private function filterByStatus($query, ?string $status): void
    {
        if (!empty($status)) {
            $query->where('status', $status);
        }
    }

    /**
     * Filter projects by a specific category.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $categoryId
     * @return void
     */
    private function filterByCategory($query, $categoryId): void
    {
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }
    }

    /**
     * Perform a keyword search on title and description.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $searchTerm
     * @return void
     */
    private function filterBySearch($query, ?string $searchTerm): void
    {
        if (empty($searchTerm)) {
            return;
        }

        $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%');
        });
    }
}
