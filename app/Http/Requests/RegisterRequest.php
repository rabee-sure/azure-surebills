<?php

namespace App\Http\Requests;

use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'business_name' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
            'mobile' => ['required', 'unique:users',
                // 'regex:/^((?:[+?0?0?966]+)(?:\s?\d{2})(?:\s?\d{7}))$/', //Saudi number with +966
                'regex:/(^[5]{1}[0-9]{8}$)/',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                new PasswordRule,
                'confirmed'
            ],
            'terms' => 'required'
        ];
    }
}
