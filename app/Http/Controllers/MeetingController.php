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
    protected \App\Services\MeetingQueryService $queryService;
    protected \App\Services\MeetingActionService $actionService;

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
    /**
     * Display a listing of the meetings.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $meetings = $this->queryService->getMeetings($request->all());
        $projects = $this->queryService->getProjects();
        $stats = $this->queryService->getStats();

        return view('app.back.meetings.index', compact('meetings', 'projects', 'stats'));
    }

    /**
     * Show the form for creating a new meeting.
     */
    /**
     * Show the form for creating a new meeting.
     */
    public function create(Request $request): \Illuminate\View\View
    {
        $projects = $this->queryService->getProjects()->load('team.user');
        $users = $this->queryService->getEligibleUsers();
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;

        return view('app.back.meetings.create', compact('projects', 'users', 'selectedProject'));
    }

    /**
     * Store a newly created meeting in storage.
     */
    /**
     * Store a newly created meeting in storage.
     */
    public function store(\App\Http\Requests\StoreMeetingRequest $request): \Illuminate\Http\RedirectResponse
    {
        $meeting = $this->actionService->createMeeting($request->validated());
        return redirect()->route('meetings.show', $meeting)->with('success', 'Reunión programada.');
    }

    /**
     * Display the specified meeting.
     */
    /**
     * Display the specified meeting.
     */
    public function show(Meeting $meeting): \Illuminate\View\View
    {
        $meeting->load(['project', 'creator.user', 'participants.user']);
        return view('app.back.meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the meeting.
     */
    /**
     * Show the form for editing the meeting.
     */
    public function edit(Meeting $meeting): \Illuminate\View\View
    {
        $this->authorize('update', $meeting);
        return view('app.back.meetings.edit', [
            'meeting'  => $meeting,
            'projects' => $this->queryService->getProjects()->load('team.user'),
            'users'    => $this->queryService->getEligibleUsers()
        ]);
    }

    public function update(\App\Http\Requests\UpdateMeetingRequest $request, Meeting $meeting): \Illuminate\Http\RedirectResponse
    {
        $this->actionService->updateMeeting($meeting, $request->validated());
        return redirect()->route('meetings.show', $meeting)->with('success', 'Actualizada.');
    }

    public function destroy(Meeting $meeting): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('delete', $meeting);
        $meeting->delete();
        return redirect()->route('meetings.index')->with('success', 'Eliminada.');
    }

    public function updateAttendance(Request $request, Meeting $meeting): \Illuminate\Http\RedirectResponse
    {
        $message = $this->actionService->updateAttendance($meeting, $request->validate([
            'attendance'       => 'required|in:confirmada,rechazada',
            'rejection_reason' => 'nullable|string|required_if:attendance,rechazada',
        ]));
        return back()->with('success', $message);
    }

    public function complete(Request $request, Meeting $meeting): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $meeting);
        $this->actionService->completeMeeting($meeting, $request->validate([
            'notes'      => 'nullable|string',
            'attended'   => 'nullable|array',
            'attended.*' => 'exists:users,id',
        ]));
        return redirect()->route('meetings.show', $meeting)->with('success', 'Completada.');
    }

    public function sendReminders(Meeting $meeting): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $meeting);
        $this->actionService->sendReminders($meeting);
        return back()->with('success', 'Recordatorios enviados.');
    }

    public function cancel(Request $request, Meeting $meeting): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $meeting);
        $this->actionService->cancelMeeting($meeting, $request->validate(['cancellation_reason' => 'required|string|max:1000'])['cancellation_reason']);
        return redirect()->route('meetings.show', $meeting)->with('success', 'Cancelada.');
    }
}
