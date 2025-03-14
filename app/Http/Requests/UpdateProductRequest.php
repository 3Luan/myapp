<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'idDeleteId' => 'nullable|array',
            'idDeleteId.*' => 'exists:images,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.string' => 'Product name must be a string.',
            'name.max' => 'Product name must not exceed 255 characters.',

            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',

            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Only jpeg, png, jpg, and gif formats are allowed.',
            'images.*.max' => 'Each image must not exceed 2MB.',

            'description.string' => 'Description must be a valid string.',

            'idDeleteId.array' => 'Image delete IDs must be an array.',
            'idDeleteId.*.exists' => 'Some images to delete do not exist.',
        ];
    }
}
