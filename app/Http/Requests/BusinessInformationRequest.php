<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessInformationRequest extends FormRequest
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
            'license_type' => ['required'],
            'business_name_en' => ['required'],
            'business_name_ar' => ['required'],
            'sector' => ['nullable'],
            'website' => ['nullable'],
            'twitter' => ['nullable'],
            'facebook' => ['nullable'],
            'instagram' => ['nullable'],
            'hidden_logo' => ['nullable'],
            'logo' => ['nullable','image','mimes:jpeg,png,jpg,gif,svg'],
            'description' => ['nullable'],
            'business_address' => ['required'],
            'business_mobile' => ['required'],
            'vat_registration_number' => ['nullable'],

            'document' => ['nullable', 'array', "max:5"],
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
          'license_type.required' => __('license type required'),
          'business_name_en.required' => __('business name required'),
          'business_name_ar.required' => __('business name required'),
          'business_address.required' => __('business address required'),
          'business_mobile.required' => __('business mobile required'),
          'logo.required_without' => __('Logo required'),
        ];
    }
}
