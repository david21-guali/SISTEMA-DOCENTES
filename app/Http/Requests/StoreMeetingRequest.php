<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Validation request for creating a new meeting.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class StoreMeetingRequest extends FormRequest
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
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'project_id'     => 'nullable|exists:projects,id',
            'meeting_date'   => 'required|date',
            'location'       => 'required|string|max:255',
            'participants'   => $this->getParticipantRules(),
            'participants.*' => 'exists:users,id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * 
     * @return array
     */
    public function attributes(): array
    {
        return [
            'title'        => 'título',
            'description'  => 'descripción',
            'meeting_date' => 'fecha y hora',
            'location'     => 'ubicación / enlace',
            'participants' => 'participantes',
        ];
    }

    /**
     * Define validation logic for the participants array.
     * 
     * @return array
     */
    private function getParticipantRules(): array
    {
        return [
            'required',
            'array',
            function ($attribute, $value, $fail) {
                $this->ensureAdditionalParticipantsInvited($value, $fail);
            },
        ];
    }

    /**
     * Ensure the participant list contains more than just the current user.
     * 
     * @param array $value
     * @param \Closure $fail
     * @return void
     */
    private function ensureAdditionalParticipantsInvited(array $value, $fail): void
    {
        $others = collect($value)->filter(fn($id) => $id != Auth::id());
        
        if ($others->isEmpty()) {
            $fail('Debe invitar al menos a un participante adicional.');
        }
    }
}
