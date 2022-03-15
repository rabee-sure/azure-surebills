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
            'active' => ['required'],
            'category_id' => ['required'],
        ];
    }
}
