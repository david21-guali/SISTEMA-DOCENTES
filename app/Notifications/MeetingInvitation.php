<?php

namespace App\Notifications;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $meeting;

    /**
     * Create a new notification instance.
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $prefs = $notifiable->profile->notification_preferences ?? [];
        
        if (!($prefs['meetings'] ?? true)) {
            return [];
        }

        $channels = ['database'];
        if ($prefs['email_enabled'] ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invitación a Reunión: ' . $this->meeting->title)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Has sido invitado a una reunión:')
            ->line('**' . $this->meeting->title . '**')
            ->line('📅 Fecha: ' . $this->meeting->meeting_date->format('d/m/Y H:i'))
            ->line('📍 Ubicación: ' . ($this->meeting->location ?? 'Por definir'))
            ->line($this->meeting->project ? '📁 Proyecto: ' . $this->meeting->project->title : '')
            ->action('Ver Reunión', route('meetings.show', $this->meeting))
            ->line('Por favor confirma tu asistencia.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'meeting_invitation',
            'meeting_id' => $this->meeting->id,
            'title' => 'Invitación a Reunión',
            'meeting_date' => $this->meeting->meeting_date->toISOString(),
            'location' => $this->meeting->location,
            'project_id' => $this->meeting->project_id,
            'project_title' => $this->meeting->project?->title,
            'created_by' => $this->meeting->creator->name,
            'message' => 'Has sido invitado a la reunión: ' . $this->meeting->title . ' - ' . $this->meeting->meeting_date->format('d/m/Y H:i'),
            'link' => route('meetings.show', $this->meeting),
        ];
    }
}
