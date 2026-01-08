<?php

namespace App\Http\Controllers;

use App\Models\Innovation;
use App\Models\InnovationType;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InnovationController extends Controller
{
    protected \App\Services\InnovationService $innovationService;

    public function __construct(\App\Services\InnovationService $innovationService)
    {
        $this->innovationService = $innovationService;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $innovationsQuery = Innovation::with(['profile.user', 'innovationType']);

        if ($request->has('status') && !empty($request->status)) {
            $innovationsQuery->where('status', $request->status);
        }
        
        // Calcular estadísticas
        $stats = [
            'total' => Innovation::count(),
            'aprobada' => Innovation::where('status', 'aprobada')->count(),
            'en_revision' => Innovation::where('status', 'en_revision')->count(),
            'rechazada' => Innovation::where('status', 'rechazada')->count(),
            'propuesta' => Innovation::where('status', 'propuesta')->count(),
            'en_implementacion' => Innovation::where('status', 'en_implementacion')->count(),
        ];

        $innovations = $innovationsQuery->latest()->get(); // Get ALL for DataTables if not paginating server-side

        return view('app.back.innovations.index', compact('innovations', 'stats'));
    }

    public function bestPractices(): \Illuminate\View\View
    {
        $bestPractices = Innovation::with(['profile.user', 'innovationType'])
            ->approved() // Cambio: solo innovaciones aprobadas
            ->bestRated()
            ->paginate(12);

        return view('app.back.innovations.best-practices', compact('bestPractices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $innovationTypes = InnovationType::all();
        return view('app.back.innovations.create', compact('innovationTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreInnovationRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->innovationService->createInnovation(
            $request->validated(), 
            $request->file('evidence_files', [])
        );

        return redirect()->route('innovations.index')
            ->with('success', 'Innovación creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    /**
     * Display the specified resource.
     */
    public function show(Innovation $innovation): \Illuminate\View\View
    {
        $innovation->load(['profile.user', 'innovationType', 'attachments']);
        return view('app.back.innovations.show', compact('innovation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Innovation $innovation): \Illuminate\View\View
    {
        $innovationTypes = InnovationType::all();
        return view('app.back.innovations.edit', compact('innovation', 'innovationTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateInnovationRequest $request, Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        $this->innovationService->updateInnovation(
            $innovation, 
            $request->validated(), 
            $request->file('evidence_files', [])
        );

        return redirect()->route('innovations.index')
            ->with('success', 'Innovación actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        $this->innovationService->deleteInnovation($innovation);

        return redirect()->route('innovations.index')
            ->with('success', 'Innovación eliminada exitosamente.');
    }

    /**
     * Remove a specific evidence file.
     */
    /**
     * Remove a specific evidence file.
     */
    public function deleteAttachment(Innovation $innovation, int|string $attachmentId): \Illuminate\Http\RedirectResponse
    {
        $this->innovationService->deleteAttachment($innovation, (int)$attachmentId);

        return back()->with('success', 'Archivo de evidencia eliminado.');
    }

    /**
     * Approve an innovation (admin/coordinator only)
     */
    /**
     * Approve an innovation (admin/coordinator only)
     */
    public function approve(Request $request, Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'review_notes' => 'nullable|string|max:1000'
        ]);

        $innovation->update([
            'status' => 'aprobada',
            'reviewed_by' => Auth::id(),
            'review_notes' => $request->review_notes,
            'reviewed_at' => now()
        ]);

        // Notificar al autor
        if ($innovation->profile && $innovation->profile->user) {
            $innovation->profile->user->notify(new \App\Notifications\InnovationStatusChanged($innovation));
        }

        return back()->with('success', 'Innovación aprobada como Mejor Práctica.');
    }

    /**
     * Request review for an innovation (user/owner)
     */
    /**
     * Request review for an innovation (user/owner)
     */
    public function requestReview(Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        // Solo permitir si no está ya aprobada o en revisión
        if (in_array($innovation->status, ['aprobada', 'en_revision'])) {
            return back()->with('error', 'La innovación ya está en proceso de revisión o aprobada.');
        }

        $innovation->update([
            'status' => 'en_revision'
        ]);

        // Enviar notificación a administradores y coordinadores
        $staff = \App\Models\User::role(['admin', 'coordinador'])->get();
        \Illuminate\Support\Facades\Notification::send($staff, new \App\Notifications\InnovationReviewRequested($innovation));

        return back()->with('success', 'Solicitud de revisión enviada correctamente.');
    }

    /**
     * Reject an innovation (admin/coordinator only)
     */
    /**
     * Reject an innovation (admin/coordinator only)
     */
    public function reject(Request $request, Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'review_notes' => 'required|string|max:1000'
        ]);

        $innovation->update([
            'status' => 'rechazada',
            'reviewed_by' => Auth::id(),
            'review_notes' => $request->review_notes,
            'reviewed_at' => now()
        ]);

        // Notificar al autor
        if ($innovation->profile && $innovation->profile->user) {
            $innovation->profile->user->notify(new \App\Notifications\InnovationStatusChanged($innovation));
        }

        return back()->with('error', 'Innovación rechazada.');
    }
}
