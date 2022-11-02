<?php

namespace App\Http\Requests;

use App\Rules\ValidateIban;
use App\Rules\ValidateUploadFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PosRedirectToBillsProductsRequest extends FormRequest
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
            'password' => 'required',
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
