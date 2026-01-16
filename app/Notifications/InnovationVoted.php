<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InnovationVoted extends Notification
{
    use Queueable;

    /**
     * @var \App\Models\Innovation
     */
    protected $innovation;

    /**
     * Create a new notification instance.
     * 
     * @param \App\Models\Innovation $innovation
     */
    public function __construct($innovation)
    {
        $this->innovation = $innovation;
    }

    /**
     * @param object $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        /** @var \App\Models\User $notifiable */
        return (new MailMessage)
            ->subject('¡Han votado en tu innovación!')
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Alguien ha participado en la validación comunitaria de tu innovación: "' . $this->innovation->title . '".')
            ->action('Ver mi Innovación', route('innovations.show', $this->innovation))
            ->line('¡Echa un vistazo a la evolución de tu propuesta!');
    }

    /**
     * @param object $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'innovation_id' => $this->innovation->id,
            'title' => 'Voto recibido',
            'message' => 'Han votado en tu innovación: ' . $this->innovation->title,
            'action_url' => route('innovations.show', $this->innovation),
        ];
    }
}
