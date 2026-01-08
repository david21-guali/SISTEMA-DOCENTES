<?php

namespace App\Notifications;

use App\Models\ForumPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewForumReply extends Notification
{
    use Queueable;

    public ForumPost $post;

    public function __construct(ForumPost $post)
    {
        $this->post = $post;
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
            'type' => 'forum_reply', // Optional tag
            'reply_id' => $this->post->id,
            'topic_id' => $this->post->topic_id,
            'user_name' => $this->post->profile->user->name ?? 'Usuario',
            'title' => 'Nueva respuesta en el foro',
            'message' => "{$this->post->profile->user->name} respondió en el tema: " . $this->post->topic->title,
            'link' => route('forum.show', $this->post->topic_id),
            'content' => \Illuminate\Support\Str::limit($this->post->content, 50),
        ];
    }
}
