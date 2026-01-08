<?php

namespace App\Notifications;

use App\Models\Innovation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InnovationReviewRequested extends Notification
{
    use Queueable;

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
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'innovation_id' => $this->innovation->id,
            'title' => 'Nueva solicitud de revisión',
            'message' => 'El docente ' . ($this->innovation->profile->user->name ?? 'N/A') . ' ha solicitado la revisión de la innovación: ' . $this->innovation->title,
            'link' => route('innovations.show', $this->innovation->id),
        ];
    }
}
