<?php

namespace App\Observers;

use App\Models\Message;
use App\Notifications\NewMessageReceived;

class MessageObserver
{
    public function created(Message $message): void
    {
        if ($message->receiver && $message->receiver->user) {
            $message->receiver->user->notify(new NewMessageReceived($message));
        }
    }
}
