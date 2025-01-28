<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class sendInvoiveToZatcaApi extends FormRequest
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
            "bill.bill_uuid" => ['required'],
            "bill.bill_type" => ['required', 'in:Bill,Debit Note,Credit Note'],
            "bill.BillingRefrence" => ['required_if:bill.bill_type,Debit Note,Credit Note'],
            "bill.number" => ['required', 'numeric'],
            "bill.status" => ['required', 'in:pending,paid,paid_cash,paid_bank_transfer,paid_machine,refunded'],
            "bill.sub_total" => ['required', 'numeric', 'gt:0'],
            "bill.tax_value" => [
                'required_if:bill.bill_type,Bill', 
                function($attribute, $value, $fail) {
                    if(!is_null($value)) {
                        if(!is_numeric($value)) {
                            $fail('tax_value must be numeric.');
                        }
                        if($value <= 0) {
                            $fail('tax_value must be greater than 0.');
                        }
                    }
                }
            ],
            "bill.vat" => [
                'required_if:bill.bill_type,Bill', 
                function($attribute, $value, $fail) {
                    if(!is_null($value)) {
                        if(!is_numeric($value)) {
                            $fail('vat must be numeric.');
                        }
                        if($value <= 0) {
                            $fail('vat must be greater than 0.');
                        }
                    }
                }    
            ],
            "bill.discount" => [
                'required_if:bill.bill_type,Bill',
                function($attribute, $value, $fail) {
                    if(!is_null($value)) {
                        if(!is_numeric($value)) {
                            $fail('discount must be numeric.');
                        }
                        if($value < 0) {
                            $fail('discount must be equal or greater than 0.');
                        }
                    }
                }
            ],
            "bill.items.*.name" => ['required_if:bill.bill_type,in(Bill,Debit Note)'],
            "bill.items.*.quantity" => ['required_if:bill.bill_type,in(Bill,Debit Note)', 'numeric', 'gt:0'],
            "bill.items.*.price" => ['required_if:bill.bill_type,in(Bill,Debit Note)','numeric', 'gt:0'],
            "bill.created_at" => ['required', 'date'],
            "bill.paid_at" => [
                'required_if:bill.bill_type,Bill,Debit Note', 
                function($attribute, $value, $fail) {
                    if(!is_null($value)) {
                        if(!strtotime($value)) {
                            $fail('The paid_at must be valid date time.');
                        }
                    }
                }
            ],
            "customer.name" => ['required_if:bill.bill_type,Bill'],
            "customer.vat_registration_number" => ['nullable', 'numeric', 'regex:/^3\d{13}3$/'],
            "customer.building_no" => [
                'required_with:customer.vat_registration_number,not null', 
                function($attribute, $value, $fail) {
                    if(!is_null($value)) {
                        if(!is_numeric($value)) {
                            $fail('The building number must be numeric.');
                        }
                        if(strlen($value) !== 4) {
                            $fail('The building number must be 4 digits.');
                        }
                    }
                }],
            "customer.street_name" => ['required_with:customer.vat_registration_number,null'],
            "customer.district" => ['required_with:customer.vat_registration_number,not null'],
            "customer.city" => [
                'required_with:customer.vat_registration_number,not null', 
                function($attribute, $value, $fail) {
                    if(!is_null($value)) {
                        if(!preg_match('/^[a-zA-Z ]+$/', $value)) {
                            $fail('The city must contain only English letters.');
                        }
                    }
                }],
            "customer.postal_code" => [
                'required_with:customer.vat_registration_number,not null', 
                function($attribute, $value, $fail) {
                    if(!is_null($value)) {
                        if(!is_numeric($value)) {
                            $fail('The building number must be numeric.');
                        }
                        if(strlen($value) !== 5) {
                            $fail('The building number must be 5 digits.');
                        }
                    }
                }]
        ];
    }

    public function messages(){
        return [
            "merchant_uuid.required" => "merchant_uuid is required",
            "bill.bill_uuid.required" => "bill_uuid is required",
            "bill.bill_type.required" => "bill_type is required",
            "bill.bill_type.in" => "bill_type must be Bill,Debit Note,Credit Note",
            "bill.BillingRefrence.required_if" => "BillingRefrence is required",
            "bill.number.required" => "number is required",
            "bill.number.numeric" => "number must be numeric",
            "bill.status.required" => "status is required",
            "bill.status.in" => "status must be pending,paid,paid_cash,paid_bank_transfer,paid_machine,refunded",
            "bill.sub_total.required" => "sub_total is required",
            "bill.sub_total.numeric" => "sub_total must be numeric",
            "bill.sub_total.gt" => "sub_total must be greater than 0",
            "bill.tax_value.required_if" => "tax_value is required",
            "bill.tax_value.numeric" => "tax_value must be numeric",
            "bill.tax_value.gt" => "tax_value must be greater than 0",
            "bill.vat.required_if" => "vat is required",
            "bill.vat.numeric" => "vat must be numeric",
            "bill.vat.gt" => "vat must be greater than 0",
            "bill.discount.required_if" => "discount is required",
            "bill.discount.numeric" => "discount must be numeric",
            "bill.items.*.name.required_if" => "name is required",
            "bill.items.*.quantity.required_if" => "quantity is required",
            "bill.items.*.quantity.numeric" => "quantity must be numeric",
            "bill.items.*.quantity.gt" => "quantity must be greater than 0",
            "bill.items.*.price.required_if" => "price is required",
            "bill.items.*.price.numeric" => "price must be numeric",
            "bill.items.*.price.gt" => "price must be greater than 0",
            "bill.created_at.required" => "created_at is required",
            "bill.created_at.date" => "created_at must be date",
            "bill.paid_at.required" => "paid_at is required if bill_type is Bill,Debit Note",
            "bill.paid_at.date" => "paid_at must be date",
            "customer.name.required_if" => "name is required",
            "customer.vat_registration_number.numeric" => "vat_registration_number must be numeric",
            "customer.vat_registration_number.regex" => "vat_registration_number must be 15 digits and start with 3 and end with 3",
            "customer.building_no.required_with" => "building_no is required if vat_registration_number is not null",
            "customer.building_no.numeric" => "building_no must be numeric",
            "customer.building_no.digits" => "building_no must be 4 digits",
            "customer.street_name.required_with" => "street_name is required if vat_registration_number is not null",
            "customer.district.required_with" => "district is required if vat_registration_number is not null",
            "customer.city.required_with" => "city is required if vat_registration_number is not null",
            "customer.city.regex" => "city must be english letters",
            "customer.postal_code.required_with" => "postal_code is required if vat_registration_number is not null",
            "customer.postal_code.numeric" => "postal_code must be numeric",
            "customer.postal_code.digits" => "postal_code must be 5 digits",
        ];
    }
}
