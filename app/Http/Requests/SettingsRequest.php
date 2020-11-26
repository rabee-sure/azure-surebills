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
        return [
            'create_send_sms' => ['nullable'],
            'create_send_email' => ['nullable'],
            'paid_send_email' => ['nullable'],
            'paid_send_email' => ['nullable'],
            'tax_value' => ['required_if:add_tax,on', 'between:1,100', 'numeric'],
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
            'add_tax' => $this->add_tax == 'on' ? true : false,
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
}
