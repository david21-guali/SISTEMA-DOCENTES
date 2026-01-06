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
        $events = [];

        // 1. Projects (Deadlines)
        $projects = Project::where('status', '!=', 'finalizado')->get();
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
        $tasks = Task::where('status', '!=', 'completada')->get();
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
        $meetings = Meeting::where('status', 'pendiente')->get();
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
}
