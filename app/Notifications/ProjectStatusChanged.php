<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectStatusChanged extends Notification
{
    use \App\Traits\HasNotificationPreferences;

    public string $category = 'projects';

    /** @var \App\Models\Project */
    public $project;
    /** @var string */
    public $oldStatus;
    /** @var string */
    public $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, string $oldStatus, string $newStatus)
    {
        $this->project = $project;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }



    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Estado de proyecto actualizado: ' . $this->project->title)
            ->line("El proyecto '{$this->project->title}' cambió de estado.")
            ->line("Estado anterior: {$this->oldStatus}")
            ->line("Nuevo estado: {$this->newStatus}")
            ->action('Ver Proyecto', route('projects.show', $this->project->id))
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
            'project_id' => $this->project->id,
            'title' => 'Estado de proyecto actualizado',
            'message' => "El proyecto '{$this->project->title}' cambió de {$this->oldStatus} a {$this->newStatus}",
            'link' => route('projects.show', $this->project->id),
        ];
    }
}
