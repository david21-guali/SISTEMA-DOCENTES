<?php

namespace App\Notifications;

use App\Models\Innovation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InnovationStatusChanged extends Notification
{
    use Queueable, \App\Traits\HasNotificationPreferences;

    public string $category = 'innovations';

    /** @var \App\Models\Innovation */
    public $innovation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Innovation $innovation)
    {
        $this->innovation = $innovation;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = match($this->innovation->status) {
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada',
            'en_revision' => 'En Revisión',
            default => $this->innovation->status
        };

        return (new MailMessage)
            ->subject('Actualización de Innovación: ' . $this->innovation->title)
            ->line("Tu propuesta de innovación '{$this->innovation->title}' ha cambiado su estado a: {$statusLabel}")
            ->action('Ver Innovación', route('innovations.show', $this->innovation->id))
            ->line('Gracias por tu aporte a la innovación docente.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusLabel = match($this->innovation->status) {
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada',
            'en_revision' => 'En Revisión',
            default => $this->innovation->status
        };

        return [
            'innovation_id' => $this->innovation->id,
            'title' => 'Estado de innovación actualizado',
            'message' => 'Tu innovación "' . $this->innovation->title . '" ha cambiado su estado a: ' . $statusLabel,
            'link' => route('innovations.show', $this->innovation->id),
        ];
    }
}
