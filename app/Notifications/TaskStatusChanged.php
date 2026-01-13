<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStatusChanged extends Notification
{
    use \App\Traits\HasNotificationPreferences;

    public string $category = 'tasks';

    /** @var \App\Models\Task */
    public $task;
    /** @var string */
    public $oldStatus;
    /** @var string */
    public $newStatus;

    public function __construct(Task $task, string $oldStatus, string $newStatus)
    {
        $this->task = $task;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }



    /**
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Estado de Tarea Actualizado: ' . $this->task->title)
            ->line("La tarea '{$this->task->title}' ha cambiado su estado.")
            ->line("Estado anterior: {$this->oldStatus}")
            ->line("Nuevo estado: {$this->newStatus}")
            ->action('Ver Tarea', route('tasks.show', $this->task->id))
            ->line('Gracias por tu compromiso.');
    }

    /**
     * @param \App\Models\User $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => 'Estado de tarea actualizado',
            'message' => "La tarea '{$this->task->title}' cambió de {$this->oldStatus} a {$this->newStatus}",
            'project_id' => $this->task->project_id,
            'link' => route('tasks.show', $this->task->id),
        ];
    }
}
