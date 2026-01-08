<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentReplied extends Notification
{
    use Queueable;

    public Comment $reply;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $reply)
    {
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // You can add 'mail' if needed
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $commentable = $this->reply->commentable;
        $title = $commentable instanceof \App\Models\Project ? $commentable->title : ($commentable->title ?? 'Elemento');
        $url = url('/'); // Determine generic or specific url based on type

        if ($this->reply->commentable_type === \App\Models\Project::class) {
             $url = route('projects.show', $this->reply->commentable_id);
        } elseif ($this->reply->commentable_type === \App\Models\Innovation::class) {
             $url = route('innovations.show', $this->reply->commentable_id);
        }

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
        $type = $commentable instanceof \App\Models\Project ? 'proyecto' : ($commentable instanceof \App\Models\Innovation ? 'innovación' : 'elemento');
        $routeName = $commentable instanceof \App\Models\Project ? 'projects.show' : ($commentable instanceof \App\Models\Innovation ? 'innovations.show' : 'home');

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
