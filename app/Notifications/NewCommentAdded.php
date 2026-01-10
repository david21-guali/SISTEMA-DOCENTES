<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentAdded extends Notification
{
    use Queueable, \App\Traits\HasNotificationPreferences;

    public string $category = 'forum';

    /** @var \App\Models\Comment */
    public Comment $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */


    /**
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        /** @var \App\Models\Project|\App\Models\Innovation $commentable */
        $commentable = $this->comment->commentable;
        $type = $commentable instanceof \App\Models\Project ? 'proyecto' : 'innovación';
        $routeName = $commentable instanceof \App\Models\Project ? 'projects.show' : 'innovations.show';

        return (new MailMessage)
            ->subject('Nuevo comentario en ' . $type)
            ->line("{$this->comment->profile->user->name} ha comentado en el {$type}: {$commentable->title}")
            ->line('"' . \Illuminate\Support\Str::limit($this->comment->content, 100) . '"')
            ->action('Ver Comentario', route($routeName, $commentable->id))
            ->line('Gracias por participar.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        /** @var \App\Models\Project|\App\Models\Innovation $commentable */
        $commentable = $this->comment->commentable;
        $type = $commentable instanceof \App\Models\Project ? 'proyecto' : 'innovación';
        $routeName = $commentable instanceof \App\Models\Project ? 'projects.show' : 'innovations.show';

        return [
            'commentable_id' => $this->comment->commentable_id,
            'commentable_type' => $this->comment->commentable_type,
            'comment_id' => $this->comment->id,
            'user_name' => $this->comment->profile->user->name ?? 'Usuario',
            'title' => 'Nuevo comentario',
            'message' => "{$this->comment->profile->user->name} comentó en el {$type}: {$commentable->title}",
            'link' => route($routeName, (int) $commentable->id),
        ];
    }
}
