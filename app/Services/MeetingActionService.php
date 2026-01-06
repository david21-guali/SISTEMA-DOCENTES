<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\User;
use App\Notifications\MeetingInvitation;
use App\Notifications\MeetingResponse;
use App\Notifications\MeetingReminder;
use App\Notifications\MeetingCancellation;
use Illuminate\Support\Facades\Auth;

/**
 * Service to handle write operations for Meetings.
 * Designed for High Maintainability Index (MI >= 65).
 */
class MeetingActionService
{
    use \App\Traits\HandlesNotifications;

    /**
     * Create a new meeting, sync participants and send notifications.
     * 
     * @param array<string, mixed> $data Basic meeting information and participants.
     * @return Meeting
     */
    public function createMeeting(array $data): Meeting
    {
        $data['created_by'] = Auth::user()->profile->id;
        $data['status'] = 'pendiente';

        $meeting = Meeting::create($data);

        $this->syncParticipants($meeting, $data['participants']);
        $this->notifyParticipants($meeting, $data['participants']);

        return $meeting;
    }

    /**
     * Update meeting details and sync participants list.
     * 
     * @param Meeting $meeting
     * @param array<string, mixed> $data
     * @return void
     */
    public function updateMeeting(Meeting $meeting, array $data): void
    {
        $meeting->update($data);
        if (isset($data['participants']) && is_array($data['participants'])) {
            $this->syncParticipants($meeting, $data['participants'], true);
        }
    }

    /**
     * Update the attendance status of the current authenticated user.
     * 
     * @param Meeting $meeting
     * @param array<string, mixed> $data Contains attendance (confirmada/rechazada) and reason.
     * @return string Feedback message for the UI.
     */
    public function updateAttendance(Meeting $meeting, array $data): string
    {
        $message = (new MeetingAttendanceService())->updateAttendance($meeting, $data);
        
        $this->notifyCreatorResponse(
            $meeting, 
            $data['attendance'], 
            $data['rejection_reason'] ?? null
        );

        return $message;
    }

    /**
     * Mark a meeting as completed and register actual attendance.
     * 
     * @param Meeting $meeting
     * @param array<string, mixed> $data
     * @return void
     */
    public function completeMeeting(Meeting $meeting, array $data): void
    {
        $meeting->update([
            'status' => 'completada', 
            'notes'  => $data['notes'] ?? $meeting->notes
        ]);

        if (isset($data['attended'])) {
            (new MeetingAttendanceService())->processFinalAttendance($meeting, $data['attended']);
        }
    }

    /**
     * Send reminders to all users who haven't rejected the invitation.
     * 
     * @param Meeting $meeting
     * @return void
     */
    public function sendReminders(Meeting $meeting): void
    {
        (new MeetingLifecycleService())->sendReminders($meeting);
    }

    /**
     * Cancel a meeting and notify all invited participants.
     * 
     * @param Meeting $meeting
     * @param string $reason
     * @return void
     */
    public function cancelMeeting(Meeting $meeting, string $reason): void
    {
        (new MeetingLifecycleService())->cancelMeeting($meeting, $reason);
    }

    /**
     * Synchronize the participants list with the pivot table.
     * 
     * @param Meeting $meeting
     * @param array<int, int> $userIds
     * @param bool $isUpdate
     * @return void
     */
    private function syncParticipants(Meeting $meeting, array $userIds, bool $isUpdate = false): void
    {
        (new MeetingAttendanceService())->syncParticipants($meeting, $userIds, $isUpdate);
    }

    /**
     * Send notifications to invited users.
     * 
     * @param Meeting $meeting
     * @param array<int, int> $userIds
     * @return void
     */
    private function notifyParticipants(Meeting $meeting, array $userIds): void
    {
        (new MeetingLifecycleService())->notifyParticipants($meeting, $userIds);
    }

    /**
     * Notify meeting creator about a participant response.
     * 
     * @param Meeting $meeting
     * @param string $status
     * @param string|null $reason
     * @return void
     */
    private function notifyCreatorResponse(Meeting $meeting, string $status, ?string $reason): void
    {
        (new MeetingLifecycleService())->notifyCreator($meeting, $status, $reason);
    }
}
