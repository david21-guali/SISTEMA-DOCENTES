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
    public function __construct(
        protected \App\Services\InnovationService $innovationService,
        protected \App\Services\InnovationReviewService $reviewService
    ) {}

    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of pedagogical innovations.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $innovations = Innovation::with(['profile.user', 'innovationType'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()->get();

        return view('app.back.innovations.index', [
            'innovations' => $innovations, 
            'stats'       => $this->innovationService->getStats()
        ]);
    }

    /**
     * Display the best practices repository.
     * 
     * @return \Illuminate\View\View
     */
    public function bestPractices(): \Illuminate\View\View
    {
        return view('app.back.innovations.best-practices', [
            'bestPractices' => Innovation::with(['profile.user', 'innovationType'])
                ->approved()
                ->bestRated()
                ->paginate(12)
        ]);
    }

    /**
     * Show the form for creating a new innovation.
     * 
     * @return \Illuminate\View\View
     */
    public function create(): \Illuminate\View\View
    {
        return view('app.back.innovations.create', [
            'innovationTypes' => \App\Models\InnovationType::all()
        ]);
    }

    /**
     * Store a newly created innovation.
     * 
     * @param \App\Http\Requests\StoreInnovationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(\App\Http\Requests\StoreInnovationRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->innovationService->createInnovation(
            $request->validated(), 
            $request->file('evidence_files', [])
        );
        return redirect()->route('innovations.index')->with('success', 'Innovación creada.');
    }

    /**
     * Display the specified innovation details.
     * 
     * @param Innovation $innovation
     * @return \Illuminate\View\View
     */
    public function show(Innovation $innovation): \Illuminate\View\View
    {
        $innovation->load(['profile.user', 'innovationType', 'attachments']);
        $user = auth()->user();
        
        $hasVoted = $user && $innovation->hasVotedBy($user);
        $canVote = $this->reviewService->canUserReview($innovation, $user) && !$hasVoted;

        return view('app.back.innovations.show', [
            'innovation' => $innovation,
            'hasVoted'   => $hasVoted,
            'canVote'    => $canVote,
            'reviews'    => $user?->hasRole('admin') ? $innovation->reviews()->latest()->get() : collect(),
        ]);
    }

    /**
     * Show the form for editing an innovation.
     * 
     * @param Innovation $innovation
     * @return \Illuminate\View\View
     */
    public function edit(Innovation $innovation): \Illuminate\View\View
    {
        return view('app.back.innovations.edit', [
            'innovation'      => $innovation, 
            'innovationTypes' => \App\Models\InnovationType::all()
        ]);
    }

    /**
     * Update the specified innovation.
     * 
     * @param \App\Http\Requests\UpdateInnovationRequest $request
     * @param Innovation $innovation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(\App\Http\Requests\UpdateInnovationRequest $request, Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        $this->innovationService->updateInnovation(
            $innovation, 
            $request->validated(), 
            $request->file('evidence_files', [])
        );
        return redirect()->route('innovations.index')->with('success', 'Innovación actualizada.');
    }

    /**
     * Remove the specified innovation.
     * 
     * @param Innovation $innovation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        $this->innovationService->deleteInnovation($innovation);
        return redirect()->route('innovations.index')->with('success', 'Innovación eliminada.');
    }

    /**
     * Delete a single attachment from the innovation.
     * 
     * @param Innovation $innovation
     * @param mixed $attachmentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAttachment(Innovation $innovation, $attachmentId): \Illuminate\Http\RedirectResponse
    {
        $this->innovationService->deleteAttachment($innovation, (int)$attachmentId);
        return back()->with('success', 'Archivo eliminado.');
    }

    /**
     * Request review for the specified innovation.
     * 
     * @param Innovation $innovation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestReview(Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        $innovation->update([
            'status' => 'en_revision',
            'review_deadline' => now()->addDays(3)
        ]);
        return redirect()->route('innovations.show', $innovation)->with('success', 'Solicitud de revisión enviada. El periodo de votación comunitaria será de 3 días.');
    }

    /**
     * Approve the specified innovation.
     * 
     * @param Request $request
     * @param Innovation $innovation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'impact_score' => 'required|integer|min:1|max:10',
            'review_notes' => 'nullable|string|max:1000'
        ]);

        $this->innovationService->approve($innovation, $validated);

        $message = $validated['impact_score'] >= 4 
            ? 'Innovación aprobada y agregada a Buenas Prácticas.'
            : 'Innovación aprobada.';

        return redirect()->route('innovations.index')->with('success', $message);
    }

    /**
     * Reject the specified innovation.
     * 
     * @param Request $request
     * @param Innovation $innovation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        $innovation->update([
            'status'       => 'rechazada',
            'review_notes' => $request->review_notes,
            'reviewed_by'  => auth()->id(),
            'reviewed_at'  => now()
        ]);
        return redirect()->route('innovations.index')->with('success', 'Innovación rechazada.');
    }

    /**
     * Show the form for submitting an anonymous review.
     * 
     * @param Innovation $innovation
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function review(Innovation $innovation): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) abort(403, 'Los administradores no pueden votar.');

        try {
            $this->reviewService->validateReview($innovation, $user);
        } catch (\Exception $e) {
            return redirect()->route('innovations.show', $innovation)->with('error', $e->getMessage());
        }

        if ($innovation->hasVotedBy($user)) {
            return redirect()->route('innovations.show', $innovation)->with('info', 'Ya has votado esta innovación.');
        }

        return view('app.back.innovations.review', compact('innovation'));
    }

    /**
     * Store an anonymous review.
     * 
     * @param Request $request
     * @param Innovation $innovation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeReview(Request $request, Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'vote' => 'required|in:approved,rejected',
            'comment' => 'required|string|min:20|max:70'
        ], [
            'comment.min' => 'El comentario debe tener al menos 20 caracteres.',
            'comment.max' => 'El comentario no debe exceder los 70 caracteres.'
        ]);

        try {
            $this->reviewService->submitReview($innovation, auth()->user(), $validated);
            return redirect()->route('innovations.show', $innovation)
                ->with('success', 'Tu voto ha sido registrado de forma anónima.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
