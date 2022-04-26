<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateUploadFile;

class ProductApiRequest extends FormRequest
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
            'discription_en' => ['required'],
            'discription_ar' => ['required'],
            'price' => ['required', 'numeric'],
            'image.*' => [new ValidateUploadFile(['png', 'jpg', 'jpeg'])],
            'sort_number' => ['required'],
            'category_id' => ['required'],
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
          'discription_en.required' => __('Discription En required'),
          'discription_ar.required' => __('Discription Ar required'),
          'price.required' => __('Price required'),
          'price.numeric' => __('Price must be number'),
          'category_id.required' => __('Product Category required'),
        ];
    }
}
