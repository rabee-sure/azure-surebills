<?php

namespace App\Http\Requests;

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
            'business_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['required', 'unique:users',
                // 'regex:/^((?:[+?0?0?966]+)(?:\s?\d{2})(?:\s?\d{7}))$/', //Saudi number with +966
                'regex:/(^[5]{1}[0-9]{8}$)/',
            ],
            'password' => [
                'required', 
                'string', 
                'min:8',                // must be at least 8 characters in length
                'regex:/[a-z]/',        // must contain at least one lowercase letter
                'regex:/[A-Z]/',        // must contain at least one uppercase letter
                'regex:/[0-9]/',        // must contain number
                'confirmed'
            ],
        ];
    }
}
