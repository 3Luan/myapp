<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
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
            'cart_id' => 'required|exists:carts,id',
            'count' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'cart_id.required' => 'The cart ID is required.',
            'cart_id.exists' => 'The selected cart does not exist.',
            'count.required' => 'The quantity is required.',
            'count.integer' => 'The quantity must be an integer.',
            'count.min' => 'The quantity must be at least 1.',
        ];
    }
}
