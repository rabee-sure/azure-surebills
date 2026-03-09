<?php

namespace App\Http\Requests;

use App\Enums\CouponMechanism;
use Illuminate\Foundation\Http\FormRequest;

class CouponBulkGenerationCodesExportRequest extends FormRequest
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
            'format' => 'required|in:csv,excel',
        ];
    }

    public function messages()
    {
        return [
            'format.required' => __('The format field is required.'),
            'format.in' => __('The format field must be a valid format.'),
        ];
    }
}
