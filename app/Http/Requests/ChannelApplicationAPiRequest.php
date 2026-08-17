<?php

namespace App\Http\Requests;

use App\Rules\EmailFormat;
use Illuminate\Foundation\Http\FormRequest;

class ChannelApplicationAPiRequest extends FormRequest
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
            'channel_token' => ['required'],
            'email' => ['required', 'email', new EmailFormat(), 'unique:users'],
            'mobile' => ['required', 'unique:users'],
            'redirect' => ['required'],
            'webhook_url' => ['required'],
            'mada_fixed' => ['required'],
            'mada_percentage' => ['required'],
            'credit_cards_fixed' => ['required'],
            'credit_cards_percentage' => ['required'],
        ];
    }
}
