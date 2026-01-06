<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HandlesNotifications
{
    /**
     * Notify a collection of users with a specific notification.
     * @param iterable<int, \App\Models\User>|\Illuminate\Support\Collection<int, \App\Models\User>|array<int, \App\Models\User> $users
     */
    protected function notifyUsers(iterable $users, \Illuminate\Notifications\Notification $notification, bool $excludeAuth = true): void
    {
        foreach ($users as $user) {
            if (!$excludeAuth || ($user->id !== Auth::id())) {
                try {
                    $user->notify($notification);
                } catch (\Exception $e) {
                    // Log the error but continue execution to avoid blocking the user
                    \Illuminate\Support\Facades\Log::error("Failed to send notification: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Get users by their profile IDs.
     * @param array<int, int> $profileIds
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    protected function getUsersFromProfiles(array $profileIds): \Illuminate\Database\Eloquent\Collection
    {
        return User::whereHas('profile', fn($q) => $q->whereIn('id', $profileIds))->get();
    }
}
