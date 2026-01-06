<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var \App\Models\Task */
    public $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $prefs = $notifiable->profile->notification_preferences ?? [];
        
        if (!($prefs['tasks'] ?? true)) {
            return [];
        }

        $channels = ['database'];
        if ($prefs['email_enabled'] ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }



    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('Se te ha asignado una nueva tarea.')
                    ->action('Ver Tarea', url('/tasks/' . $this->task->id))
                    ->line('Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => 'Nueva tarea asignada',
            'message' => 'Se te ha asignado la tarea: ' . $this->task->title,
            'project_id' => $this->task->project_id,
            'link' => route('tasks.show', $this->task->id),
        ];
    }
}
