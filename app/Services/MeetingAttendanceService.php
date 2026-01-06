<?php

namespace App\Services;

use App\Models\Meeting;
use Illuminate\Support\Facades\Auth;

/**
 * Service to handle meeting attendance and reminders.
 * Aiming for very low Class Cyclomatic Complexity.
 */
class MeetingAttendanceService
{
    /**
     * Update the attendance status of the current authenticated user.
     * 
     * @param Meeting $meeting
     * @param array<string, mixed> $data
     * @return string
     */
    public function updateAttendance(Meeting $meeting, array $data): string
    {
        $status = $data['attendance'];
        $reason = ($status === 'rechazada') ? ($data['rejection_reason'] ?? null) : null;

        $meeting->participants()->updateExistingPivot(Auth::user()->profile->id, [
            'attendance'       => $status,
            'rejection_reason' => $reason
        ]);

        return $status === 'confirmada' ? 'Asistencia confirmada.' : 'Invitación declinada.';
    }

    /**
     * Synchronize the participants list with the pivot table.
     * 
     * @param Meeting $meeting
     * @param array<int, int> $userIds
     * @param bool $isUpdate
     * @return void
     */
    public function syncParticipants(Meeting $meeting, array $userIds, bool $isUpdate = false): void
    {
        $profiles = \App\Models\User::whereIn('id', $userIds)
            ->with('profile')
            ->get()
            ->pluck('profile.id')
            ->toArray();
            
        $syncData = [];

        foreach ($profiles as $pid) {
            $syncData[$pid] = ['attendance' => $this->getInitialAttendance($meeting, $pid, $isUpdate)];
        }

        $syncData[Auth::user()->profile->id] = ['attendance' => 'confirmada'];

        $meeting->participants()->sync($syncData);
    }

    /**
     * Determine initial attendance status for sync.
     * 
     * @param Meeting $meeting
     * @param int $pid
     * @param bool $isUpdate
     * @return string
     */
    private function getInitialAttendance(Meeting $meeting, int $pid, bool $isUpdate): string
    {
        $existing = $isUpdate ? $meeting->participants->find($pid) : null;
        return $existing->pivot->attendance ?? 'pendiente';
    }

    /**
     * Process final attendance list during completion.
     * 
     * @param Meeting $meeting
     * @param array<int, int> $attendedPids
     * @return void
     */
    public function processFinalAttendance(Meeting $meeting, array $attendedPids): void
    {
        foreach ($meeting->participants as $participant) {
            $this->updateSingleParticipantAttendance($meeting, $participant->id, $attendedPids);
        }
    }

    /**
     * Update individual participant status based on final list.
     * 
     * @param Meeting $meeting
     * @param int $pid
     * @param array<int, int> $attendedPids
     * @return void
     */
    private function updateSingleParticipantAttendance(Meeting $meeting, int $pid, array $attendedPids): void
    {
        $status = in_array($pid, $attendedPids) ? 'asistio' : 'rechazada';
        $meeting->participants()->updateExistingPivot($pid, ['attendance' => $status]);
    }
}
