<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\MeetingReminder;
use App\Notifications\MeetingCancellation;

/**
 * Service to handle meeting notifications and lifecycle events.
 * Aiming for class CC < 10.
 */
class MeetingLifecycleService
{
    use \App\Traits\HandlesNotifications;

    /**
     * Send reminders to all users who haven't rejected the invitation.
     * 
     * @param Meeting $meeting
     * @return void
     */
    public function sendReminders(Meeting $meeting): void
    {
        $users = $meeting->participants
            ->filter(function($p) {
                /** @var \App\Models\MeetingParticipant $pivot */
                $pivot = $p->pivot;
                return $pivot->attendance !== 'rechazada';
            })
            ->pluck('user');

        $this->notifyUsers($users, new MeetingReminder($meeting));
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
        $meeting->update([
            'status' => 'cancelada', 
            'notes'  => $meeting->notes . "\n\n[CANCELADA]: " . $reason
        ]);

        $users = $meeting->participants->pluck('user');
        $this->notifyUsers($users, new MeetingCancellation($meeting, $reason));
    }

    /**
     * Send notifications to invited users.
     * 
     * @param Meeting $meeting
     * @param array<int> $userIds
     * @return void
     */
    public function notifyParticipants(Meeting $meeting, array $userIds): void
    {
        $users = \App\Models\User::whereIn('id', $userIds)->get();
        $this->notifyUsers($users, new \App\Notifications\MeetingInvitation($meeting));
    }

    /**
     * Notify meeting creator about a participant response.
     * 
     * @param Meeting $meeting
     * @param string $status
     * @param string|null $reason
     * @return void
     */
    public function notifyCreator(Meeting $meeting, string $status, ?string $reason): void
    {
        $creator = $meeting->creator?->user;
        if ($creator && $meeting->created_by !== Auth::user()->profile->id) {
            $creator->notify(new \App\Notifications\MeetingResponse(
                $meeting, Auth::user(), $status, $reason
            ));
        }
    }
}
