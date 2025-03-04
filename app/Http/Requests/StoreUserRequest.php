<?php

namespace App\Http\Requests;

use App\Rules\PasswordRule;
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
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'mobile' => ['required', 'regex:/(^[5]{1}[0-9]{8}$)/','unique:users,mobile,NULL,id,deleted_at,NULL'],
            'password' => ['required', 'string', 'min:8', new PasswordRule],
            'confirm_password' =>  ['required', 'same:password'],
        ];

        if($this->_method == 'PATCH')
        {
            $rules['password'] = ['nullable', 'string', 'min:8', new PasswordRule];
            $rules['confirm_password'] =  ['same:password'];
            $rules['email'] = ['required', 'string', 'email', 'max:50', 'unique:users,email,'.$this->user->id.',id,deleted_at,NULL'];
            $rules['mobile'] = ['required', 'regex:/(^[5]{1}[0-9]{8}$)/', 'unique:users,mobile,'.$this->user->id.',id,deleted_at,NULL'];
        }
        else
        {
            $rules['role'] = ['required'];
        }

        return $rules;
    }
}
