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
            'bank_id' => ['required'],
            'iban_number' => ['required'],
            'beneficiary_name' => ['required'],
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
        ];
    }
}
