<?php

namespace App\Traits;

trait HasMeetingStatusLabel
{
    public function getStatusLabelAttribute(): string
    {
        return $this->is_expired ? 'Completada' : ucfirst($this->status ?: 'pendiente');
    }
}
