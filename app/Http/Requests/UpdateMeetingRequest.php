<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Validation request for updating an existing meeting.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class UpdateMeetingRequest extends FormRequest
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
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'project_id'     => 'nullable|exists:projects,id',
            'meeting_date'   => 'required|date',
            'location'       => 'required|string|max:255',
            'type'           => 'required|in:virtual,presencial',
            'status'         => 'nullable|in:pendiente,completada,cancelada',
            'notes'          => 'nullable|string',
            'participants'   => 'nullable|array',
            'participants.*' => 'exists:users,id',
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
            'title'        => 'título',
            'description'  => 'descripción',
            'meeting_date' => 'fecha y hora',
            'location'     => 'ubicación / enlace',
            'status'       => 'estado',
            'notes'        => 'notes',
            'participants' => 'participantes',
        ];
    }

}
