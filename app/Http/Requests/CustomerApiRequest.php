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

    protected function prepareForValidation()
    {
        $user = $this->application->user ?? null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'name' => ['required', 'string', 'max:50'],
            'email' => ['nullable','email', 'max:50',
                Rule::unique('customers')->where(function ($query){
                    return $query->where('user_id', $this->user->id);
                })
            ],
            'mobile' => ['required', 'regex:/(^[5]{1}[0-9]{8}$)/',
                Rule::unique('customers')->where(function ($query){
                    return $query->where('user_id', $this->user->id);
                })
            ],
            'notes' => ['nullable'],
        ];
    }
}
