<?php

namespace App\Http\Requests;

use App\Models\Application;
use App\Models\Customer;
use App\Rules\EmailFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\BillTotalValidation;
use App\Rules\UniqeBillReference;

class BillApiRequest extends FormRequest
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
            $this->is_redirect = true;
        }

        if(config('bills.pay_page_expiration_time_type') == 'Days')
        {
            if($this->expiry_date){
                if($this->expiry_date >= config('bills.pay_page_expiration_time')){
                    $this->merge(['expiry_date' => config('bills.pay_page_expiration_time')]);
                    $this->merge(['expiry_hours' => 0]);
                    $this->merge(['expiry_minutes' => 0]);
                }elseif($this->expiry_date < 1){
                    $this->merge(['expiry_date' => 1]);
                }
            }else{
                $this->merge(['expiry_date' => 1]);
            }
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Hours')
        {
            $this->merge(['expiry_date' => 0]);
            if($this->expiry_hours){
                if($this->expiry_hours >= config('bills.pay_page_expiration_time')){
                    $this->merge(['expiry_hours' => config('bills.pay_page_expiration_time')]);
                    $this->merge(['expiry_minutes' => 0]);
                }elseif($this->expiry_hours < 1){
                    $this->merge(['expiry_hours' => 1]);
                }
            }else{
                $this->merge(['expiry_hours' => 1]);
            }
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Minutes')
        {
            $this->merge(['expiry_date' => 0]);
            $this->merge(['expiry_hours' => 0]);
            if($this->expiry_minutes){
                if($this->expiry_minutes >= config('bills.pay_page_expiration_time')){
                    $this->merge(['expiry_minutes' => config('bills.pay_page_expiration_time')]);
                }elseif($this->expiry_minutes < 1){
                    $this->merge(['expiry_minutes' => 1]);
                }
            }else{
                $this->merge(['expiry_minutes' => 5]);
            }
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
            'application_id' => ['required'],
            'application_secret' => ['required'],

            'reference_id' => [
                'required',
                'string',
                'max:255',
                new UniqeBillReference
                // Rule::unique('bills')->where(function ($query) use ($application) {
                //     return $query->where('user_id', $application->user_id ?? null)->where('status', 'pending');
                // })
            ],
            'customer_name' => ['required', 'string', 'max:50', 'regex:/^[\p{L}\s]+$/u'],
            'customer_email' => ['required', 'string', new EmailFormat, 'max:50'],
            'customer_mobile' => ['required'],
            'customer_notes' => ['nullable'],

            'due_date' => ['required', 'date_format:d-m-Y'],

            'coupon_code' => ['nullable', 'string', 'max:255'],
            'add_discount' => ['nullable'],
            'discount_type' => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('add_discount') === 'on'),
                Rule::in(['fixed', 'percentage']),
            ],
            'discount_value' => ['required_if:add_discount,on'],

            'add_tax' => ['nullable'],
            'tax_value' => ['required_if:add_tax,on'],

            'send_sms' => ['nullable'],
            'send_email' => ['nullable'],
            'is_redirect' => ['nullable'],

            'items' => ['required', new BillTotalValidation],
            'items.*.name' => 'required',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',

            'application_name' => ['nullable'],
            'redirect_url' => ['nullable'],
            'webhook_url' => ['nullable'],

            'tags' => ['nullable', 'string'],
        ];

        if(config('bills.pay_page_expiration_time_type') == 'Days')
        {
            $rules['expiry_date'] = ['required', 'numeric', 'min:0', 'max:'.config('bills.pay_page_expiration_time')];
            $rules['expiry_hours'] = ['nullable', 'numeric', 'min:0', 'max:23'];
            $rules['expiry_minutes'] = ['nullable', 'numeric', 'min:0', 'max:59'];
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Hours')
        {
            $rules['expiry_date'] = ['nullable', 'numeric', 'max:0'];
            $rules['expiry_hours'] = ['required', 'numeric', 'min:0', 'max:'.config('bills.pay_page_expiration_time')];
            $rules['expiry_minutes'] = ['nullable', 'numeric', 'min:0', 'max:59'];
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Minutes')
        {
            $rules['expiry_date'] = ['nullable', 'numeric', 'max:0'];
            $rules['expiry_hours'] = ['nullable', 'numeric', 'max:0'];
            $rules['expiry_minutes'] = ['required', 'numeric', 'min:0', 'max:'.config('bills.pay_page_expiration_time')];
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
          'customer_name.required' => __('customer name required'),
          'customer_mobile.required' => __('customer mobile required'),
          'customer_mobile.regex' => __('customer mobile is not correct'),
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
        $this->merge([
            'add_discount' => $this->add_discount == 'on' ? true : (($this->add_discount == 'off') ? false : null),
            'add_tax' => $this->add_tax == 'on' ? true : (($this->add_tax == 'off') ? false : null),
            'send_sms' => $this->send_sms == 'on' ? true : (($this->send_sms == 'off') ? false : null),
            'send_email' => $this->send_email == 'on' ? true : (($this->send_email == 'off') ? false : null),
        ]);
    }
}
