<?php

namespace App\Http\Controllers;

use App\Models\Innovation;
use Illuminate\Http\Request;
use App\Services\InnovationService;

class InnovationActionController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @param InnovationService $service
     */
    public function __construct(protected InnovationService $service) {}

    /**
     * Approve an innovation proposal.
     * 
     * @param Request $req
     * @param Innovation $i
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $req, Innovation $i): \Illuminate\Http\RedirectResponse 
    {
        $validated = $req->validate(['review_notes' => 'nullable|string|max:1000']);
        $this->service->approve($i, $validated);
        return back()->with('success', 'Aprobada.');
    }

    /**
     * Request a formal review for an innovation.
     * 
     * @param Innovation $i
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestReview(Innovation $i): \Illuminate\Http\RedirectResponse 
    {
        $this->service->requestReview($i);
        return back()->with('success', 'Revisión solicitada.');
    }

    /**
     * Reject an innovation proposal.
     * 
     * @param Request $req
     * @param Innovation $i
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $req, Innovation $i): \Illuminate\Http\RedirectResponse 
    {
        $validated = $req->validate(['review_notes' => 'required|string|max:1000']);
        $this->service->reject($i, $validated);
        return back()->with('error', 'Rechazada.');
    }
}
