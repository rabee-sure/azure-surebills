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
        'mastercard' => [
            'merchant_id' => env('MASTERCARD_MERCHANT_ID'),
            'base_url'    => env('MASTERCARD_BASE_URL'),
        ],
        'mastercard_iframe' => [
            'merchant_id'           => env('MASTERCARD_MERCHANT_ID'),
            'checkout_script'       => env('MASTERCARD_CHECKOUT_SCRIPT'),
            'session_script'        => env('MASTERCARD_SESSION_SCRIPT'),
            'api_base_url'          => env('MASTERCARD_API_BASE_URL'),
            'operator_username'     => env('MASTERCARD_OPERATOR_USERNAME'),
            'operator_password'     => env('MASTERCARD_OPERATOR_PASSWORD'),
            'X-Notification-Secret' => env('MASTERCARD_NOTIFICATION_SECRET'),
        ],
        'mastercard_applepay' => [
            'merchant_id'          => env('MASTERCARD_APPLEPAY_MASTERCARD_MERCHANT_ID'),
            'applepay_merchant_id' => env('MASTERCARD_APPLEPAY_MERCHANT_ID'),
            'domain'               => env('MASTERCARD_APPLEPAY_DOMAIN'),
            'passwd'               => env('MASTERCARD_APPLEPAY_KEY_PASSWD'),
            'api_base_url'         => env('MASTERCARD_APPLEPAY_COMPLETE_PAYMENT'),
            'operator_username'    => env('MASTERCARD_OPERATOR_USERNAME'),
            'operator_password'    => env('MASTERCARD_OPERATOR_PASSWORD'),
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
        'mastercard_applepay' => \App\Payment\Drivers\MasterCardApplePay\MasterCardApplePay::class,
    ]
];
