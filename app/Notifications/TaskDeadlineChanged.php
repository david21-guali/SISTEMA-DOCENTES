<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDeadlineChanged extends Notification
{
    use Queueable;

    /** @var \App\Models\Task */
    public $task;
    /** @var \Carbon\Carbon|string|null */
    public $oldDate;
    /** @var \Carbon\Carbon|string|null */
    public $newDate;

    /**
     * @param \App\Models\Task $task
     * @param \Carbon\Carbon|string|null $oldDate
     * @param \Carbon\Carbon|string|null $newDate
     */
    public function __construct(Task $task, $oldDate, $newDate)
    {
        $this->task = $task;
        $this->oldDate = $oldDate;
        $this->newDate = $newDate;
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
        $old = $this->oldDate instanceof \Carbon\Carbon ? $this->oldDate->format('d/m/Y') : $this->oldDate;
        $new = $this->newDate instanceof \Carbon\Carbon ? $this->newDate->format('d/m/Y') : $this->newDate;

        return [
            'task_id' => $this->task->id,
            'title' => 'Fecha de tarea modificada',
            'message' => "La fecha límite de la tarea '{$this->task->title}' cambió de {$old} a {$new}",
            'project_id' => $this->task->project_id,
            'link' => route('tasks.show', $this->task->id),
        ];
    }
}
