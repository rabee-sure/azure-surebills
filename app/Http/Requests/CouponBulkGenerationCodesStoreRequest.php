<?php

namespace App\Http\Requests;

use App\Enums\CouponMechanism;
use Illuminate\Foundation\Http\FormRequest;

class CouponBulkGenerationCodesStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'count' => 'required|integer|min:1|max:10000',
            'pattern' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'count.required' => __('The count field is required.'),
            'count.integer' => __('The count field must be an integer.'),
            'count.min' => __('The count field must be greater than 0.'),
            'count.max' => __('The count field must be less than 10000.'),
            'pattern.string' => __('The pattern field must be a string.'),
            'pattern.max' => __('The pattern field must be less than 255 characters.'),
        ];
    }
}
