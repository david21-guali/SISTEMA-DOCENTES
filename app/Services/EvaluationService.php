<?php

namespace App\Services;

use App\Models\Evaluation;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Service to manage project evaluations and report files.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class EvaluationService
{
    /**
     * Create a new evaluation record and store the associated report file.
     * 
     * @param Project $project The project being evaluated.
     * @param array<string, mixed> $data Evaluation details (score, comments, etc.).
     * @param \Illuminate\Http\UploadedFile|null $file Optional uploaded report document.
     * @return Evaluation
     */
    public function createEvaluation(Project $project, array $data, $file = null): Evaluation
    {
        $payload = array_merge($data, [
            'project_id'   => $project->id,
            'evaluator_id' => Auth::user()->profile->id
        ]);

        if ($file) {
            $payload['report_file'] = $this->storeReportFile($file);
        }

        return Evaluation::create($payload);
    }

    /**
     * Update an existing evaluation and handle report file replacement.
     * 
     * @param Evaluation $evaluation
     * @param array<string, mixed> $data
     * @param \Illuminate\Http\UploadedFile|null $file
     * @return void
     */
    public function updateEvaluation(Evaluation $evaluation, array $data, $file = null): void
    {
        if ($file) {
            $this->removeExistingReport($evaluation);
            $data['report_file'] = $this->storeReportFile($file);
        }

        $evaluation->update($data);
    }

    /**
     * Permanently delete an evaluation and its associated report.
     * 
     * @param Evaluation $evaluation
     * @return void
     */
    public function deleteEvaluation(Evaluation $evaluation): void
    {
        $this->removeExistingReport($evaluation);
        $evaluation->delete();
    }

    /**
     * Store a report file in the designated storage path.
     * 
     * @param mixed $file
     * @return string The relative path to the stored file.
     */
    private function storeReportFile($file): string
    {
        return $file->store('evaluations/reports', 'public');
    }

    /**
     * Delete an evaluation's report file from storage if it exists.
     * 
     * @param Evaluation $evaluation
     * @return void
     */
    private function removeExistingReport(Evaluation $evaluation): void
    {
        if ($evaluation->report_file) {
            Storage::disk('public')->delete($evaluation->report_file);
        }
    }
}
