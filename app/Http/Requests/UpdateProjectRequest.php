<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation request for updating an existing institutional project.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class UpdateProjectRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'title'                 => 'required|string|max:255',
            'description'           => 'required|string',
            'objectives'            => 'nullable|string',
            'category_id'           => 'required|exists:categories,id',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date|after:start_date',
            'status'                => $this->getStatusRules(),
            'budget'                => 'nullable|numeric|min:0|max:9999999.99',
            'impact_description'    => 'nullable|string',
            'completion_percentage' => 'nullable|integer|min:0|max:100',
            'team_members'          => $this->getTeamMemberRules(),
            'team_members.*'        => 'exists:users,id',
        ];
    }

    /**
     * @return array Rules for project status lifecycle.
     */
    private function getStatusRules(): array
    {
        return ['required', 'in:planificacion,en_progreso,finalizado,en_riesgo'];
    }

    /**
     * @return array Rules for the team members collection.
     */
    private function getTeamMemberRules(): array
    {
        return ['required', 'array', 'min:1'];
    }
}
