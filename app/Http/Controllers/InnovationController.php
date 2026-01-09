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
    public function __construct(protected \App\Services\InnovationService $innovationService) {}

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
        return view('app.back.innovations.show', [
            'innovation' => $innovation->load(['profile.user', 'innovationType', 'attachments'])
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
     * Approve the specified innovation.
     * 
     * @param Request $request
     * @param Innovation $innovation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, Innovation $innovation): \Illuminate\Http\RedirectResponse
    {
        $innovation->update([
            'status'       => 'aprobada',
            'review_notes' => $request->review_notes
        ]);
        return redirect()->route('innovations.index')->with('success', 'Innovación aprobada.');
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
            'review_notes' => $request->review_notes
        ]);
        return redirect()->route('innovations.index')->with('success', 'Innovación rechazada.');
    }
}
