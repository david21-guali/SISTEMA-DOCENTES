<?php

namespace App\Notifications;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingResponse extends Notification implements ShouldQueue
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
        return (new \App\Services\NotificationPreferenceService())->getChannels($notifiable, 'meetings');
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new \App\Services\MeetingNotificationFormatService())->formatMail($this, $notifiable);
    }

    /**
     * Get array representation for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'meeting_response',
            'message'      => (new \App\Services\MeetingNotificationFormatService())->formatArrayMessage($this),
            'status'       => $this->status,
            'meeting_id'   => $this->meeting->id,
            'responder_id' => $this->responder->id,
            'link'         => route('meetings.show', $this->meeting),
        ];
    }
}
