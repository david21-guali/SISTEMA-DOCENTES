<?php

namespace App\Traits;

trait HasMeetingIsPast
{
    public function getIsPastAttribute(): bool
    {
        return $this->meeting_date && $this->meeting_date->isPast();
    }
}
