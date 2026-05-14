<?php

namespace App\Http\Requests;

use App\Enums\CouponMechanism;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponStoreRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('coupons', 'name')->whereNull('deleted_at'),
            ],
            'mechanism' => 'required|in:' . implode(',', CouponMechanism::values()),
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0' . ($this->discount_type === 'percentage' ? '|max:100' : ''),
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'max_usage' => 'nullable|integer|min:1',
            'max_customer_usage' => 'nullable|integer|min:1',
            'code_pattern' => 'nullable|string|max:255|unique:coupons,code_pattern',
            'is_active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('The name field is required.'),
            'name.string' => __('The name field must be a string.'),
            'name.max' => __('The name field must be less than 255 characters.'),
            'name.unique' => __('The name has already been taken.'),
            'mechanism.required' => __('The mechanism field is required.'),
            'mechanism.in' => __('The mechanism field must be a valid mechanism.'),
            'discount_type.required' => __('The discount type field is required.'),
            'discount_type.in' => __('The discount type field must be a valid discount type.'),
            'discount_value.required' => __('The discount value field is required.'),
            'discount_value.numeric' => __('The discount value field must be a number.'),
            'discount_value.min' => __('The discount value field must be greater than 0.'),
            'discount_value.max' => __('The discount value field must be less than 100.'),
            'valid_from.date' => __('The valid from field must be a valid date.'),
            'valid_to.date' => __('The valid to field must be a valid date.'),
            'valid_to.after_or_equal' => __('The valid to field must be after or equal to the valid from field.'),
            'max_usage.integer' => __('The max usage field must be an integer.'),
            'max_usage.min' => __('The max usage field must be greater than 0.'),
            'max_customer_usage.integer' => __('The max customer usage field must be an integer.'),
            'max_customer_usage.min' => __('The max customer usage field must be greater than 0.'),
            'code_pattern.string' => __('The code pattern field must be a string.'),
            'code_pattern.max' => __('The code pattern field must be less than 255 characters.'),
            'code_pattern.unique' => __('The code pattern has already been taken.'),
            'is_active.boolean' => __('The is active field must be a boolean.'),
        ];
    }
}
