<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email cannot be empty.',
            'email.email' => 'Invalid email format.',
            'password.required' => 'Password cannot be empty.',
            'password.min' => 'Password must be at least 6 characters.',
        ];
    }
}
