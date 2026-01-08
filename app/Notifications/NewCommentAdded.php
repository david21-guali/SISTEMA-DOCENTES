<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentAdded extends Notification
{
    use Queueable;

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
    public function via(object $notifiable): array
    {
        return ['database'];
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
