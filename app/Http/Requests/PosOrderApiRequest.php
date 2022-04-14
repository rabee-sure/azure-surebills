<?php

namespace App\Http\Requests;

use App\Models\Application;
use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\BillTotalValidation;

class PosOrderApiRequest extends FormRequest
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
        if ($this->has('customer_mobile')) {
            $mobile = ltrim($this->customer_mobile, '+966');
            $mobile = ltrim($mobile, '966');
            $mobile = (int) $mobile;
            $this->merge(['customer_mobile'=> $mobile]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id' => ['required'],
            'customer_name' => ['required', 'string', 'max:50'],
            'customer_email' => ['required', 'string', 'email', 'max:50'],
            'customer_mobile' => ['required', 'regex:/(^[5]{1}[0-9]{8}$)/'],
            'customer_notes' => ['nullable'],

            'add_discount' => ['nullable'],
            'discount_type' => ['required_if:add_discount,on', Rule::in(['fixed', 'percentage'])],
            'discount_value' => ['required_if:add_discount,on'],

            'add_tax' => ['nullable'],
            'tax_value' => ['required_if:add_tax,on'],

            'items' => ['required', new BillTotalValidation],
            'items.*.name' => 'required',
            'items.*.category' => 'required',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
          'customer_name.required' => __('customer name required'),
          'customer_mobile.required' => __('customer mobile required'),
          'customer_mobile.regex' => __('customer mobile is not correct'),
          'items.*.name.required' => __('item name required'),
          'items.*.category.required' => __('item category required'),
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
            'add_discount' => $this->add_discount == 'on' ? true : (($this->add_discount == 'off') ? false : null),
            'add_tax' => $this->add_tax == 'on' ? true : (($this->add_tax == 'off') ? false : null),
        ]);
    }
}
