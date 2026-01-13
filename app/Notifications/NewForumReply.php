<?php

namespace App\Notifications;

use App\Models\ForumPost;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewForumReply extends Notification
{
    use \App\Traits\HasNotificationPreferences;

    public string $category = 'forum';

    public ForumPost $post;

    public function __construct(ForumPost $post)
    {
        $this->post = $post;
    }



    public function toMail(object $notifiable): MailMessage
    {
        $authorName = $this->post->profile->user->name ?? 'Un usuario';

        return (new MailMessage)
            ->subject('Nueva respuesta en foro: ' . $this->post->topic->title)
            ->line("{$authorName} ha respondido a un tema en el foro.")
            ->line('Tema: ' . $this->post->topic->title)
            ->line('"' . \Illuminate\Support\Str::limit($this->post->content, 100) . '"')
            ->action('Ver Foro', route('forum.show', $this->post->topic_id))
            ->line('Gracias por participar en la comunidad.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $authorName = $this->post->profile->user->name ?? 'Usuario';

        return [
            'type' => 'forum_reply', // Optional tag
            'reply_id' => $this->post->id,
            'topic_id' => $this->post->topic_id,
            'user_name' => $authorName,
            'title' => 'Nueva respuesta en el foro',
            'message' => "{$authorName} respondió en el tema: " . $this->post->topic->title,
            'link' => route('forum.show', $this->post->topic_id),
            'content' => \Illuminate\Support\Str::limit($this->post->content, 50),
        ];
    }
}
