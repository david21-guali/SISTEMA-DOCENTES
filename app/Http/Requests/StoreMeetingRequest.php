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
        return $this->user()->can('create', \App\Models\Meeting::class);
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'meeting_date' => 'required|date|after:now',
            'location'     => 'required|string|max:500',
            'type'         => 'required|in:virtual,presencial',
            'project_id'   => 'nullable|exists:projects,id',
            'participants' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    // All participants must be besides the creator (or at least one of them must be)
                    $otherParticipants = array_diff($value, [auth()->id()]);
                    if (empty($otherParticipants)) {
                        $fail('Debe seleccionar al menos un participante adicional además de usted mismo.');
                    }

                    // If project is selected, validate participants belong to it
                    if ($this->project_id) {
                        /** @var \App\Models\Project|null $project */
                        $project = \App\Models\Project::with('team')->find($this->project_id);
                        
                        if ($project) {
                            $projectMemberIds = $project->team->pluck('user_id')->toArray();
                            $invalidParticipants = array_diff($value, $projectMemberIds);
                            
                            if (!empty($invalidParticipants)) {
                                $fail('Todos los participantes deben ser miembros del proyecto seleccionado.');
                            }
                        }
                    }
                },
            ],
            'participants.*' => 'exists:users,id',
            'status'       => 'required|in:pendiente,completada,cancelada',
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
            'participants' => 'participantes',
        ];
    }

}
