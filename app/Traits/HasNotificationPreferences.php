<?php

namespace App\Traits;

/**
 * Trait to handle preference-aware notification delivery.
 */
trait HasNotificationPreferences
{
    /**
     * Get the notification's delivery channels.
     *
     * @param object $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // La categoría debe estar definida en la clase de notificación (ej: 'projects', 'tasks')
        // @phpstan-ignore-next-line
        $category = property_exists($this, 'category') ? $this->category : 'general';
        
        // Obtener las preferencias del usuario desde su perfil
        $prefs = $notifiable->profile->notification_preferences ?? [];

        // 1. Si la categoría está desactivada explícitamente, no enviar nada
        if (isset($prefs[$category]) && $prefs[$category] === false) {
            return [];
        }

        // 2. Por defecto, siempre se guarda en la base de datos (web) si la categoría está activa
        $channels = ['database'];

        // 3. Añadir el canal de correo ('mail') solo si el interruptor global de email está activo
        // y la clase de notificación tiene implementado el método toMail()
        // @phpstan-ignore-next-line
        if (($prefs['email_enabled'] ?? true) && method_exists($this, 'toMail')) {
            $channels[] = 'mail';
        }

        return $channels;
    }
}
