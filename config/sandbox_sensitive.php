<?php

return [
    'bills' => [
        ['column' => 'business_mobile', 'example' => '574102360'],
        ['column' => 'business_crn', 'example' => '1234567890'],
        ['column' => 'business_postal_code', 'example' => '12345'],
        ['column' => 'business_additional_no', 'example' => '12362'],
        ['column' => 'business_other_buyer_id', 'example' => '186764131886'],
        ['column' => 'business_vat_registration_number', 'example' => '399999999900003'],
        ['column' => 'customer_name', 'example' => 'Ali Hassan'],
        ['column' => 'customer_mobile', 'example' => '598989877'],
        ['column' => 'customer_email', 'example' => 'customer@example.com'],
        ['column' => 'customer_crn', 'example' => '1234567890'],
        ['column' => 'customer_postal_code', 'example' => '12345'],
        ['column' => 'customer_additional_no', 'example' => '12362'],
        ['column' => 'customer_other_buyer_id', 'example' => '186764131886'],
        ['column' => 'customer_vat_registration_number', 'example' => '399999999900003'],
    ],

    'customers' => [
        ['column' => 'name', 'example' => 'Ali Hassan'],
        ['column' => 'email', 'example' => 'ali@example.com'],
        ['column' => 'postal_code', 'example' => '12345'],
        ['column' => 'additional_no', 'example' => '12362'],
        ['column' => 'other_buyer_id', 'example' => '186764131886'],
        ['column' => 'vat_registration_number', 'example' => '399999999900003'],
    ],


    'due_amount_auto_transfer_report' => [
        ['column' => 'merchant_iban', 'example' => 'SA0380000000608010167519'],
        ['column' => 'bank', 'example' => 'bank'],
    ],

    'merchant_auto_transfer_report' => [
        ['column' => 'bill_business_name', 'example' => 'Vielka Chen'],
    ],

    'payment_logs' => [
        ['column' => 'card_number', 'example' => '4111111111111111'],
    ],

    'pos_orders' => [
        ['column' => 'postal_code', 'example' => '12345'],
        ['column' => 'additional_no', 'example' => '12362'],
        ['column' => 'other_buyer_id', 'example' => '186764131886'],
        ['column' => 'vat_registration_number', 'example' => '399999999900003'],
    ],



    'settlements' => [
        ['column' => 'iban_number', 'example' => 'SA0380000000608010167519'],
        ['column' => 'beneficiary_name', 'example' => 'bank'],
    ],

    'transactions' => [
        ['column' => 'card', 'example' => '4111111111111111'],
    ],

    'users' => [
        ['column' => 'mobile', 'example' => '574102360'],
        ['column' => 'national_id', 'example' => '1234567890'],
        ['column' => 'vat_registration_number', 'example' => '399999999900003'],
        ['column' => 'beneficiary_name', 'example' => 'bank'],
    ],
];
