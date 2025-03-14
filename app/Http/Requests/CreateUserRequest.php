<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name cannot be empty.',
            'email.required' => 'Email cannot be empty.',
            'email.email' => 'Invalid email format.',
            'email.unique' => 'This email is already in use.',
            'phone.required' => 'Phone cannot be empty.',
            'phone.unique' => 'This phone is already in use.',
            'password.required' => 'Password cannot be empty.',
            'password.min' => 'Password must be at least 6 characters.',
        ];
    }
}
