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
    /**
     * Display a listing of the meetings.
     */
    public function index(Request $request)
    {
        $query = Meeting::with(['project', 'creator', 'participants.user'])
            ->forUser(Auth::id());

        // Filtrar por estado
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filtrar por proyecto
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Ordenar por fecha
        $query->orderBy('meeting_date', $request->order ?? 'asc');

        $meetings = $query->paginate(10);
        $projects = Project::orderBy('title')->get();

        // Estadísticas
        $stats = [
            'upcoming' => Meeting::forUser(Auth::id())->upcoming()->count(),
            'completed' => Meeting::forUser(Auth::id())->completed()->count(),
            'total' => Meeting::forUser(Auth::id())->count(),
        ];

        return view('app.back.meetings.index', compact('meetings', 'projects', 'stats'));
    }

    /**
     * Show the form for creating a new meeting.
     */
    public function create(Request $request)
    {
        $projects = Project::orderBy('title')->get();
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'coordinador', 'docente']);
        })->orderBy('name')->get();

        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;

        return view('app.back.meetings.create', compact('projects', 'users', 'selectedProject'));
    }

    /**
     * Store a newly created meeting in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'project_id' => 'nullable|exists:projects,id',
            'meeting_date' => 'required|date',
            'location' => 'required|string|max:255',
            'participants' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $others = collect($value)->filter(fn($id) => $id != Auth::id());
                    if ($others->isEmpty()) {
                        $fail('Debe invitar al menos a un participante adicional.');
                    }
                },
            ],
            'participants.*' => 'exists:users,id',
        ], [], [
            'title' => 'título',
            'description' => 'descripción',
            'meeting_date' => 'fecha y hora',
            'location' => 'ubicación / enlace',
            'participants' => 'participantes',
        ]);

        $meeting = Meeting::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'project_id' => $validated['project_id'],
            'meeting_date' => $validated['meeting_date'],
            'location' => $validated['location'],
            'created_by' => Auth::user()->profile->id,
            'status' => 'pendiente',
        ]);

        // Agregar participantes (Profiles)
        // Convert User IDs to Profile IDs
        $participantUserIds = $validated['participants'];
        $participantProfiles = \App\Models\User::whereIn('id', $participantUserIds)
            ->with('profile')
            ->get()
            ->pluck('profile.id', 'id'); // user_id => profile_id

        $syncData = [];
        foreach ($participantUserIds as $userId) {
            if (isset($participantProfiles[$userId])) {
                $syncData[$participantProfiles[$userId]] = ['attendance' => 'pendiente'];
            }
        }
        
        $meeting->participants()->attach($syncData);

        // Enviar notificaciones a participantes (Users)
        $usersToNotify = \App\Models\User::whereIn('id', $participantUserIds)->get();
        foreach ($usersToNotify as $participant) {
            if ($participant->id !== Auth::id()) {
                $participant->notify(new MeetingInvitation($meeting));
            }
        }

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Reunión programada exitosamente. Se han enviado las invitaciones.');
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
        // Solo el creador o admin puede editar
        if ($meeting->created_by !== Auth::user()->profile->id && !Auth::user()->hasRole('admin')) {
            return redirect()->route('meetings.index')
                ->with('error', 'No tienes permiso para editar esta reunión.');
        }

        $projects = Project::orderBy('title')->get();
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'coordinador', 'docente']);
        })->orderBy('name')->get();

        return view('app.back.meetings.edit', compact('meeting', 'projects', 'users'));
    }

    /**
     * Update the specified meeting in storage.
     */
    public function update(Request $request, Meeting $meeting)
    {
        // Solo el creador o admin puede editar
        if ($meeting->created_by !== Auth::user()->profile->id && !Auth::user()->hasRole('admin')) {
            return redirect()->route('meetings.index')
                ->with('error', 'No tienes permiso para editar esta reunión.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'project_id' => 'nullable|exists:projects,id',
            'meeting_date' => 'required|date',
            'location' => 'required|string|max:255',
            'status' => 'required|in:pendiente,completada,cancelada',
            'notes' => 'nullable|string',
            'participants' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $others = collect($value)->filter(fn($id) => $id != Auth::id());
                    if ($others->isEmpty()) {
                        $fail('Debe invitar al menos a un participante adicional.');
                    }
                },
            ],
            'participants.*' => 'exists:users,id',
        ], [], [
            'title' => 'título',
            'description' => 'descripción',
            'meeting_date' => 'fecha y hora',
            'location' => 'ubicación / enlace',
            'status' => 'estado',
            'notes' => 'notas',
            'participants' => 'participantes',
        ]);

        $meeting->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'project_id' => $validated['project_id'],
            'meeting_date' => $validated['meeting_date'],
            'location' => $validated['location'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // Actualizar participantes
        $participants = [];
        foreach ($validated['participants'] as $userId) {
            // Mantener el estado de asistencia si ya existía
            $existing = $meeting->participants->find($userId);
            /** @var \App\Models\Profile|null $existing */
            /** @phpstan-ignore-next-line */
            $attendance = $existing ? $existing->pivot->attendance : 'pendiente';
            $participants[$userId] = ['attendance' => $attendance];
        }
        $meeting->participants()->sync($participants);

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Reunión actualizada exitosamente.');
    }

    /**
     * Remove the specified meeting from storage.
     */
    public function destroy(Meeting $meeting)
    {
        // Solo el creador o admin puede eliminar
        if ($meeting->created_by !== Auth::user()->profile->id && !Auth::user()->hasRole('admin')) {
            return redirect()->route('meetings.index')
                ->with('error', 'No tienes permiso para eliminar esta reunión.');
        }

        $meeting->delete();

        return redirect()->route('meetings.index')
            ->with('success', 'Reunión eliminada exitosamente.');
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

        $updateData = ['attendance' => $validated['attendance']];
        
        if ($validated['attendance'] === 'rechazada') {
            $updateData['rejection_reason'] = $validated['rejection_reason'];
        } else {
            $updateData['rejection_reason'] = null;
        }

        $meeting->participants()->updateExistingPivot(Auth::user()->profile->id, $updateData);

        $message = $validated['attendance'] === 'confirmada' 
            ? 'Has confirmado tu asistencia a la reunión.'
            : 'Has declinado la invitación a la reunión.';

        if ($meeting->created_by !== Auth::user()->profile->id) {
            $reason = $validated['attendance'] === 'rechazada' ? $validated['rejection_reason'] : null;
            $meeting->creator->user->notify(new \App\Notifications\MeetingResponse($meeting, Auth::user(), $validated['attendance'], $reason));
        }

        return back()->with('success', $message);
    }

    /**
     * Mark meeting as completed and update attendance.
     */
    public function complete(Request $request, Meeting $meeting)
    {
        // Solo el creador o admin puede completar
        if ($meeting->created_by !== Auth::user()->profile->id && !Auth::user()->hasRole('admin')) {
            return back()->with('error', 'No tienes permiso para completar esta reunión.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
            'attended' => 'nullable|array',
            'attended.*' => 'exists:users,id',
        ]);

        $meeting->update([
            'status' => 'completada',
            'notes' => $validated['notes'],
        ]);

        // Marcar quiénes asistieron
        if (isset($validated['attended'])) {
            foreach ($meeting->participants as $participant) {
                $attendance = in_array($participant->id, $validated['attended']) ? 'asistio' : 'rechazada';
                $meeting->participants()->updateExistingPivot($participant->id, [
                    'attendance' => $attendance,
                ]);
            }
        }

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Reunión marcada como completada.');
    }

    /**
     * Send reminders to participants.
     */
    public function sendReminders(Meeting $meeting)
    {
        // Solo el creador o admin puede enviar recordatorios
        if ($meeting->created_by !== Auth::user()->profile->id && !Auth::user()->hasRole('admin')) {
            return back()->with('error', 'No tienes permiso para enviar recordatorios.');
        }

        foreach ($meeting->participants as $participant) {
            /** @phpstan-ignore-next-line */
            if ($participant->pivot->attendance !== 'rechazada') {
                $participant->user->notify(new MeetingReminder($meeting));
            }
        }

        return back()->with('success', 'Recordatorios enviados a los participantes.');
    }
    /**
     * Cancel the meeting and notify participants.
     */
    public function cancel(Request $request, Meeting $meeting)
    {
        // Solo el creador o admin puede cancelar
        if ($meeting->created_by !== Auth::user()->profile->id && !Auth::user()->hasRole('admin')) {
            return back()->with('error', 'No tienes permiso para cancelar esta reunión.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        $meeting->update([
            'status' => 'cancelada',
            'notes' => $meeting->notes . "\n\n[CANCELADA]: " . $validated['cancellation_reason'],
        ]);

        // Notificar participantes
        foreach ($meeting->participants as $participant) {
            if ($participant->id !== Auth::id()) {
                $participant->user->notify(new \App\Notifications\MeetingCancellation($meeting, $validated['cancellation_reason']));
            }
        }

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Reunión cancelada. Se ha notificado a todos los participantes.');
    }
}
