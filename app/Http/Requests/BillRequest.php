<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BillRequest extends FormRequest
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
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'string', 'email', 'max:255'],
            'customer_mobile' => ['required', 'regex:/(^[5]{1}[0-9]{8}$)/'],
            'customer_notes' => ['nullable'],

            'add_discount' => ['required'],
            // 'discount_type' => ['required_if:add_discount,==,true', Rule::in(['fixed', 'percentage'])],
            // 'discount_value' => ['required_if:add_discount,==,true'],

            'add_tax' => ['required'],
            // 'tax_name' => ['required_if:add_discount,==,true'],
            // 'tax_value' => ['required_if:add_discount,==,true'],            

            // 'send_sms' => ['required'],
            // 'send_email' => ['required'],
        ];
    }
}
