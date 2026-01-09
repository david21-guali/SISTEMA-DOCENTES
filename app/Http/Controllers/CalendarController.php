<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Meeting;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @param \App\Services\CalendarService $cs
     */
    public function __construct(protected \App\Services\CalendarService $cs) {}

    /**
     * Display the calendar view.
     * 
     * @return \Illuminate\View\View
     */
    public function index(): \Illuminate\View\View 
    { 
        return view('calendar.index'); 
    }

    /**
     * Get JSON events for FullCalendar.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function events(): \Illuminate\Http\JsonResponse 
    { 
        $events = $this->cs->getEventsForUser(auth()->user());
        return response()->json($events); 
    }

    /**
     * Export calendar events to ICS format.
     * 
     * @return \Illuminate\Http\Response
     */
    public function exportIcs(): \Illuminate\Http\Response 
    {
        $icsContent = $this->cs->generateIcs(auth()->user());
        return response($icsContent)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="calendario.ics"');
    }
}
