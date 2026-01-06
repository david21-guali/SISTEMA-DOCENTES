<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation request for creating a new institutional project.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class StoreProjectRequest extends FormRequest
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
            'title'              => 'required|string|max:255',
            'description'        => 'required|string',
            'objectives'         => 'required|string',
            'category_id'        => 'required|exists:categories,id',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after:start_date',
            'budget'             => 'nullable|numeric|min:0|max:9999999.99',
            'impact_description' => 'required|string',
            'team_members'       => $this->getTeamMemberRules(),
            'team_members.*'     => 'exists:users,id',
            'attachments.*'      => $this->getAttachmentRules(),
        ];
    }

    /**
     * Get custom error messages for validation failures.
     * 
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required'              => 'El título del proyecto es obligatorio.',
            'title.max'                   => 'El título no puede exceder 255 caracteres.',
            'description.required'        => 'La descripción del proyecto es obligatoria.',
            'objectives.required'         => 'Los objetivos del proyecto son obligatorios.',
            'category_id.required'        => 'Debes seleccionar una categoría.',
            'category_id.exists'          => 'La categoría seleccionada no es válida.',
            'start_date.required'         => 'La fecha de inicio es obligatoria.',
            'start_date.date'             => 'La fecha de inicio debe ser una fecha válida.',
            'end_date.required'           => 'La fecha de fin es obligatoria.',
            'end_date.date'               => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after'              => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'budget.numeric'              => 'El presupuesto debe ser un número.',
            'budget.min'                  => 'El presupuesto no puede ser negativo.',
            'budget.max'                  => 'El presupuesto no puede exceder $9,999,999.99.',
            'impact_description.required' => 'La descripción del impacto esperado es obligatoria.',
            'team_members.required'       => 'Debes seleccionar al menos un miembro del equipo.',
            'team_members.min'            => 'Debes seleccionar al menos un miembro del equipo.',
            'team_members.*.exists'       => 'Uno o más miembros seleccionados no son válidos.',
        ];
    }

    /**
     * @return array Rules for the team members collection.
     */
    private function getTeamMemberRules(): array
    {
        return ['required', 'array', 'min:1'];
    }

    /**
     * @return array Rules for individual file attachments.
     */
    private function getAttachmentRules(): array
    {
        return ['nullable', 'file', 'max:10240'];
    }
}
