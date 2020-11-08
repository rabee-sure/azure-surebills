<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | This value determines which of the following gateway to use.
    | You can switch to a different driver at runtime.
    |
    */
    'default' => 'hyperpay_iframe',

    /*
    |--------------------------------------------------------------------------
    | List of Drivers
    |--------------------------------------------------------------------------
    |
    | These are the list of drivers to use for this package.
    | You can change the name. Then you'll have to change
    | it in the map array too.
    |
    */
    'drivers' => [
        'hyperpay' => [
            /* normal api */
            'access_token'     => env('HYPERPAY_TOKEN', 'OGFjOWE0Y2I2Y2ZiMjdkYzAxNmQzNDFkOWJhZTQ0YzF8dHhRazdHZVpSWg=='),
            'entity_id'        => env('HYPERPAY_ENTITY_ID', '8ac9a4cb6cfb27dc016d3422a55644f7'),
            'api_purchase_url' => env('HYPERPAY_API_URL', 'https://test.oppwa.com/v1/payments'),
        ],
        'hyperpay_iframe' => [
            /* normal api */
            'access_token' => env('HYPERPAY_TOKEN', 'OGFjOWE0Y2I2Y2ZiMjdkYzAxNmQzNDFkOWJhZTQ0YzF8dHhRazdHZVpSWg=='),
            'entity_id'    => env('HYPERPAY_ENTITY_ID', '8ac9a4cb6cfb27dc016d3422a55644f7'),
            'api_base_url' => env('HYPERPAY_BASE_URL', 'https://test.oppwa.com/v1'),
        ],
        'hyperpay_applepay' => [
            /* normal api */
            'access_token' => env('HYPERPAY_APPLEPAY_TOKEN', 'OGFjOWE0Y2I2Y2ZiMjdkYzAxNmQzNDFkOWJhZTQ0YzF8dHhRazdHZVpSWg=='),
            'entity_id'    => env('HYPERPAY_APPLEPAY_ENTITY_ID', '8ac9a4cb6cfb27dc016d3422a55644f7'),
            'api_base_url' => env('HYPERPAY_APPLEPAY_BASE_URL', 'https://test.oppwa.com/v1'),
        ],
        'mastercard_iframe' => [
            /* normal api */
            'merchant_id' => env('MASTERCARD_MERCHANT_ID','TEST3000000330'),
            'checkout_script' => env('MASTERCARD_CHECKOUT_SCRIPT','https://test-gateway.mastercard.com/checkout/version/58/checkout.js'),
            'session_script' => env('MASTERCARD_SESSION_SCRIPT','https://test-gateway.mastercard.com/form/version/58/merchant/TEST3000000330/session.js'),
            'api_base_url' => env('MASTERCARD_API_BASE_URL','https://test-gateway.mastercard.com/api/rest/version/58/merchant/TEST3000000330'),
            'operator_username' => env('MASTERCARD_OPERATOR_USERNAME','merchant.TEST3000000330'),
            'operator_password' => env('MASTERCARD_OPERATOR_PASSWORD','d4da5ee0b3612ec2b7a51c058a8c7f09'),
        ],
        'mastercard_applepay' => [
            /* normal api */
            'merchant_id' => env('MASTERCARD_MERCHANT_ID','TEST3000000330'),
            'checkout_script' => env('MASTERCARD_CHECKOUT_SCRIPT','https://test-gateway.mastercard.com/checkout/version/58/checkout.js'),
            'session_script' => env('MASTERCARD_SESSION_SCRIPT','https://test-gateway.mastercard.com/form/version/58/merchant/TEST3000000330/session.js'),
            'api_base_url' => env('MASTERCARD_API_BASE_URL','https://test-gateway.mastercard.com/api/rest/version/58/merchant/TEST3000000330'),
            'operator_username' => env('MASTERCARD_OPERATOR_USERNAME','merchant.TEST3000000330'),
            'operator_password' => env('MASTERCARD_OPERATOR_PASSWORD','d4da5ee0b3612ec2b7a51c058a8c7f09'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Maps
    |--------------------------------------------------------------------------
    |
    | This is the array of Classes that maps to Drivers above.
    | You can create your own driver if you like and add the
    | config in the drivers array and the class to use for
    | here with the same name. You will have to extend
    | App\Payment\Abstracts\Driver in your driver.
    |
    */
    'map' => [
        'hyperpay' => \App\Payment\Drivers\HyperPay::class,
        'hyperpay_iframe' => \App\Payment\Drivers\HyperPayFrame::class,
        'hyperpay_applepay' => \App\Payment\Drivers\HyperPayApplePay::class,
        'mastercard_iframe' => \App\Payment\Drivers\MasterCardFrame::class,
        'mastercard_applepay' => \App\Payment\Drivers\MasterCardApplePay::class,
    ]
];
