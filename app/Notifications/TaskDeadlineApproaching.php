<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDeadlineApproaching extends Notification
{
    use \App\Traits\HasNotificationPreferences;

    public string $category = 'tasks';

    /** @var Task */
    public $task;
    /** @var int */
    public $daysLeft;

    /**
     * Create a new notification instance.
     * @param Task $task
     * @param int $daysLeft
     */
    public function __construct(Task $task, int $daysLeft)
    {
        $this->task = $task;
        $this->daysLeft = $daysLeft;
    }



    /**
     * Get the mail representation of the notification.
     */
    /**
     * Get the mail representation of the notification.
     * 
     * @param \App\Models\User $notifiable
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = "La tarea '{$this->task->title}' vence en {$this->daysLeft} " . ($this->daysLeft == 1 ? 'día' : 'días') . ".";
        
        return (new MailMessage)
            ->subject('Recordatorio: Tarea próxima a vencer')
            ->priority(1) // High priority
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line($message)
            ->line('Proyecto: ' . $this->task->project->title)
            ->line('Fecha de vencimiento: ' . $this->task->due_date->format('d/m/Y'))
            ->action('Ver Tarea', route('tasks.show', $this->task->id))
            ->line('Por favor revisa el progreso.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    /**
     * Get the array representation of the notification.
     *
     * @param \App\Models\User $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => 'Plazo de tarea próximo',
            'message' => "La tarea '{$this->task->title}' vence en {$this->daysLeft} días.",
            'project_id' => $this->task->project_id,
            'link' => route('tasks.show', $this->task->id),
            'priority' => 'high',
        ];
    }
}
