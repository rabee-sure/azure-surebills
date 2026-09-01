<?php

namespace App\Http\Requests;

use App\Rules\EmailFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerUpdateRequest extends FormRequest
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
        $customerId = optional($this->route('customer'))->id;

        $emailRules = ['nullable', new EmailFormat, 'max:50'];
        $mobileRules = ['required', 'regex:/(^[5]{1}[0-9]{8}$)/'];

        if ($customerId) {
            $emailRules[] = Rule::unique('customers')->where(function ($query) {
                return $query->where('user_id', auth()->user()->id);
            })->ignore($customerId);
            $mobileRules[] = Rule::unique('customers')->where(function ($query) {
                return $query->where('user_id', auth()->user()->id);
            })->ignore($customerId);
        }

        return [
            'name' => ['required', 'string', 'max:50', 'regex:/^[\p{L}\s]+$/u'],
            'email' => $emailRules,
            'mobile' => $mobileRules,
            'notes' => ['nullable'],
        ];
    }
}
