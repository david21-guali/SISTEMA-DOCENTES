<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation request for creating a new innovation evaluation.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class StoreEvaluationRequest extends FormRequest
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
            'innovation_score'  => 'required|integer|min:1|max:5',
            'relevance_score'   => 'required|integer|min:1|max:5',
            'results_score'     => 'required|integer|min:1|max:5',
            'impact_score'      => 'required|integer|min:1|max:5',
            'methodology_score' => 'required|integer|min:1|max:5',
            'final_score'       => 'required|numeric|min:1|max:10',
            'strengths'         => 'nullable|string',
            'weaknesses'        => 'nullable|string',
            'recommendations'   => 'nullable|string',
            'general_comments'  => 'nullable|string',
            'report_file'       => 'nullable|file|mimes:pdf|max:5120',
            'status'            => 'required|in:borrador,finalizada',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'Por favor, completa este campo obligatorio.',
            'min'      => 'El valor mínimo es :min.',
            'max'      => 'El valor máximo es :max.',
            'numeric'  => 'Este campo debe ser un número.',
            'integer'  => 'Este campo debe ser un número entero.',
            'mimes'    => 'El archivo debe ser un PDF.',
            // Removed duplicate max key, keeping relevant message
        ];
    }
}
