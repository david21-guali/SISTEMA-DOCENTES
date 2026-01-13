<?php

namespace App\Observers;

use App\Models\ForumTopic;
use App\Models\User;
use App\Notifications\NewForumTopic;

/**
 * Observer for the ForumTopic model.
 * 
 * This class handles automated actions when a forum topic is created,
 * updated, or deleted. It primarily manages system-wide notifications
 * for new content.
 */
class ForumObserver
{
    /**
     * Handle the ForumTopic "created" event.
     * 
     * When a new topic is posted, this method triggers a notification
     * to all registered users except for the author of the topic.
     * 
     * @param \App\Models\ForumTopic $forumTopic The instance of the newly created topic.
     * @return void
     */
    public function created(ForumTopic $forumTopic): void
    {
        // La notificación se gestiona centralizadamente en ForumService::notifyNewTopic
        // para evitar envíos duplicados y asegurar el filtrado correcto por roles.
    }

}
