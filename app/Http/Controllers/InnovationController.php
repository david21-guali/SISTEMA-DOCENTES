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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $innovations = Innovation::with(['profile.user', 'innovationType'])
            ->latest()
            ->paginate(12);

        return view('app.back.innovations.index', compact('innovations'));
    }

    public function bestPractices()
    {
        $bestPractices = Innovation::with(['profile.user', 'innovationType'])
            ->completed()
            ->bestRated()
            ->limit(9)
            ->get();

        return view('app.back.innovations.best-practices', compact('bestPractices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $innovationTypes = InnovationType::all();
        return view('app.back.innovations.create', compact('innovationTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'innovation_type_id' => 'required|exists:innovation_types,id',
            'methodology' => 'required|string',
            'expected_results' => 'required|string',
            'actual_results' => 'required|string',
            'impact_score' => 'required|integer|min:1|max:10',
            'evidence_files.*' => 'nullable|file|max:10240', // 10MB max
        ], [], [
            'title' => 'título',
            'description' => 'descripción',
            'innovation_type_id' => 'tipo de innovación',
            'methodology' => 'metodología',
            'expected_results' => 'resultados esperados',
            'actual_results' => 'resultados obtenidos',
            'impact_score' => 'puntuación de impacto',
        ]);

        $validated['profile_id'] = Auth::user()->profile->id;
        $validated['status'] = 'propuesta';

        // Remover evidence_files del array validado ya que no es una columna en la tabla
        unset($validated['evidence_files']);

        $innovation = Innovation::create($validated);

        // Manejar archivos de evidencia como Attachments
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $path = $file->store('innovations/evidence', 'public');
                
                $innovation->attachments()->create([
                    'filename' => $file->hashName(),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                    'uploaded_by' => Auth::user()->profile->id,
                ]);
            }
        }

        return redirect()->route('innovations.index')
            ->with('success', 'Innovación creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Innovation $innovation)
    {
        $innovation->load(['profile.user', 'innovationType']);
        return view('app.back.innovations.show', compact('innovation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Innovation $innovation)
    {
        $innovationTypes = InnovationType::all();
        return view('app.back.innovations.edit', compact('innovation', 'innovationTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Innovation $innovation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'innovation_type_id' => 'required|exists:innovation_types,id',
            'methodology' => 'required|string',
            'expected_results' => 'required|string',
            'actual_results' => 'required|string',
            'status' => 'required|in:propuesta,en_implementacion,completada',
            'impact_score' => 'required|integer|min:1|max:10',
            'evidence_files.*' => 'nullable|file|max:10240',
        ], [], [
            'title' => 'título',
            'description' => 'descripción',
            'innovation_type_id' => 'tipo de innovación',
            'methodology' => 'metodología',
            'expected_results' => 'resultados esperados',
            'actual_results' => 'resultados obtenidos',
            'impact_score' => 'puntuación de impacto',
            'status' => 'estado',
        ]);

        // Remover evidence_files del array validado
        unset($validated['evidence_files']);

        $innovation->update($validated);

        // Manejar nuevos archivos de evidencia
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $path = $file->store('innovations/evidence', 'public');
                
                $innovation->attachments()->create([
                    'filename' => $file->hashName(),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                    'uploaded_by' => Auth::user()->profile->id,
                ]);
            }
        }

        return redirect()->route('innovations.index')
            ->with('success', 'Innovación actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Innovation $innovation)
    {
        // Eliminar attachments asociados
        foreach ($innovation->attachments as $attachment) {
            $attachment->delete();
        }

        $innovation->delete();

        return redirect()->route('innovations.index')
            ->with('success', 'Innovación eliminada exitosamente.');
    }

    /**
     * Remove a specific evidence file.
     */
    public function deleteEvidence(Innovation $innovation, $attachmentId)
    {
        $attachment = $innovation->attachments()->findOrFail($attachmentId);
        $attachment->delete();

        return back()->with('success', 'Archivo de evidencia eliminado.');
    }
}
