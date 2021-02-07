<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateUploadFile;

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
            // 'commercial_registry_expiry_date' => ['required_if:license_type,Commercial Record', 'required'],
            'business_name_en' => ['required', 'max:50'],
            'business_name_ar' => ['required', 'max:50'],
            'sector' => ['nullable', 'max:50'],
            'website' => ['nullable', 'url'],
            'twitter' => ['nullable', 'url'],
            'facebook' => ['nullable', 'url'],
            'instagram' => ['nullable', 'url'],
            'hidden_logo' => ['nullable'],
            'logo' => ['nullable', new ValidateUploadFile(['png', 'jpg', 'jpeg'])],
            'description' => ['nullable'],
            'business_address' => ['required', 'max:100'],
            'business_mobile' => ['required'],
            'vat_registration_number' => ['nullable'],
            'document' => ['nullable', 'array', "max:5"],
            'document.*' => ['required', new ValidateUploadFile(['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xlsx', 'csv'])],
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
