<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\App;

class CyperSourceProcessPaymentRequest extends FormRequest
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
        $billRule = ['billId' => ['required', Rule::exists('bills', 'id')->where('status', 'pending')]];
        if ($this->header('X-Pay-Token')) {
            return $billRule;
        }
        
        $cardRules = [
            'card_number' => 'required|digits_between:13,19',
            'card_expiration_month' => 'required|integer|between:1,12',
            'card_expiration_year' => 'required|integer|gte:' . date('Y'),
            'card_cvv' => 'required|digits_between:3,4',
        ];

        return array_merge($billRule, $cardRules);
    }

    /**
     * Get the error messages for the defined validation rules.
     * @return array
     * 
     */
    public function messages()
    {
        return [
            'billId.required' => trans('Bill is required'),
            'billId.exists' => trans('Invalid bill'),
            'card_number.digits_between' => trans('Invalid card number'),
            'card_cvv.digits_between' => trans('Invalid security code'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
