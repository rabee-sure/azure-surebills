<?php

namespace App\Http\Requests;

use App\Rules\EmailChannel;
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
        $rules =  [
            'mada_fixed' => ['required', 'numeric', 'max:1000', 'min:'.$this->channel->mada_fixed],
            'mada_percentage' => ['required', 'numeric', 'max:100', 'min:'.$this->channel->mada_percentage],
            'credit_cards_fixed' => ['required', 'numeric', 'max:1000', 'min:'.$this->channel->credit_cards_fixed],
            'credit_cards_percentage' => ['required', 'numeric', 'max:100', 'min:'.$this->channel->credit_cards_percentage],            

            'redirect' => ['required', 'url'],
            'webhook_url' => ['required', 'url'],
        ];

        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return array_merge($rules, [
                    'email' => [
                        'required',
                        'email', 
                        'exists:users,email', 
                        new EmailChannel],
                ]);
            }
            case 'PUT':
            case 'PATCH':
            {
                return $rules;
            }
            default:break;
        }
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
