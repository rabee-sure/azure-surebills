<?php

namespace App\Http\Requests;

use App\Rules\ValidateIban;
use App\Rules\ValidateUploadFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInformationRequest extends FormRequest
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
        $bank = [
            'bank_id' => ['required'],
            'iban_number' => ['required', new ValidateIban()],
            'beneficiary_name' => ['required', 'max:50'],
            'bank_documents' => ['nullable', 'array', "max:5"],
        ];

        $business =  [
            'license_type' => ['required', Rule::in(['Commercial Record','Freelance'])],
            'commercial_registry_expiry_date' => ['required_if:license_type,Commercial Record', 'required'],
            'business_name_en' => ['required', 'max:50'],
            'business_name_ar' => ['required', 'max:50'],
            'sector' => ['nullable', 'max:50'],
            'website' => ['nullable'],
            'logo' => ['nullable'],
            'facebook' => ['nullable', 'url'],
            'instagram' => ['nullable', 'url'],
            'hidden_logo' => ['nullable'],
            'logo' => ['nullable'],
            'description' => ['nullable'],
            'business_address' => ['required', 'max:100'],
            'business_mobile' => ['required'],
            'vat_registration_number' => ['nullable'],
            'business_documents' => ['nullable', 'array', "max:5"],
        ];

        if($this->type == 'bank')
            return $bank;
        elseif($this->type == 'business')
            return $business;
        else
            return array_merge($business, $bank);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */

    public function messages()
    {
        return [
            'bank_id.required' => __('bank required'),
            'iban_number.required' => __('iban number required'),
            'beneficiary_name.required' => __('beneficiary name required'),
            'license_type.required' => __('license type required'),
            'business_name_en.required' => __('business name required'),
            'business_name_ar.required' => __('business name required'),
            'business_address.required' => __('business address required'),
            'business_mobile.required' => __('business mobile required'),
            'logo.required_without' => __('Logo required'),
        ];
    }
}
