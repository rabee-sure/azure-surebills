<?php

namespace App\Http\Requests;

use App\Models\Application;
use App\Models\Customer;
use App\Rules\AmountPartialRefund;
use App\Rules\BillTotalValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartialRefundRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        if ($this->has('customer_mobile')) {
            $mobile = ltrim($this->customer_mobile, '+966');
            $mobile = ltrim($mobile, '966');
            $mobile = (int) $mobile;
            $this->merge(['customer_mobile'=> $mobile]);
        }

        if (!$this->has('is_redirect')) {
            $this->is_redirect = true;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => ['required', new AmountPartialRefund($this->id)],
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
