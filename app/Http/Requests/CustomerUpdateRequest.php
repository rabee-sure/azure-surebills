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
        return [
            'name' => ['required', 'string', 'max:50', 'regex:/^[\p{L}\s]+$/u'],
            'email' => ['nullable',new EmailFormat, 'max:50',
                Rule::unique('customers')->where(function ($query){
                    return $query->where('user_id', auth()->user()->id);
                })->ignore($this->customer->id)
            ],
            'mobile' => ['required', 'regex:/(^[5]{1}[0-9]{8}$)/',
                Rule::unique('customers')->where(function ($query){
                    return $query->where('user_id', auth()->user()->id);
                })->ignore($this->customer->id)
            ],
            'notes' => ['nullable'],
        ];
    }
}
