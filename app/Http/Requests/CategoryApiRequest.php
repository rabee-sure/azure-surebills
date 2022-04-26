<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateUploadFile;

class CategoryApiRequest extends FormRequest
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
            'name_en' => ['required'],
            'name_ar' => ['required'],
            'image' => ['nullable', new ValidateUploadFile(['png', 'jpg', 'jpeg'])],
            'sort_number' => ['required'],
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
          'name_en.required' => __('Name En required'),
          'name_ar.required' => __('Name Ar required'),
        ];
    }
}
