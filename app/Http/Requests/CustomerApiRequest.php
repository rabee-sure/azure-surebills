<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerApiRequest extends FormRequest
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
        $user = auth('api')->user();
        return [

            'name' => ['required', 'string', 'max:50'],
            'email' => ['nullable','email', 'max:50',
                Rule::unique('customers')->where(function ($query) use ($user){
                    return $query->where('user_id', $user->id);
                })
            ],
            'mobile' => ['required', 'regex:/(^[5]{1}[0-9]{8}$)/',
                Rule::unique('customers')->where(function ($query) use ($user){
                    return $query->where('user_id', $user->id);
                })
            ],
            'notes' => ['nullable'],
        ];
    }
}
