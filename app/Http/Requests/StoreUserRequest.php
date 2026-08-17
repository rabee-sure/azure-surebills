<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\EmailFormat;
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
            'email' => ['required', new EmailFormat(), 'max:50', 'unique:users,email,NULL,id'],
            'mobile' => ['required', 'regex:/(^[5]{1}[0-9]{8}$)/','unique:users,mobile,NULL,id'],
            'password' => ['required', 'string', 'min:8', new PasswordRule],
            'confirm_password' =>  ['required', 'same:password'],
            'role' => ['required'],
        ];

        if($this->_method == 'PATCH')
        {
            $user = User::withTrashed()->find($this->user);
            $rules['password'] = ['nullable', 'string', 'min:8', new PasswordRule];
            $rules['confirm_password'] =  ['same:password'];
            $rules['email'] = ['required', 'string', 'email', 'max:50', 'unique:users,email,'.$user->id.',id'];
            $rules['mobile'] = ['required', 'regex:/(^[5]{1}[0-9]{8}$)/', 'unique:users,mobile,'.$user->id.',id'];
            if($user->store_main_user_id == null){
                $rules['role'] = [];
            }
        }

        return $rules;
    }
}
