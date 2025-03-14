<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCartRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'count' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'products.*.product_id.required' => 'The product ID is required.',
            'products.*.product_id.exists' => 'The selected product does not exist.',
            'products.*.count.required' => 'The product quantity is required.',
            'products.*.count.integer' => 'The product quantity must be an integer.',
            'products.*.count.min' => 'The product quantity must be at least 1.',
        ];
    }
}
