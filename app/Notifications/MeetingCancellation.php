<?php

namespace App\Notifications;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingCancellation extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var Meeting */
    protected $meeting;
    /** @var string|null */
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Meeting $meeting, ?string $reason = null)
    {
        $this->meeting = $meeting;
        $this->reason = $reason;
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
    /**
     * Get the mail representation of the notification.
     * 
     * @param \App\Models\User $notifiable
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('🚫 Reunión Cancelada: ' . $this->meeting->title)
            ->greeting('Hola ' . $notifiable->name)
            ->line('La siguiente reunión ha sido cancelada por el organizador:')
            ->line('**' . $this->meeting->title . '**')
            ->line('📅 Originalmente programada para: ' . $this->meeting->meeting_date->format('d/m/Y H:i'));

        if ($this->reason) {
            $mail->line('**Motivo de cancelación:** ' . $this->reason);
        }

        return $mail->line('Lamentamos los inconvenientes.');
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
        $message = 'La reunión "' . $this->meeting->title . '" ha sido cancelada.';
        
        if ($this->reason) {
            $message .= ' Motivo: ' . $this->reason;
        }

        return [
            'type' => 'meeting_cancellation',
            'meeting_id' => $this->meeting->id,
            'title' => 'Reunión Cancelada',
            'message' => $message,
            'reason' => $this->reason,
            'link' => route('meetings.show', $this->meeting),
        ];
    }
}
