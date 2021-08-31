<?php

namespace App\Http\Requests;

use App\Models\Application;
use App\Models\Customer;
use App\Rules\AmountPartialRefund;
use App\Rules\AmountPartialRefundGTBalance;
use App\Rules\BillTotalValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RefundRequest extends FormRequest
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
            'type' => ['nullable'],
            'amount' => ['nullable', 'required_if:type,partial_refund', new AmountPartialRefund($this->id), 'integer', 'gt:0', new AmountPartialRefundGTBalance($this->id)],
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
          'customer_name.required' => __('customer name required'),
        ];
    }
}
