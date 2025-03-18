<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDiscountRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name'       => 'required|string|max:255',
            'percent'    => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after:start_date',
        ];
    }

    public function messages()
    {
        return [
            'name.required'       => 'Tên khuyến mãi không được để trống.',
            'name.string'         => 'Tên khuyến mãi phải là chuỗi ký tự.',
            'name.max'            => 'Tên khuyến mãi không được vượt quá 255 ký tự.',
            'percent.required'    => 'Phần trăm giảm giá không được để trống.',
            'percent.numeric'     => 'Phần trăm giảm giá phải là số.',
            'percent.min'         => 'Phần trăm giảm giá tối thiểu là 1%.',
            'percent.max'         => 'Phần trăm giảm giá tối đa là 100%.',
            'start_date.required' => 'Ngày bắt đầu không được để trống.',
            'start_date.date'     => 'Ngày bắt đầu phải là định dạng ngày hợp lệ.',
            'start_date.after_or_equal' => 'Ngày bắt đầu phải từ hôm nay trở đi.',
            'end_date.required'   => 'Ngày kết thúc không được để trống.',
            'end_date.date'       => 'Ngày kết thúc phải là định dạng ngày hợp lệ.',
            'end_date.after'      => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ];
    }
}
