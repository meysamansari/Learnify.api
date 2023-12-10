<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'family' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email|max:255',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|string',
            'university' => 'nullable|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'educational_stage' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
        ];
    }
}
