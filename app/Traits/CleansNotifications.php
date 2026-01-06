<?php

namespace App\Traits;

use Illuminate\Notifications\DatabaseNotification;

trait CleansNotifications
{
    protected static function bootCleansNotifications(): void
    {
        static::deleting(function ($model) {
            $key = $model->getNotificationKey();
            
            DatabaseNotification::where('data', 'LIKE', "%\"{$key}\":%")
                ->get()
                ->each(function ($notification) use ($model, $key) {
                    if (($notification->data[$key] ?? null) == $model->id) {
                        $notification->delete();
                    }
                });
        });
    }

    /**
     * Get the key name used in the notification JSON data.
     * Override this in the model if it differs from the snake_case model name.
     */
    protected function getNotificationKey(): string
    {
        return strtolower(class_basename($this)) . '_id';
    }
}
