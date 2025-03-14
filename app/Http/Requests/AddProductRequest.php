<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProductRequest extends FormRequest
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
            'price' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name cannot be empty.',
            'name.string' => 'Product name must be a string.',
            'name.max' => 'Product name cannot exceed 255 characters.',

            'price.required' => 'Product price cannot be empty.',
            'price.numeric' => 'Product price must be a number.',

            'images.*.image' => 'Each uploaded file must be an image.',
            'images.*.mimes' => 'Images must be in jpeg, png, jpg, or gif format.',
            'images.*.max' => 'Each image must not exceed 2MB.',

            'description.string' => 'Product description must be a string.',
        ];
    }
}
