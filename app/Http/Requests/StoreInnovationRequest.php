<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation request for creating a new innovation proposal.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class StoreInnovationRequest extends FormRequest
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
        ];
    }

    /**
     * @return array<int, string> Rules for individual evidence documents.
     */
    private function getEvidenceFileRules(): array
    {
        return ['nullable', 'file', 'max:10240'];
    }
}
