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
            'business_name' => ['required'],
            'sector' => ['nullable'],
            'website' => ['nullable'],
            'twitter' => ['nullable'],
            'facebook' => ['nullable'],
            'instagram' => ['nullable'],
            'logo' => ['required','image','mimes:jpeg,png,jpg,gif,svg'],
            'description' => ['nullable'],
            'business_address' => ['required'],
            'business_mobile' => ['required'],
            'vat_registration_number' => ['nullable'],
        ];
    }
}
