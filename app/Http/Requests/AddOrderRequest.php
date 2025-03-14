<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.count' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'products.required' => 'The product list cannot be empty.',
            'products.array' => 'The product data must be an array.',
            'products.*.product_id.required' => 'The product ID is required.',
            'products.*.product_id.exists' => 'The selected product does not exist.',
            'products.*.count.required' => 'The product quantity is required.',
            'products.*.count.integer' => 'The product quantity must be an integer.',
            'products.*.count.min' => 'The product quantity must be at least 1.',
        ];
    }
}
