<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Application;
use Illuminate\Validation\Rule;
use App\Rules\AmountPartialRefund;
use App\Rules\BillTotalValidation;
use App\Rules\AmountPartialRefundGTBalance;
use Illuminate\Foundation\Http\FormRequest;

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
            'amount' => ['nullable', 'required_if:type,partial_refund', new AmountPartialRefund($this->id), 'numeric', 'gt:0', new AmountPartialRefundGTBalance($this->id)],
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


    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {

        $bill = Bill::find($this->id);
        $validator->after(function ($validator) use($bill){
            $otherDate = Carbon::now()->subDays(14);

            if ($otherDate->gt($bill->paid_at)) {
                $validator->errors()->add('field', __('It must not pass more than 14 days on the date of payment of the Bill'));
            }
        });
    }
}
