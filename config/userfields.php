<?php

return [
    'general_data' => [
        'name', 
        'logo', 
        'email', 
        'password', 
        'mobile', 
        'mobile_verified', 
        'gender',
        'able_refund',
        'vat_inclusive',
        'auto_trnasfer',
    ],
    'bank_information' => [
        'bank_id',
        'iban_number',
        'beneficiary_name',
    ],
    'priceing' => [
        'mada_fixed',
        'mada_percentage',
        'credit_cards_fixed',
        'credit_cards_percentage',
    ],
    'business_information' => [
        'license_type',
        'commercial_registry_expiry_date',
        'vat_registration_number',
        'business_name_en',
        'business_name_ar',
        'sector',
        'business_address',
        'business_mobile',
        'website',
    ],
    'documents' => [
        'disable_business_documents',
        'disable_bank_documents',
    ],
]; 