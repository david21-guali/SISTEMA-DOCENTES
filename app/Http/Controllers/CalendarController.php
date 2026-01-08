<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Meeting;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        return view('calendar.index');
    }

    public function events(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $events = [];

        // 1. Projects (Deadlines)
        $projects = Project::forUser($user)->where('status', '!=', 'finalizado')->get();
        foreach ($projects as $project) {
            $events[] = [
                'id' => 'project-' . $project->id,
                'title' => 'Entrega: ' . $project->title,
                'start' => $project->end_date->format('Y-m-d'),
                'url' => route('projects.show', $project),
                'backgroundColor' => '#4e73df', // Primary Blue
                'borderColor' => '#4e73df',
                'textColor' => '#ffffff',
                'type' => 'Proyecto'
            ];
        }

        // 2. Tasks (Due Dates)
        $tasks = Task::forUser($user)->where('status', '!=', 'completada')->get();
        foreach ($tasks as $task) {
            $color = '#f6c23e'; // Warning Yellow by default
            if ($task->priority === 'alta') $color = '#e74a3b'; // Red
            if ($task->priority === 'baja') $color = '#1cc88a'; // Green

            $events[] = [
                'id' => 'task-' . $task->id,
                'title' => 'Tarea: ' . $task->title,
                'start' => $task->due_date->format('Y-m-d'),
                'url' => route('tasks.show', $task),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'description' => $task->description,
                'type' => 'Tarea'
            ];
        }

        // 3. Meetings (Scheduled Time)
        $meetings = Meeting::forUser($user)->where('status', 'pendiente')->get();
        foreach ($meetings as $meeting) {
            $events[] = [
                'id' => 'meeting-' . $meeting->id,
                'title' => 'Reunión: ' . $meeting->title,
                'start' => $meeting->meeting_date->format('Y-m-d\TH:i:s'),
                // Default to 1 hour duration if no end time exists
                'end' => $meeting->meeting_date->copy()->addHour()->format('Y-m-d\TH:i:s'), 
                'url' => route('meetings.show', $meeting),
                'backgroundColor' => '#36b9cc', // Info Cyan
                'borderColor' => '#36b9cc',
                'textColor' => '#ffffff',
                'type' => 'Reunión'
            ];
        }

        return response()->json($events);
    }

    public function exportIcs(): \Illuminate\Http\Response
    {
        $user = auth()->user();
        $eol = "\r\n";
        $icsContent = "BEGIN:VCALENDAR" . $eol;
        $icsContent .= "VERSION:2.0" . $eol;
        $icsContent .= "PRODID:-//SISTEMA-DOCENTES//Calendar//ES" . $eol;
        $icsContent .= "CALSCALE:GREGORIAN" . $eol;
        $icsContent .= "METHOD:PUBLISH" . $eol;
        $icsContent .= "X-WR-CALNAME:Mi Cronograma Académico" . $eol;

        // 1. Projects (All-day events)
        $projects = Project::forUser($user)->whereNotNull('end_date')->get();
        foreach ($projects as $project) {
            $icsContent .= "BEGIN:VEVENT" . $eol;
            $icsContent .= "UID:project-" . $project->id . "@sistema-docentes" . $eol;
            $icsContent .= "DTSTAMP:" . date('Ymd\THis\Z') . $eol;
            $icsContent .= "DTSTART;VALUE=DATE:" . $project->end_date->format('Ymd') . $eol;
            // End date in ICS is exclusive, so for all-day events it should be next day
            $icsContent .= "DTEND;VALUE=DATE:" . $project->end_date->copy()->addDay()->format('Ymd') . $eol;
            $icsContent .= "SUMMARY:Entrega: " . $this->escapeIcs($project->title) . $eol;
            $icsContent .= "DESCRIPTION:Portafolio de Proyecto: " . $this->escapeIcs($project->description) . $eol;
            $icsContent .= "END:VEVENT" . $eol;
        }

        // 2. Tasks (All-day events)
        $tasks = Task::forUser($user)->where('status', '!=', 'completada')->whereNotNull('due_date')->get();
        foreach ($tasks as $task) {
            $icsContent .= "BEGIN:VEVENT" . $eol;
            $icsContent .= "UID:task-" . $task->id . "@sistema-docentes" . $eol;
            $icsContent .= "DTSTAMP:" . date('Ymd\THis\Z') . $eol;
            $icsContent .= "DTSTART;VALUE=DATE:" . $task->due_date->format('Ymd') . $eol;
            $icsContent .= "DTEND;VALUE=DATE:" . $task->due_date->copy()->addDay()->format('Ymd') . $eol;
            $icsContent .= "SUMMARY:Tarea: " . $this->escapeIcs($task->title) . $eol;
            $icsContent .= "DESCRIPTION:Prioridad: " . ucfirst($task->priority) . "\\n" . $this->escapeIcs($task->description) . $eol;
            $icsContent .= "END:VEVENT" . $eol;
        }

        // 3. Meetings (Timed events)
        $meetings = Meeting::forUser($user)->where('status', 'pendiente')->whereNotNull('meeting_date')->get();
        foreach ($meetings as $meeting) {
            $icsContent .= "BEGIN:VEVENT" . $eol;
            $icsContent .= "UID:meeting-" . $meeting->id . "@sistema-docentes" . $eol;
            $icsContent .= "DTSTAMP:" . date('Ymd\THis\Z') . $eol;
            // Using floating time (no Z) for meetings as they are usually local
            $icsContent .= "DTSTART:" . $meeting->meeting_date->format('Ymd\THis') . $eol;
            $icsContent .= "DTEND:" . $meeting->meeting_date->copy()->addHour()->format('Ymd\THis') . $eol;
            $icsContent .= "SUMMARY:Reunión: " . $this->escapeIcs($meeting->title) . $eol;
            $icsContent .= "DESCRIPTION:" . $this->escapeIcs($meeting->description) . $eol;
            $icsContent .= "LOCATION:" . $this->escapeIcs($meeting->location ?? 'N/A') . $eol;
            $icsContent .= "END:VEVENT" . $eol;
        }

        $icsContent .= "END:VCALENDAR";

        return response($icsContent)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="mi-calendario-' . date('Y-m-d') . '.ics"');
    }

    private function escapeIcs(?string $string): string
    {
        return str_replace([",", ";", "\n", "\r"], ["\\,", "\\;", "\\n", ""], $string ?? '');
    }
}
