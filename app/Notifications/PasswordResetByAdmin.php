<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetByAdmin extends Notification
{
    use Queueable;

    protected $newPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔐 Tu contraseña ha sido restablecida')
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('El administrador ha restablecido tu contraseña manualmente.')
            ->line('Tu nueva contraseña temporal es:')
            ->line('**' . $this->newPassword . '**')
            ->line('Por favor, inicia sesión y cambia tu contraseña lo antes posible.')
            ->action('Iniciar Sesión', route('login'))
            ->line('Si no solicitaste este cambio, contacta al administrador inmediatamente.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'password_reset_by_admin',
            'title' => 'Contraseña Restablecida',
            'message' => 'El administrador ha restablecido tu contraseña.',
            'link' => route('login'),
        ];
    }
}
