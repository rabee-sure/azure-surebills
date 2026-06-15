<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesMerchantDropzoneDocuments;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateUploadFile;
use App\Rules\ValidateIban;
use App\Rules\ValidateIbanNotAllowWhiteSpaces;
use Illuminate\Validation\Validator;

class BankInformationRequest extends FormRequest
{
    use ValidatesMerchantDropzoneDocuments;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->prepareMerchantDropzoneDocuments();
    }

    public function withValidator(Validator $validator): void
    {
        $this->withMerchantDropzoneDocumentDistinctValidator($validator);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bank_id' => ['required'],
            'beneficiary_name' => ['required', 'regex:/^[a-zA-Z ]+$/', 'max:50'],
            'iban_number' => ['required', new ValidateIban(), new ValidateIbanNotAllowWhiteSpaces()],
            'document' => ['nullable', 'array', 'max:5'],
            'document.*' => ['required', 'string', 'max:2048', new ValidateUploadFile(['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xlsx', 'csv'])],
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
          'bank_id.required' => __('bank required'),
          'iban_number.required' => __('iban number required'),
          'beneficiary_name.required' => __('beneficiary name required'),
          'beneficiary_name.regex' => __('beneficiary name must english'),
          'document.max' => __('You may upload at most 5 documents.'),
        ];
    }
}
