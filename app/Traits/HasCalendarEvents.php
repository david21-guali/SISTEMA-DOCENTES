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
        $start = $this->start_date ?: ($this->date ?: $this->created_at);
        return [
            'id'    => $this->id,
            'title' => $this->title ?: ($this->name ?: 'Evento'),
            'start' => $start,
            'end'   => $this->end_date ?: $this->date,
            'url'   => $this->getCalendarUrl(),
            'color' => $this->getCalendarColor(),
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
