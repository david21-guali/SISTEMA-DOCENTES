<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

/**
 * Validation request for updating an existing task.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_id'  => 'required|exists:projects,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'assignees'   => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
            'due_date'    => $this->getDueDateRules(),
            'status'      => 'required|in:pendiente,en_progreso,completada,atrasada',
            'priority'    => 'required|in:baja,media,alta',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * 
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'project_id'  => 'proyecto',
            'title'       => 'título',
            'description' => 'descripción',
            'assignees'   => 'asignados',
            'due_date'    => 'fecha límite',
            'status'      => 'estado',
            'priority'    => 'prioridad',
        ];
    }

    /**
     * Return the rules for the due_date field, including project bounds check.
     * 
     * @return array<int, mixed>
     */
    private function getDueDateRules(): array
    {
        return [
            'required',
            'date',
            function ($attribute, $value, $fail) {
                $this->validateDueDateWithinProjectScope($value, $fail);
            },
        ];
    }

    /**
     * Ensure the task's due date falls within the project's date range.
     * 
     * @param mixed $value
     * @param \Closure $fail
     * @return void
     */
    private function validateDueDateWithinProjectScope($value, $fail): void
    {
        /** @var \App\Models\Project|null $project */
        $project = Project::find($this->project_id);
        
        if (!$project) {
            return;
        }

        $date = Carbon::parse($value);
        $this->checkStartDateBound($project, $date, $fail);
        $this->checkEndDateBound($project, $date, $fail);
    }

    /**
     * Check if the date is before the project's start date.
     * 
     * @param Project $project
     * @param Carbon $date
     * @param \Closure $fail
     * @return void
     */
    private function checkStartDateBound(Project $project, Carbon $date, $fail): void
    {
        if ($project->start_date && $date->lt($project->start_date->startOfDay())) {
            $fail('La fecha de vencimiento no puede ser anterior al inicio del proyecto (' . $project->start_date->format('d/m/Y') . ').');
        }
    }

    /**
     * Check if the date is after the project's end date.
     * 
     * @param Project $project
     * @param Carbon $date
     * @param \Closure $fail
     * @return void
     */
    private function checkEndDateBound(Project $project, Carbon $date, $fail): void
    {
        if ($project->end_date && $date->gt($project->end_date->endOfDay())) {
            $fail('La fecha de vencimiento no puede ser posterior al fin del proyecto (' . $project->end_date->format('d/m/Y') . ').');
        }
    }
}
