<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\BillTotalValidation;

class DebitNoteRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        if (!$this->has('is_redirect')) {
            $this->is_redirect = false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'customer_notes' => ['nullable'],
            'due_date' => ['required'],
            'add_discount' => ['nullable'],
            'discount_type' => ['required_if:add_discount,on', Rule::in(['fixed', 'percentage'])],
            'discount_value' => ['required_if:add_discount,on'],
            'items' => ['required', new BillTotalValidation],
            'items.*.name' => 'required|string|max:50',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
        ];

        if(config('bills.pay_page_expiration_time_type') == 'Days')
        {
            $rules['expiry_date'] = ['required', 'numeric', 'min:1', 'max:'.config('bills.pay_page_expiration_time')];
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Hours')
        {
            $rules['expiry_hours'] = ['required', 'numeric', 'min:1', 'max:'.config('bills.pay_page_expiration_time')];
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Minutes')
        {
            $rules['expiry_minutes'] = ['required', 'numeric', 'min:1', 'max:'.config('bills.pay_page_expiration_time')];
        }

        return $rules;
    }


    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
          'discount_value.required_if' => __('Discount value is required'),
          'items.*.name.required' => __('item name required'),
          'items.*.name.max' => __('item name should not be greater than 50 character'),
          'items.*.price.required' => __('item price required'),
          'items.*.quantity.required' => __('item quantity required'),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $this->merge([
            'add_discount' => $this->add_discount== 'on' ? true : false,
        ]);
    }
}
