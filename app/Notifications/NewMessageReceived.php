<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageReceived extends Notification
{
    use Queueable, \App\Traits\HasNotificationPreferences;

    public string $category = 'messages';

    /** @var \App\Models\Message */
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }



    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo mensaje de chat: ' . $this->message->sender->name)
            ->line("Has recibido un nuevo mensaje de {$this->message->sender->name}.")
            ->line('"' . \Illuminate\Support\Str::limit($this->message->content, 100) . '"')
            ->action('Responder en el Chat', route('chat.show', $this->message->sender_id))
            ->line('No respondas a este correo directamente.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'title' => 'Nuevo mensaje de chat',
            'message' => 'Has recibido un nuevo mensaje de ' . $this->message->sender->name,
            'link' => route('chat.show', $this->message->sender_id),
        ];
    }
}
