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
        $meeting = $this->route('meeting');
        return $meeting instanceof \App\Models\Meeting && $this->user()->can('update', $meeting);
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
            'meeting_date' => 'required|date',
            'location'     => 'required|string|max:500',
            'type'         => 'required|in:virtual,presencial',
            'project_id'   => 'nullable|exists:projects,id',
            'participants' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    // All participants must be besides the creator (creator is always added anyway)
                    // We check if there's someone else in the chosen array
                    $creatorId = $this->route('meeting')->created_by ?? auth()->id();
                    $creatorUserId = \App\Models\Profile::find($creatorId)->user_id ?? auth()->id();

                    $otherParticipants = array_diff($value, [$creatorUserId]);
                    if (empty($otherParticipants)) {
                        $fail('Debe seleccionar al menos un participante adicional además del creador de la reunión.');
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
            'notes'        => 'nullable|string',
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
