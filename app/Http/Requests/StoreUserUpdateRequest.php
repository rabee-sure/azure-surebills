<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\EmailFormat;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserUpdateRequest extends FormRequest
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
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->id : $user;

        $emailRules = ['required', 'string', 'email', new EmailFormat(), 'max:50'];
        $mobileRules = ['required', 'regex:/(^[5]{1}[0-9]{8}$)/'];

        if ($userId) {
            $emailRules[] = 'unique:users,email,'.$userId.',id';
            $mobileRules[] = 'unique:users,mobile,'.$userId.',id';
        }

        return [
            'name' => ['required', 'string', 'max:50'],
            'email' => $emailRules,
            'mobile' => $mobileRules,
            'password' => ['nullable', 'string', 'min:8', new PasswordRule],
            'confirm_password' => ['same:password'],
            'role' => ['required'],
        ];
    }
}
