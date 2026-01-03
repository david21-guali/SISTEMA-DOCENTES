<?php

namespace App\Notifications;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingResponse extends Notification
{
    use Queueable;
    
    /** @var Meeting */
    public $meeting;

    /** @var User */
    public $responder;

    /** @var string */
    public $status;

    /** @var string|null */
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Meeting $meeting, User $responder, string $status, ?string $reason = null)
    {
        $this->meeting = $meeting;
        $this->responder = $responder;
        $this->status = $status;
        $this->reason = $reason;
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
        $action = $this->status === 'confirmada' ? 'confirmado su asistencia' : 'rechazado la invitación';
        $emoji = $this->status === 'confirmada' ? '✅' : '❌';

        $mail = (new MailMessage)
            ->subject($emoji . ' Respuesta a Reunión: ' . $this->meeting->title)
            ->greeting('Hola ' . $notifiable->name)
            ->line($this->responder->name . ' ha ' . $action . ' a la reunión:')
            ->line('**' . $this->meeting->title . '**');

        if ($this->status === 'rechazada' && $this->reason) {
            $mail->line('**Motivo:** ' . $this->reason);
        }

        return $mail
            ->line('📅 Fecha: ' . $this->meeting->meeting_date->format('d/m/Y H:i'))
            ->action('Ver Detalles', route('meetings.show', $this->meeting));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $action = $this->status === 'confirmada' ? 'confirmado asistencia' : 'rechazado invitación';
        $message = $this->responder->name . ' ha ' . $action . ' a: ' . $this->meeting->title;

        if ($this->status === 'rechazada' && $this->reason) {
            $message .= ' (Motivo: ' . Str::limit($this->reason, 30) . ')';
        }

        return [
            'type' => 'meeting_response',
            'meeting_id' => $this->meeting->id,
            'title' => 'Respuesta a Reunión',
            'message' => $message,
            'status' => $this->status,
            'reason' => $this->reason,
            'responder_id' => $this->responder->id,
            'link' => route('meetings.show', $this->meeting),
        ];
    }
}
