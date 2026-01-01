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

    public $comment;

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
        $prefs = $notifiable->profile->notification_preferences ?? [];
        
        // Comments are project updates
        if (!($prefs['projects'] ?? true)) {
            return [];
        }

        return ['database']; // Keeping mail disabled for comments to avoid too much email
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->comment->project_id,
            'comment_id' => $this->comment->id,
            'user_name' => $this->comment->profile->user->name ?? 'Usuario',
            'title' => 'Nuevo comentario',
            'message' => "{$this->comment->profile->user->name} comentó en el proyecto: {$this->comment->project->title}",
            'link' => route('projects.show', $this->comment->project_id),
        ];
    }
}
