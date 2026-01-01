<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class EvaluationController extends Controller
{
    /**
     * Show the form for creating a new evaluation.
     */
    public function create(Project $project)
    {
        // Solo Coordinadores (y Admins) pueden evaluar
        if (!Auth::user()->hasRole('coordinador') && !Auth::user()->hasRole('admin')) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Solo los coordinadores pueden realizar evaluaciones.');
        }

        // Verificar si ya existe una evaluación
        $existing = Evaluation::where('project_id', $project->id)
            ->where('evaluator_id', Auth::user()->profile->id)
            ->first();

        if ($existing) {
            return redirect()->route('evaluations.edit', $existing)
                ->with('info', 'Ya existe una evaluación para este proyecto. Puedes editarla.');
        }

        return view('evaluations.create', compact('project'));
    }

    /**
     * Store a newly created evaluation in storage.
     */
    public function store(Request $request, Project $project)
    {
        $messages = [
            'required' => 'Por favor, completa este campo obligatorio.',
            'min' => 'El valor mínimo es :min.',
            'max' => 'El valor máximo es :max.',
            'numeric' => 'Este campo debe ser un número.',
            'integer' => 'Este campo debe ser un número entero.',
        ];

        $validated = $request->validate([
            'innovation_score' => 'required|integer|min:1|max:5',
            'relevance_score' => 'required|integer|min:1|max:5',
            'results_score' => 'required|integer|min:1|max:5',
            'impact_score' => 'required|integer|min:1|max:5',
            'methodology_score' => 'required|integer|min:1|max:5',
            'final_score' => 'required|numeric|min:1|max:10',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'general_comments' => 'nullable|string',
            'report_file' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:borrador,finalizada',
        ], $messages);

        $validated['project_id'] = $project->id;
        $validated['evaluator_id'] = Auth::user()->profile->id;

        // Manejar archivo del informe
        if ($request->hasFile('report_file')) {
            $path = $request->file('report_file')->store('evaluations/reports', 'public');
            $validated['report_file'] = $path;
        }

        Evaluation::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Evaluación guardada exitosamente.');
    }

    /**
     * Show the form for editing the specified evaluation.
     */
    public function edit(Evaluation $evaluation)
    {
        return view('evaluations.edit', compact('evaluation'));
    }

    /**
     * Update the specified evaluation in storage.
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        $messages = [
            'required' => 'Por favor, completa este campo obligatorio.',
            'min' => 'El valor mínimo es :min.',
            'max' => 'El valor máximo es :max.',
            'numeric' => 'Este campo debe ser un número.',
            'integer' => 'Este campo debe ser un número entero.',
        ];

        $validated = $request->validate([
            'innovation_score' => 'required|integer|min:1|max:5',
            'relevance_score' => 'required|integer|min:1|max:5',
            'results_score' => 'required|integer|min:1|max:5',
            'impact_score' => 'required|integer|min:1|max:5',
            'methodology_score' => 'required|integer|min:1|max:5',
            'final_score' => 'required|numeric|min:1|max:10',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'general_comments' => 'nullable|string',
            'report_file' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:borrador,finalizada',
        ], $messages);

        // Manejar archivo del informe
        if ($request->hasFile('report_file')) {
            // Eliminar archivo anterior si existe
            if ($evaluation->report_file) {
                Storage::disk('public')->delete($evaluation->report_file);
            }
            $path = $request->file('report_file')->store('evaluations/reports', 'public');
            $validated['report_file'] = $path;
        }

        $evaluation->update($validated);

        return redirect()->route('projects.show', $evaluation->project)
            ->with('success', 'Evaluación actualizada exitosamente.');
    }

    /**
     * Remove the specified evaluation from storage.
     */
    public function destroy(Evaluation $evaluation)
    {
        $project = $evaluation->project;

        // Eliminar archivo del informe si existe
        if ($evaluation->report_file) {
            Storage::disk('public')->delete($evaluation->report_file);
        }

        $evaluation->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Evaluación eliminada exitosamente.');
    }

    public function fixStorage()
    {
        try {
            $publicPath = public_path();
            $storagePath = storage_path('app/public');
            $linkPath = $publicPath . DIRECTORY_SEPARATOR . 'storage';

            // Eliminar si ya existe algo (directorio o link roto)
            if (file_exists($linkPath) || is_link($linkPath)) {
                if (PHP_OS_FAMILY === 'Windows') {
                    exec("rd /s /q \"$linkPath\"") ?: unlink($linkPath);
                } else {
                    is_link($linkPath) ? unlink($linkPath) : exec("rm -rf \"$linkPath\"");
                }
            }

            // Intentar crear el link simbólico manualmente
            if (function_exists('symlink')) {
                symlink($storagePath, $linkPath);
            } else {
                // Fallback: usar comando de Artisan
                Artisan::call('storage:link');
            }

            return redirect()->back()
                ->with('success', 'Enlace de almacenamiento reparado en: ' . $linkPath);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error reparando el enlace: ' . $e->getMessage());
        }
    }

}
