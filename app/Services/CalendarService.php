<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\Meeting;
use Illuminate\Support\Collection;

class CalendarService
{
    /**
     * Get all calendar events for a user in FullCalendar format.
     * 
     * @param \App\Models\User $user
     * @return array<int, array<string, mixed>>
     */
    public function getEventsForUser($user): array
    {
        return $this->getUnifiedEvents($user)->map(fn($e) => $e['fc'])->values()->all();
    }

    /**
     * Generate an ICS calendar file content for a specific user.
     * 
     * @param \App\Models\User $user
     * @return string
     */
    public function generateIcs($user): string
    {
        $eol = "\r\n";
        $header = "BEGIN:VCALENDAR{$eol}VERSION:2.0{$eol}PRODID:-//SISTEMA-DOCENTES//Calendar//ES{$eol}CALSCALE:GREGORIAN{$eol}METHOD:PUBLISH{$eol}X-WR-CALNAME:Mi Cronograma Académico{$eol}";
        $body = $this->getUnifiedEvents($user)->map(fn($e) => $e['ics'])->implode('');
        
        return "{$header}{$body}END:VCALENDAR";
    }

    /**
     * Aggregates projects, tasks, and meetings into a unified event collection.
     * 
     * @param \App\Models\User $user
     * @return \Illuminate\Support\Collection<int, array{fc: array<string, mixed>, ics: string}>
     */
    private function getUnifiedEvents($user): \Illuminate\Support\Collection
    {
        return collect()
            ->concat(Project::forUser($user)->whereNotNull('end_date')->get())
            ->concat(Task::forUser($user)->where('status', '!=', 'completada')->get())
            ->concat(\App\Models\Meeting::forUser($user)->where('status', 'pendiente')->get())
            ->map(fn($m) => $this->mapToServiceFormats($m))
            ->values();
    }

    /**
     * Maps a model instance to both FullCalendar and ICS formats.
     * 
     * @param mixed $m
     * @return array{fc: array<string, mixed>, ics: string}
     */
    private function mapToServiceFormats($m): array
    {
        $dt = $m->end_date ?: ($m->date ?: $m->created_at);
        $uid = strtolower(class_basename($m)) . "-{$m->id}";
        
        $summary = $m->title ?: ($m->name ?: 'Evento');
        if (in_array(class_basename($m), ['Project', 'Task'])) {
            $summary = "Entrega: {$summary}";
        }

        return [
            'fc'  => $m->toCalendarEvent(),
            'ics' => $this->formatVevent(
                $uid, 
                $dt->format('Ymd\THis'), 
                $dt->copy()->addHour()->format('Ymd\THis'), 
                $summary, 
                $m->description ?: '', 
                $m->location ?: null
            )
        ];
    }

    /**
     * Formats a single event into VEVENT ICS format.
     * 
     * @param string $uid
     * @param string $start
     * @param string $end
     * @param string $summary
     * @param string $desc
     * @param string|null $loc
     * @return string
     */
    private function formatVevent(string $uid, string $start, string $end, string $summary, string $desc, ?string $loc): string
    {
        $eol = "\r\n";
        $ics = "BEGIN:VEVENT{$eol}UID:{$uid}@sistema-docentes{$eol}DTSTAMP:" . date('Ymd\THis\Z') . "{$eol}DTSTART:{$start}{$eol}DTEND:{$end}{$eol}";
        $ics .= "SUMMARY:" . $this->escape($summary) . "{$eol}DESCRIPTION:" . $this->escape($desc) . "{$eol}";
        
        if ($loc) {
            $ics .= "LOCATION:" . $this->escape($loc) . "{$eol}";
        }

        return $ics . "END:VEVENT{$eol}";
    }

    /**
     * Escapes special characters for ICS compatibility.
     * 
     * @param string $str
     * @return string
     */
    private function escape(string $str): string 
    { 
        return str_replace([",", ";", "\n", "\r"], ["\\,", "\\;", "\\n", ""], $str); 
    }
}
