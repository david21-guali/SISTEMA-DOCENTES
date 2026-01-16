<?php

namespace App\Notifications;

use App\Models\Innovation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InnovationReviewCompleted extends Notification
{
    use Queueable;

    public function __construct(protected Innovation $innovation)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Validación Comunitaria Completada: ' . $this->innovation->title)
                    ->line('El periodo de validación comunitaria para la innovación ha finalizado.')
                    ->line('Título: ' . $this->innovation->title)
                    ->line('Puntaje de la Comunidad: ' . $this->innovation->community_score . '%')
                    ->line('Votos Totales: ' . $this->innovation->total_votes)
                    ->action('Revisar y Aprobar', route('innovations.show', $this->innovation))
                    ->line('Por favor, procede con la revisión final y asignación del puntaje de impacto.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'innovation_id' => $this->innovation->id,
            'title' => 'Validación Comunitaria Completada',
            'message' => 'La innovación "' . $this->innovation->title . '" ha completado su periodo de votación con ' . $this->innovation->community_score . '% de aprobación.',
            'link' => route('innovations.show', $this->innovation),
        ];
    }
}
