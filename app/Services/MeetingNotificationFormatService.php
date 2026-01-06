<?php

namespace App\Services;

use App\Notifications\MeetingResponse;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

/**
 * Service to format meeting notifications content.
 * Aiming for class CC < 5.
 */
class MeetingNotificationFormatService
{
    /**
     * Format a mail message for a meeting response.
     * 
     * @param MeetingResponse $notif
     * @param object $notifiable
     * @return MailMessage
     */
    public function formatMail(MeetingResponse $notif, object $notifiable): MailMessage
    {
        $isConfirmed = $notif->status === 'confirmada';
        $emoji = $isConfirmed ? '✅' : '❌';
        $action = $isConfirmed ? 'confirmado su asistencia' : 'rechazado la invitación';

        $mail = (new MailMessage)
            ->subject($emoji . ' Respuesta: ' . $notif->meeting->title)
            ->greeting('Hola ' . $notifiable->name)
            ->line($notif->responder->name . ' ha ' . $action . ' a: ' . $notif->meeting->title);

        if ($notif->status === 'rechazada' && $notif->reason) {
            $mail->line('**Motivo:** ' . $notif->reason);
        }

        return $mail->action('Ver Detalles', route('meetings.show', $notif->meeting));
    }

    /**
     * Format array message for database notifications.
     * 
     * @param MeetingResponse $notif
     * @return string
     */
    public function formatArrayMessage(MeetingResponse $notif): string
    {
        $action = ($notif->status === 'confirmada') ? 'confirmado asistencia' : 'rechazado invitación';
        $message = $notif->responder->name . ' ha ' . $action . ' a: ' . $notif->meeting->title;

        if ($notif->status === 'rechazada' && $notif->reason) {
            $message .= ' (Motivo: ' . Str::limit($notif->reason, 20) . ')';
        }

        return $message;
    }
}
