<?php

namespace App\Traits;

trait HasMeetingIsUpcoming
{
    public function getIsUpcomingAttribute(): bool
    {
        return $this->status === 'pendiente' && $this->meeting_date && $this->meeting_date->isFuture();
    }
}
