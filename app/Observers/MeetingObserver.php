<?php

namespace App\Observers;

use App\Models\Meeting;
use App\Notifications\MeetingInvitation;
use App\Notifications\MeetingReminder;
use Illuminate\Support\Facades\Notification;

class MeetingObserver
{


    public function updated(Meeting $meeting): void
    {
        // Si se cambia la fecha o el enlace, enviar recordatorio/notificación de cambio
        if ($meeting->wasChanged(['scheduled_at', 'meeting_link'])) {
             $participants = $meeting->participants->pluck('user');
             Notification::send($participants, new MeetingReminder($meeting));
        }
    }
}
