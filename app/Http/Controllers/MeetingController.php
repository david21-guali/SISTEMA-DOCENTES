<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use App\Notifications\MeetingInvitation;
use App\Notifications\MeetingReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    protected $queryService;
    protected $actionService;

    public function __construct(
        \App\Services\MeetingQueryService $queryService,
        \App\Services\MeetingActionService $actionService
    ) {
        $this->queryService = $queryService;
        $this->actionService = $actionService;
    }

    /**
     * Display a listing of the meetings.
     */
    public function index(Request $request)
    {
        $meetings = $this->queryService->getMeetings($request->all());
        $projects = $this->queryService->getProjects();
        $stats = $this->queryService->getStats();

        return view('app.back.meetings.index', compact('meetings', 'projects', 'stats'));
    }

    /**
     * Show the form for creating a new meeting.
     */
    public function create(Request $request)
    {
        $projects = $this->queryService->getProjects();
        $users = $this->queryService->getEligibleUsers();
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;

        return view('app.back.meetings.create', compact('projects', 'users', 'selectedProject'));
    }

    /**
     * Store a newly created meeting in storage.
     */
    public function store(\App\Http\Requests\StoreMeetingRequest $request)
    {
        $meeting = $this->actionService->createMeeting($request->validated());
        return redirect()->route('meetings.show', $meeting)->with('success', 'Reunión programada.');
    }

    /**
     * Display the specified meeting.
     */
    public function show(Meeting $meeting)
    {
        $meeting->load(['project', 'creator.user', 'participants.user']);
        return view('app.back.meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the meeting.
     */
    public function edit(Meeting $meeting)
    {
        $this->authorizeOwner($meeting);
        $projects = $this->queryService->getProjects();
        $users = $this->queryService->getEligibleUsers();

        return view('app.back.meetings.edit', compact('meeting', 'projects', 'users'));
    }

    /**
     * Update the specified meeting in storage.
     */
    public function update(\App\Http\Requests\UpdateMeetingRequest $request, Meeting $meeting)
    {
        $this->authorizeOwner($meeting);
        $this->actionService->updateMeeting($meeting, $request->validated());
        return redirect()->route('meetings.show', $meeting)->with('success', 'Actualizada.');
    }

    /**
     * Remove the specified meeting from storage.
     */
    public function destroy(Meeting $meeting)
    {
        $this->authorizeOwner($meeting);
        $meeting->delete();
        return redirect()->route('meetings.index')->with('success', 'Eliminada.');
    }

    /**
     * Update attendance status for current user.
     */
    public function updateAttendance(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'attendance' => 'required|in:confirmada,rechazada',
            'rejection_reason' => 'nullable|string|required_if:attendance,rechazada',
        ]);

        $message = $this->actionService->updateAttendance($meeting, $validated);
        return back()->with('success', $message);
    }

    public function complete(Request $request, Meeting $meeting)
    {
        $this->authorizeOwner($meeting);
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'attended' => 'nullable|array',
            'attended.*' => 'exists:users,id',
        ]);

        $this->actionService->completeMeeting($meeting, $validated);
        return redirect()->route('meetings.show', $meeting)->with('success', 'Completada.');
    }

    public function sendReminders(Meeting $meeting)
    {
        $this->authorizeOwner($meeting);
        $this->actionService->sendReminders($meeting);
        return back()->with('success', 'Recordatorios enviados.');
    }

    public function cancel(Request $request, Meeting $meeting)
    {
        $this->authorizeOwner($meeting);
        $validated = $request->validate(['cancellation_reason' => 'required|string|max:1000']);
        $this->actionService->cancelMeeting($meeting, $validated['cancellation_reason']);
        return redirect()->route('meetings.show', $meeting)->with('success', 'Cancelada.');
    }

    private function authorizeOwner(Meeting $meeting)
    {
        if ($meeting->created_by !== Auth::user()->profile->id && !Auth::user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }
    }
}
