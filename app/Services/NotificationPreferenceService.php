<?php

namespace App\Services;

use App\Models\User;

/**
 * Service to handle notification channel preferences.
 * Aiming for class CC < 5.
 */
class NotificationPreferenceService
{
    /**
     * Get enabled channels for a notification type.
     * 
     * @param User $user
     * @param string $type (meetings, projects, etc)
     * @return array
     */
    public function getChannels(User $user, string $type): array
    {
        $prefs = $user->profile->notification_preferences ?? [];
        
        if (!($prefs[$type] ?? true)) {
            return [];
        }

        return $this->getEnabledMedia($prefs);
    }

    /**
     * Determine media channels (database, mail).
     * 
     * @param array $prefs
     * @return array
     */
    private function getEnabledMedia(array $prefs): array
    {
        $channels = ['database'];
        
        if ($prefs['email_enabled'] ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }
}
