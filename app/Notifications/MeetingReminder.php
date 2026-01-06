<?php

namespace App\Notifications;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingReminder extends Notification
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
        
        if (!$this->isNotificationEnabled($prefs)) {
            return [];
        }

        return $this->getEnabledChannels($prefs);
    }

    /**
     * Check if meeting notifications are globally enabled for the user.
     * 
     * @param array $prefs
     * @return bool
     */
    private function isNotificationEnabled(array $prefs): bool
    {
        return $prefs['meetings'] ?? true;
    }

    /**
     * Determine active channels based on user preferences.
     * 
     * @param array $prefs
     * @return array
     */
    private function getEnabledChannels(array $prefs): array
    {
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
        $hoursUntil = now()->diffInHours($this->meeting->meeting_date);
        $timeMessage = $hoursUntil > 24 
            ? 'en ' . now()->diffInDays($this->meeting->meeting_date) . ' días'
            : 'en ' . $hoursUntil . ' horas';

        return (new MailMessage)
            ->subject('Recordatorio de Reunión: ' . $this->meeting->title)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Te recordamos que tienes una reunión programada ' . $timeMessage . ':')
            ->line('**' . $this->meeting->title . '**')
            ->line('📅 Fecha: ' . $this->meeting->meeting_date->format('d/m/Y H:i'))
            ->line('📍 Ubicación: ' . ($this->meeting->location ?? 'Por definir'))
            ->line($this->meeting->project ? '📁 Proyecto: ' . $this->meeting->project->title : '')
            ->action('Ver Reunión', route('meetings.show', $this->meeting))
            ->line('¡No olvides asistir!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'meeting_reminder',
            'meeting_id' => $this->meeting->id,
            'title' => 'Recordatorio de Reunión',
            'meeting_date' => $this->meeting->meeting_date->toISOString(),
            'location' => $this->meeting->location,
            'project_id' => $this->meeting->project_id,
            'project_title' => $this->meeting->project?->title,
            'message' => 'Recordatorio: Reunión "' . $this->meeting->title . '" el ' . $this->meeting->meeting_date->format('d/m/Y H:i'),
            'link' => route('meetings.show', $this->meeting),
        ];
    }
}
