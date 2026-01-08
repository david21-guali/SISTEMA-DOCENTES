<?php

namespace App\Notifications;

use App\Models\ForumTopic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewForumTopic extends Notification
{
    use Queueable;

    public ForumTopic $topic;

    public function __construct(ForumTopic $topic)
    {
        $this->topic = $topic;
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'forum_topic',
            'topic_id' => $this->topic->id,
            'user_name' => $this->topic->profile->user->name ?? 'Usuario',
            'title' => 'Nuevo tema en el foro',
            'message' => "{$this->topic->profile->user->name} creó un nuevo tema: " . $this->topic->title,
            'link' => route('forum.show', $this->topic->id),
            'content' => \Illuminate\Support\Str::limit($this->topic->description, 50),
        ];
    }
}
