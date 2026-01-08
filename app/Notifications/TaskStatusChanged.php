<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStatusChanged extends Notification
{
    use Queueable;

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
     * @param \App\Models\User $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
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
