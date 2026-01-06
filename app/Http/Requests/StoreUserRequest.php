<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

/**
 * Validation request for creating a new system user.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class StoreUserRequest extends FormRequest
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
            'name'     => $this->getNameRules(),
            'email'    => $this->getEmailRules(),
            'password' => $this->getPasswordRules(),
            'role'     => $this->getRoleRules(),
        ];
    }

    /**
     * @return array Basic name validation rules.
     */
    private function getNameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * @return array Email validation rules ensuring uniqueness.
     */
    private function getEmailRules(): array
    {
        return ['required', 'string', 'email', 'max:255', 'unique:users'];
    }

    /**
     * @return array Password validation rules using system defaults.
     */
    private function getPasswordRules(): array
    {
        return ['required', 'confirmed', Rules\Password::defaults()];
    }

    /**
     * @return array Role assignment validation rules.
     */
    private function getRoleRules(): array
    {
        return ['required', 'exists:roles,name'];
    }
}
