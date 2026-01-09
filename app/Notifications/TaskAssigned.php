<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification
{
    use Queueable, \App\Traits\HasNotificationPreferences;

    public string $category = 'tasks';
    public Task $task;

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




    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $type = $this->task->assignees()->count() > 1 ? 'grupal' : 'individual';

        return (new MailMessage)
                    ->subject("Nueva tarea {$type} asignada: " . $this->task->title)
                    ->line("Se te ha asignado una nueva tarea {$type}.")
                    ->line("Nombre: " . $this->task->title)
                    ->line("Proyecto: " . $this->task->project->title)
                    ->action('Ver Tarea', route('tasks.show', $this->task->id))
                    ->line('Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $type = $this->task->assignees()->count() > 1 ? 'grupal' : 'individual';

        return [
            'task_id' => $this->task->id,
            'title' => "Nueva tarea {$type} asignada",
            'message' => "Se te ha asignado la tarea {$type}: " . $this->task->title,
            'project_id' => $this->task->project_id,
            'link' => route('tasks.show', $this->task->id),
        ];
    }
}
