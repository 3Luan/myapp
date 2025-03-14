<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderToCartRequest extends FormRequest
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
            'products' => 'required|array',
            'products.*.cart_id' => 'required|exists:carts,id',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.count' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'The product list is required.',
            'products.array' => 'The product data must be an array.',

            'products.*.cart_id.required' => 'The cart ID is required.',
            'products.*.cart_id.exists' => 'The selected cart does not exist.',

            'products.*.product_id.required' => 'The product ID is required.',
            'products.*.product_id.exists' => 'The selected product does not exist.',

            'products.*.count.required' => 'The product quantity is required.',
            'products.*.count.integer' => 'The product quantity must be an integer.',
            'products.*.count.min' => 'The product quantity must be at least 1.',
        ];
    }
}
