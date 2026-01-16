<?php

namespace App\Policies;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create meetings.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'coordinador', 'docente']);
    }

    /**
     * Determine whether the user can view the meeting.
     */
    public function view(User $user, Meeting $meeting): bool
    {
        if ($user->hasRole(['admin', 'coordinador'])) {
            return true;
        }

        $profileId = $user->profile?->id;

        // Creador o participante
        return $meeting->created_by === $profileId || 
               $meeting->participants->contains($profileId);
    }

    /**
     * Determine whether the user can update the meeting.
     */
    public function update(User $user, Meeting $meeting): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Solo el creador
        return $meeting->created_by === $user->profile?->id;
    }

    /**
     * Determine whether the user can delete the meeting.
     */
    public function delete(User $user, Meeting $meeting): bool
    {
        return $this->update($user, $meeting);
    }

    /**
     * Determine whether the user can cancel the meeting.
     */
    public function cancel(User $user, Meeting $meeting): bool
    {
        return $this->update($user, $meeting);
    }

    /**
     * Determine whether the user can complete the meeting.
     */
    public function complete(User $user, Meeting $meeting): bool
    {
        return $this->update($user, $meeting);
    }

    /**
     * Determine whether the user can send reminders.
     */
    public function sendReminders(User $user, Meeting $meeting): bool
    {
        return $this->update($user, $meeting);
    }
}
