<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
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
        $rules = [
            'create_send_sms' => ['nullable'],
            'create_send_email' => ['nullable'],
            'paid_send_email' => ['nullable'],
            'paid_send_email' => ['nullable'],
        ];

        if(request()->add_tax == 'on')
        {
            $rules['tax_value'] = ['required_if:add_tax,on', 'between:0.1,100'];
        }

        if(request()->add_tax_invoice == 'on')
        {
            $rules['bullding_no'] = ['required_if:add_tax_invoice,on'];
            $rules['street_name'] = ['required_if:add_tax_invoice,on'];
            $rules['district'] = ['required_if:add_tax_invoice,on'];
            $rules['postal_code'] = ['required_if:add_tax_invoice,on'];
        }

        return $rules;
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
            'add_tax' => $this->add_tax == 'on' ? true : false,
            'add_tax_invoice' => $this->add_tax_invoice == 'on' ? true : false,
            'display_customer_details' => $this->display_customer_details == 'on' ? true : false,
            'create_send_sms' => $this->create_send_sms == 'on' ? true : false,
            'create_send_email' => $this->create_send_email == 'on' ? true : false,
            'paid_send_sms' => $this->paid_send_sms == 'on' ? true : false,
            'paid_send_email' => $this->paid_send_email == 'on' ? true : false,
            'active_lang' => $this->getActiveLang(),
            'default_lang' => $this->getDefaultLang(),
        ]);
    }


        /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function getActiveLang()
    {
        if($this->active_lang_en == 'on' && $this->active_lang_ar == 'on'){
            return 'all';
        }elseif($this->active_lang_en == 'on'){
            return 'en';
        }elseif($this->active_lang_ar == 'on'){
            return 'ar';
        }else{
            return 'ar';
        }
    }

        /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function getDefaultLang()
    {
        if($this->active_lang_en == 'on' && $this->active_lang_ar == 'on'){
            return $this->default_lang;
        }elseif($this->active_lang_en == 'on'){
            return 'en';
        }elseif($this->active_lang_ar == 'on'){
            return 'ar';
        }else{
            return 'ar';
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
          'bullding_no.required_if' => __('The bullding no field is required when add tax invoice is on.'),
          'street_name.required_if' => __('The street name field is required when add tax invoice is on.'),
          'district.required_if' => __('The district field is required when add tax invoice is on.'),
          'postal_code.required_if' => __('The postal code field is required when add tax invoice is on.'),
        ];
    }
}
