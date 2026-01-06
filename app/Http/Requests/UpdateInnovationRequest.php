<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation request for updating an existing innovation initiative.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class UpdateInnovationRequest extends FormRequest
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
            'title'              => 'required|string|max:255',
            'description'        => 'required|string',
            'innovation_type_id' => 'required|exists:innovation_types,id',
            'methodology'        => 'required|string',
            'expected_results'   => 'required|string',
            'actual_results'     => 'required|string',
            'status'             => $this->getStatusRules(),
            'impact_score'       => 'required|integer|min:1|max:10',
            'evidence_files.*'   => $this->getEvidenceFileRules(),
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
            'title'              => 'título',
            'description'        => 'descripción',
            'innovation_type_id' => 'tipo de innovación',
            'methodology'        => 'metodología',
            'expected_results'   => 'resultados esperados',
            'actual_results'     => 'resultados obtenidos',
            'impact_score'       => 'puntuación de impacto',
            'status'             => 'estado',
        ];
    }

    /**
     * @return array<int, string> Rules for innovation lifecycle status.
     */
    private function getStatusRules(): array
    {
        return ['required', 'in:propuesta,en_implementacion,completada,en_revision,aprobada,rechazada'];
    }

    /**
     * @return array<int, string> Rules for individual evidence documents.
     */
    private function getEvidenceFileRules(): array
    {
        return ['nullable', 'file', 'max:10240'];
    }
}
