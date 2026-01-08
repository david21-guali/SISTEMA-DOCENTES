<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectDeadlineApproaching extends Notification
{
    use Queueable;

    /** @var Project */
    public $project;
    /** @var int */
    public $daysLeft;

    /**
     * Create a new notification instance.
     * @param Project $project
     * @param int $daysLeft
     */
    public function __construct(Project $project, int $daysLeft)
    {
        $this->project = $project;
        $this->daysLeft = $daysLeft;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Cierre de proyecto próximo: ' . $this->project->title)
            ->line("El proyecto '{$this->project->title}' finaliza en {$this->daysLeft} días.")
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
            'title' => 'Cierre de proyecto próximo',
            'message' => "El proyecto '{$this->project->title}' finaliza en {$this->daysLeft} días.",
            'link' => route('projects.show', $this->project->id),
            'priority' => 'high',
        ];
    }
}
