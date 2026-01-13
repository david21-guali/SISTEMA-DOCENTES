<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectAssigned extends Notification
{
    use \App\Traits\HasNotificationPreferences;

    public string $category = 'projects';
    public Project $project;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    /**
     * Get the notification's delivery channels.
     *
     * @param \App\Models\User $notifiable
     * @return array<int, string>
     */


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
        return (new MailMessage)
            ->subject('Asignación a Proyecto: ' . $this->project->title)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Has sido añadido al equipo del proyecto:')
            ->line('**' . $this->project->title . '**')
            ->line('Descripción: ' . \Illuminate\Support\Str::limit($this->project->description, 100))
            ->action('Ver Proyecto', route('projects.show', $this->project->id))
            ->line('Estamos emocionados de tenerte en el equipo.');
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
            'project_id' => $this->project->id,
            'title' => 'Asignación a Proyecto',
            'message' => 'Has sido añadido al equipo del proyecto: ' . $this->project->title,
            'link' => route('projects.show', $this->project->id),
            'priority' => 'normal',
        ];
    }
}
