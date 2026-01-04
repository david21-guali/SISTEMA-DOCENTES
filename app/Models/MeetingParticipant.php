<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\MeetingParticipant
 *
 * @property string $attendance
 * @property string|null $rejection_reason
 */
class MeetingParticipant extends Pivot
{
    protected $table = 'meeting_profile';
}
