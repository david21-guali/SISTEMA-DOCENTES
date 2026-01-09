<?php

namespace App\Observers;

use App\Models\Innovation;
use App\Models\User;
use App\Notifications\InnovationStatusChanged;
use App\Notifications\InnovationReviewRequested;
use Illuminate\Support\Facades\Notification;

/**
 * Observer to handle automated notifications for Innovation model changes.
 */
class InnovationObserver
{

    /**
     * Handle the Innovation "updated" event.
     * 
     * @param Innovation $innovation
     * @return void
     */
    public function updated(Innovation $innovation): void
    {
        if (!$innovation->wasChanged('status')) return;

        match ($innovation->status) {
            'en_revision' => Notification::send(User::role(['admin', 'coordinador'])->get(), new InnovationReviewRequested($innovation)),
            'aprobada', 'rechazada' => $innovation->profile->user->notify(new InnovationStatusChanged($innovation)),
            default => null
        };
    }
}
