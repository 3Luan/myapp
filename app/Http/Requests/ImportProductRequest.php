<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'file'        => 'required|file|mimes:csv,txt|max:2048',
            // 'Name'        => 'required|string|max:255',
            // 'Price'       => 'required|numeric|min:0',
            // 'Rate'        => 'nullable|numeric|min:0|max:5',
            // 'Count'       => 'nullable|integer|min:0',
            // 'Description' => 'nullable|string',
            // 'Image'       => 'nullable|string',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'The file is required.',
            'file.mimes'    => 'Only CSV files are allowed.',
            'file.max'      => 'The file size should not exceed 2MB.',
            // 'Name.required' => 'The product name is required.',
            // 'Price.required' => 'The product price is required.',
            // 'Price.numeric'  => 'The price must be a valid number.',
        ];
    }
}
