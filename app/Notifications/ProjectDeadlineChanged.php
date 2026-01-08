<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectDeadlineChanged extends Notification
{
    use Queueable;

    /** @var \App\Models\Project */
    public $project;
    /** @var \Carbon\Carbon|string|null */
    public $oldDate;
    /** @var \Carbon\Carbon|string|null */
    public $newDate;

    /**
     * @param \App\Models\Project $project
     * @param \Carbon\Carbon|string|null $oldDate
     * @param \Carbon\Carbon|string|null $newDate
     */
    public function __construct(Project $project, $oldDate, $newDate)
    {
        $this->project = $project;
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
            'project_id' => $this->project->id,
            'title' => 'Fecha de proyecto modificada',
            'message' => "La fecha de fin del proyecto '{$this->project->title}' cambió de {$old} a {$new}",
            'link' => route('projects.show', $this->project->id),
        ];
    }
}
