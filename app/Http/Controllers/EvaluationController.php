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
    protected \App\Services\EvaluationService $evaluationService;

    public function __construct(\App\Services\EvaluationService $evaluationService)
    {
        $this->evaluationService = $evaluationService;
    }

    public function create(Project $project): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        if (!Auth::user()->hasRole(['coordinador', 'admin'])) {
            return redirect()->route('projects.show', $project)->with('error', 'Solo coordinadores.');
        }

        $existing = Evaluation::where('project_id', $project->id)->where('evaluator_id', Auth::user()->profile->id)->first();
        if ($existing) return redirect()->route('evaluations.edit', $existing)->with('info', 'Editando evaluación previa.');

        return view('evaluations.create', compact('project'));
    }

    /**
     * Store a newly created evaluation in storage.
     */
    public function store(\App\Http\Requests\StoreEvaluationRequest $request, Project $project): \Illuminate\Http\RedirectResponse
    {
        $this->evaluationService->createEvaluation($project, $request->validated(), $request->file('report_file'));

        return redirect()->route('projects.show', $project)->with('success', 'Evaluación guardada.');
    }

    /**
     * Show the form for editing the specified evaluation.
     */
    public function edit(Evaluation $evaluation): \Illuminate\View\View
    {
        return view('evaluations.edit', compact('evaluation'));
    }

    /**
     * Update the specified evaluation in storage.
     */
    public function update(\App\Http\Requests\StoreEvaluationRequest $request, Evaluation $evaluation): \Illuminate\Http\RedirectResponse
    {
        $this->evaluationService->updateEvaluation($evaluation, $request->validated(), $request->file('report_file'));

        return redirect()->route('projects.show', $evaluation->project)->with('success', 'Actualizada.');
    }

    /**
     * Remove the specified evaluation from storage.
     */
    public function destroy(Evaluation $evaluation): \Illuminate\Http\RedirectResponse
    {
        $project = $evaluation->project;
        $this->evaluationService->deleteEvaluation($evaluation);

        return redirect()->route('projects.show', $project)->with('success', 'Eliminada.');
    }

}
