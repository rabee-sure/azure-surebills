<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChannelApplicationRequest extends FormRequest
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
            'email' => ['required','email', 'exists:users,email'],
            'mada_fixed' => ['required', 'numeric', 'max:1000'],
            'mada_percentage' => ['required', 'numeric', 'max:100'],
            'credit_cards_fixed' => ['required', 'numeric', 'max:1000'],
            'credit_cards_percentage' => ['required', 'numeric', 'max:100'],
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
            'email.exists' => __("We can't find a user with that e-mail address")
        ];
    }
}
