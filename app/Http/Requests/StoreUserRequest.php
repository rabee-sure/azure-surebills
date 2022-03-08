<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
            'mobile' => ['required', 'regex:/(^[5]{1}[0-9]{8}$)/','unique:users,mobile'],
            'gender' => ['required', Rule::in(1,2)],
            'password' => ['required', 'min:8'],
            'confirm_password' =>  ['required', 'same:password'],
        ];

        if($this->_method == 'PATCH')
        {
            $rules['password'] = ['nullable', 'min:8'];
            $rules['confirm_password'] =  ['same:password'];
            $rules['email'] = ['required', 'string', 'email', 'max:50', 'unique:users,email,'.$this->user->id.',id'];
            $rules['mobile'] = ['required', 'regex:/(^[5]{1}[0-9]{8}$)/','unique:users,mobile,'.$this->user->id.',id'];
        }

        return $rules;
    }
}
