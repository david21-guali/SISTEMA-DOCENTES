<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectDeadlineChanged extends Notification
{
    use \App\Traits\HasNotificationPreferences;

    public string $category = 'projects';

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
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $old = $this->oldDate instanceof \Carbon\Carbon ? $this->oldDate->format('d/m/Y') : $this->oldDate;
        $new = $this->newDate instanceof \Carbon\Carbon ? $this->newDate->format('d/m/Y') : $this->newDate;

        return (new MailMessage)
            ->subject('Fecha de Proyecto Modificada: ' . $this->project->title)
            ->line("Se ha actualizado la fecha de finalización del proyecto '{$this->project->title}'.")
            ->line("Fecha anterior: {$old}")
            ->line("Nueva fecha: {$new}")
            ->action('Ver Proyecto', route('projects.show', $this->project->id))
            ->line('Gracias por tu atención.');
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
