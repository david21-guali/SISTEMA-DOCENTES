<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPasswordResetRequest extends Notification
{
    use \App\Traits\HasNotificationPreferences;

    public string $category = 'reminders';

    /** @var \App\Models\User */
    protected $userRequester;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $userRequester)
    {
        $this->userRequester = $userRequester;
    }



    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔐 Solicitud de Restablecimiento de Contraseña')
            ->greeting('Hola Administrador,')
            ->line('El usuario **' . $this->userRequester->name . '** (' . $this->userRequester->email . ') ha solicitado restablecer su contraseña.')
            ->line('Por favor, ponte en contacto con el usuario o restablece su contraseña manualmente.')
            ->action('Ver Usuario', route('users.show', $this->userRequester));
    }

    /**
     * Get the array representation of the notification.
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'password_reset_request',
            'user_id' => $this->userRequester->id,
            'title' => 'Solicitud de Contraseña',
            'message' => 'El usuario ' . $this->userRequester->name . ' ha solicitado restablecer su contraseña.',
            'link' => route('users.show', $this->userRequester),
        ];
    }
}
