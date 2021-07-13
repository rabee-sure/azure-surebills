<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChannelUpdateApplicationPaymentFeesApiRequest extends FormRequest
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
            'application_id' => ['required','exists:applications,id'],
            'mada_fixed' => ['required'],
            'mada_percentage' => ['required'],
            'credit_cards_fixed' => ['required'],
            'credit_cards_percentage' => ['required'],
        ];
    }
}
