<?php

namespace App\Traits;

/**
 * Trait to provide calendar event mapping functionality for models.
 * 
 * @property int $id
 * @property string|null $title
 * @property string|null $name
 * @property \Carbon\Carbon|string|null $start_date
 * @property \Carbon\Carbon|string|null $end_date
 * @property \Carbon\Carbon|string|null $due_date
 * @property \Carbon\Carbon|string|null $meeting_date
 * @property \Carbon\Carbon|string|null $date
 * @property \Carbon\Carbon $created_at
 * @property string|null $description
 * @property string|null $location
 */
trait HasCalendarEvents
{
    /**
     * Convert the model instance into a FullCalendar compatible event array.
     * 
     * @return array<string, mixed>
     */
    public function toCalendarEvent(): array
    {
        // USER REQUEST: Show only the DEADLINE/END DATE on the calendar (not a range).
        // Priority: 1. End Date (Deadline), 2. Start Date (Meeting), 3. Date, 4. Created At
        
        $targetDate = $this->end_date ?: ($this->due_date ?: ($this->meeting_date ?: ($this->date ?: $this->created_at)));

        if (!$targetDate instanceof \Carbon\Carbon) {
            $targetDate = \Carbon\Carbon::parse($targetDate);
        }

        return [
            'id'    => $this->id,
            'title' => $this->title ?: ($this->name ?: 'Evento'),
            'start' => $targetDate->toIso8601String(), // Start at the deadline
            'end'   => null, // No end date = single point in time
            'url'   => $this->getCalendarUrl(),
            'color' => $this->getCalendarColor(),
            'allDay' => true, // Force visual block styling
            'type'  => match(class_basename($this)) {
                'Project' => 'Proyecto',
                'Task'    => 'Tarea',
                'Meeting' => 'Reunión',
                default   => class_basename($this),
            },
        ];
    }

    abstract protected function getCalendarUrl(): string;
    abstract protected function getCalendarColor(): string;
}
