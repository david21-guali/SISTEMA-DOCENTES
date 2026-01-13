<?php

namespace App\Traits;

trait HasMeetingStatusColor
{
    public function getStatusColorAttribute(): string
    {
        return $this->is_expired ? 'success' : (['completada' => 'success', 'cancelada' => 'secondary'][$this->status] ?? 'primary');
    }
}
