<?php

namespace App\Http\Requests;

use App\Customer;
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
            'customer_email' => ['nullable', 'email', 'max:255',
                // Rule::unique('customers', 'email')->where(function ($query){
                //     return $query->where('user_id', auth()->user()->id)
                //     ->where('mobile',  '500000000');
                // })
            ],
            'customer_mobile' => ['required', 'regex:/(^[5]{1}[0-9]{8}$)/'],
            'customer_notes' => ['nullable'],            

            'due_date' => ['required'],
            'expiry_date' => ['required'],
            'expiry_hours' => ['numeric','min:0','max:23','nullable'],
            'expiry_minutes' => ['numeric','min:0','max:59','nullable'],

            'add_discount' => ['nullable'],
            'discount_type' => ['required_if:add_discount,on', Rule::in(['fixed', 'percentage'])],
            'discount_value' => ['required_if:add_discount,on'],

            'add_tax' => ['nullable'],
            'tax_value' => ['required_if:add_tax,on'],            

            'send_sms' => ['nullable'],
            'send_email' => ['nullable'],

            'items.*.name' => 'required',
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
          'items.*.name.required' => __('item name required'),
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
        // $validator->after(function ($validator) {
        //     $email_user = Customer::where('email', $this->customer_email)
        //         ->where('user_id', auth()->user()->id)
        //         ->first();
        //     $mobile_user = Customer::where('mobile', $this->customer_mobile)
        //         ->where('user_id', auth()->user()->id)
        //         ->first();

        //     if((isset($email_user) && $mobile_user == null)){
        //         $validator->errors()->add('customer_mobile', 'Something is wrong with customer_mobile');
        //     }  
        //     if((isset($mobile_user) && $email_user == null)){
        //         $validator->errors()->add('customer_email', 'Something is wrong with customer_email!');
        //     }            
        //     if( isset($mobile_user) && ($mobile_user->email != $this->customer_email) ){
        //         $validator->errors()->add('customer_email', 'Something is wrong with customer_email!');
        //     }  
        //     if(isset($email_user) &&  ($email_user->mobile != $this->customer_mobile) ){
        //         $validator->errors()->add('customer_mobile', 'Something is wrong with customer_mobile!');
        //     }
        // });

        $this->merge([
            'add_discount' => $this->add_discount== 'on' ? true : false,
            'add_tax' => $this->add_tax == 'on' ? true : false,
            'send_sms' => $this->send_sms == 'on' ? true : false,
            'send_email' => $this->send_email == 'on' ? true : false,
        ]);
    }
}
