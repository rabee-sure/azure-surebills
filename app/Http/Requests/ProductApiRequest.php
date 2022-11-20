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
        $rules = [
            'name_en' => ['required'],
            'name_ar' => ['required'],
            'price' => ['required', 'numeric'],
            'image.*' => [new ValidateUploadFile(['png', 'jpg', 'jpeg'])],
            'sort_number' => ['required'],
            'category_id' => ['required'],
        ];

        if($this->enable_customizations || $this->request->get('enable_customizations'))
        {
            $rules['customization_name_ar'] = ['required', 'array', 'min:1'];
            $rules['customization_name_ar.*'] = ['required', 'string'];
            $rules['customization_name_en'] = ['required', 'array', 'min:1'];
            $rules['customization_name_en.*'] = ['required'];
            $rules['customization_price'] = ['required', 'array', 'min:1'];
            $rules['customization_price.*'] = ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'];
        }

        return $rules;
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
          'price.required' => __('Price required'),
          'price.numeric' => __('Price must be number'),
          'category_id.required' => __('Product Category required'),
          'customization_name_ar.*.required' => __('Customization name Ar required'),
          'customization_name_en.*.required' => __('Customization name En required'),
          'customization_price.*.required' => __('Customization price required'),
        ];
    }
}
