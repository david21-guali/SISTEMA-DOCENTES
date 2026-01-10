<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentReplied extends Notification
{
    use Queueable, \App\Traits\HasNotificationPreferences;

    public string $category = 'forum';

    public Comment $reply;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $reply)
    {
        $this->reply = $reply;
    }



    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $commentable = $this->reply->commentable;
        $title = $commentable->title ?? 'Elemento';
        
        $url = match($this->reply->commentable_type) {
            \App\Models\Project::class => route('projects.show', $this->reply->commentable_id),
            \App\Models\Innovation::class => route('innovations.show', $this->reply->commentable_id),
            default => url('/')
        };

        return (new MailMessage)
                    ->line('Alguien respondió a tu comentario en: ' . $title)
                    ->line('"' . \Illuminate\Support\Str::limit($this->reply->content, 50) . '"')
                    ->action('Ver Conversación', $url)
                    ->line('Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $commentable = $this->reply->commentable;
        $type = ($commentable instanceof \App\Models\Project) ? 'proyecto' : 'innovación';
        $routeName = ($commentable instanceof \App\Models\Project) ? 'projects.show' : 'innovations.show';

        return [
            'commentable_id' => $this->reply->commentable_id,
            'commentable_type' => $this->reply->commentable_type,
            'comment_id' => $this->reply->id,
            'reply_id' => $this->reply->id,
            'user_name' => $this->reply->profile->user->name ?? 'Alguien',
            'title' => 'Nueva respuesta a tu comentario',
            'message' => "{$this->reply->profile->user->name} respondió a tu comentario en: " . ($commentable->title ?? ''),
            'link' => route($routeName, (int) $this->reply->commentable_id),
            'content' => \Illuminate\Support\Str::limit($this->reply->content, 50),
        ];
    }
}
