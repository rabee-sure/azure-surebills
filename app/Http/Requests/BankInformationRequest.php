<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankInformationRequest extends FormRequest
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
            'bank' => ['required'],
            'iban_number' => ['required'],
            'organization_name' => ['required'],
            'beneficiary_name' => ['required'],
        ];
    }
}
