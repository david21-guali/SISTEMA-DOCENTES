<?php

namespace App\Services;

use App\Models\Meeting;
use Illuminate\Support\Facades\Auth;

/**
 * Service to handle high-level read operations for Meetings.
 * Optimized for maximum Maintainability Index (MI >= 65).
 */
class MeetingQueryService
{
    /**
     * Retrieve global statistics for meetings associated with the current user.
     * 
     * @return array{upcoming: int, completed: int, total: int}
     */
    public function getStats(): array
    {
        $baseQuery = Meeting::forUser((int) Auth::id());

        return [
            'upcoming'  => $this->countUpcoming($baseQuery),
            'completed' => $this->countCompleted($baseQuery),
            'total'     => $this->countTotal($baseQuery),
        ];
    }

    /**
     * Retrieve a paginated list of meetings based on filters and user profile.
     * 
     * @param array<string, mixed> $filters Query parameters for filtering and ordering.
     * @param int $perPage Number of results per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Meeting>
     */
    public function getMeetings(array $filters, int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Meeting::with(['project', 'creator', 'participants.user'])
            ->forUser((int) Auth::id());

        $this->applyContextualFilters($query, $filters);
        $this->applyOrdering($query, $filters['order'] ?? 'asc');

        return $query->paginate($perPage);
    }

    /**
     * Get all projects available for associating with a meeting.
     * 
     * @return \Illuminate\Support\Collection<int, \App\Models\Project>
     */
    public function getProjects(): \Illuminate\Support\Collection
    {
        return \App\Models\Project::orderBy('title')->get();
    }

    /**
     * Retrieve users that can be invited to meetings (admin, coordinator, teacher).
     * 
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    public function getEligibleUsers(): \Illuminate\Support\Collection
    {
        return \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'coordinador', 'docente']);
        })->orderBy('name')->get();
    }

    /**
     * Apply status and project filters to the query.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<Meeting> $query
     * @param array<string, mixed> $filters
     * @return void
     */
    private function applyContextualFilters($query, array $filters): void
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
    }

    /**
     * Apply sorting logic to the meetings query.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<Meeting> $query
     * @param string $order Direction of the sort (asc/desc).
     * @return void
     */
    private function applyOrdering($query, string $order): void
    {
        $query->orderBy('meeting_date', $order);
    }

    /**
     * Count upcoming meetings for the provided query scope.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<Meeting> $query
     * @return int
     */
    private function countUpcoming($query): int
    {
        return (clone $query)->upcoming()->count();
    }

    /**
     * Count completed meetings for the provided query scope.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<Meeting> $query
     * @return int
     */
    private function countCompleted($query): int
    {
        return (clone $query)->completed()->count();
    }

    /**
     * Get total count for the provided meeting query.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<Meeting> $query
     * @return int
     */
    private function countTotal($query): int
    {
        return (clone $query)->count();
    }
}
