<?php

namespace App\Traits;

trait HasMeetingExpiration
{
    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'pendiente' && $this->meeting_date && $this->meeting_date->copy()->addDay()->isPast();
    }
}
