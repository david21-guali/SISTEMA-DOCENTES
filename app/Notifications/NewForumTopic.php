<?php

namespace App\Notifications;

use App\Models\ForumTopic;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewForumTopic extends Notification
{
    use Queueable, \App\Traits\HasNotificationPreferences;

    public string $category = 'forum';

    public ForumTopic $topic;

    public function __construct(ForumTopic $topic)
    {
        $this->topic = $topic;
    }



    /**
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo tema en el foro: ' . $this->topic->title)
            ->line("{$this->topic->profile->user->name} ha publicado un nuevo tema en el foro.")
            ->line('Título: ' . $this->topic->title)
            ->action('Ver Tema', route('forum.show', $this->topic->id))
            ->line('Te invitamos a participar en la discusión.');
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
