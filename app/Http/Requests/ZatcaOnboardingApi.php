<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZatcaOnboardingApi extends FormRequest
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
            "merchant_uuid" => ['required'],
            "merchant_email" => ['required', 'email'],
            "business_name_en" => ['required', 'regex:/^[a-zA-Z ]+$/'],
            "vat_registration_number" => ['required', 'numeric', 'regex:/^3\d{13}3$/'],
            "tin" => ['required', 'numeric', 'digits:10'],
            "crn" => ['required', 'numeric', 'digits:10'],
            "invoices_type" => ['required', 'in:B2C,B2B,Both'],
            "OTP" => ['required', 'numeric', 'digits:6'],
            "business_category" => ['required'],
            "building_no" => ['required', 'numeric', 'digits:4'],
            "street_name" => ['required'],
            "district" => ['required'],
            "city" => ['required', 'regex:/^[a-zA-Z ]+$/'],
            "postal_code" => ['required', 'numeric', 'digits:5'],
            "additional_no" => ['required','numeric'],
            "other_buyer_id" => ['required','numeric']
        ];
    }

    public function messages(){
        return [
            "merchant_uuid.required" => "merchant_uuid is required",
            "merchant_email.required" => "merchant_email is required",
            "merchant_email.email" => "merchant_email must be a valid email",
            "business_name_en.required" => "business_name_en is required",
            "business_name_en.regex" => "business_name_en must be only english letters",
            "vat_registration_number.required" => "vat_registration_number is required",
            "vat_registration_number.numeric" => "vat_registration_number must be numeric",
            "vat_registration_number.regex" => "vat_registration_number must be 15 digits and start with 3 end with 3",
            "tin.required" => "tin is required",
            "tin.numeric" => "tin must be numeric",
            "tin.digits" => "tin must be 10 digits",
            "crn.required" => "crn is required",
            "crn.numeric" => "crn must be numeric",
            "crn.digits" => "crn must be 10 digits",
            "invoices_type.required" => "invoices_type is required",
            "invoices_type.in" => "invoices_type must be B2C, B2B or Both",
            "OTP.required" => "OTP is required",
            "OTP.numeric" => "OTP must be numeric",
            "OTP.digits" => "OTP must be 6 digits",
            "business_category.required" => "business_category is required",
            "building_no.required" => "building_no is required",
            "building_no.numeric" => "building_no must be numeric",
            "building_no.digits" => "building_no must be 4 digits",
            "street_name.required" => "street_name is required",
            "district.required" => "district is required",
            "city.required" => "city is required",
            "city.regex" => "city must be only english letters",
            "postal_code.required" => "postal_code is required",
            "postal_code.numeric" => "postal_code must be numeric",
            "postal_code.digits" => "postal_code must be 5 digits",
            "additional_no.required" => "additional_no is required",
            "additional_no.numeric" => "additional_no must be numeric",
            "other_buyer_id.required" => "other_buyer_id is required",
            "other_buyer_id" => "other_buyer_id must be numeric"
        ];
    }
}
