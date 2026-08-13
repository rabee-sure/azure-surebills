<?php

namespace App\Http\Requests;

use App\Rules\EmailFormat;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'source' => 'required',
            'name' => 'required|string',
            'email' => ['required', 'email', new EmailFormat()],
            'company' => 'required',
            'mobile' => 'required|regex:/(^[5]{1}[0-9]{8}$)/',
            'message' => 'required'
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
          'source.required' => __('source required'),
          'name.string' => __('name must be real'),
          'email.required' => __('email required'),
          'email.email' => __('email must be email format'),
          'company.required' => __('company required'),
          'mobile.required' => __('mobile required'),
          'mobile.regex' => __('mobile is not correct'),
          'message.required' => __('message required'),
        ];
    }
}
