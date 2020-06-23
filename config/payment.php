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
    'default' => 'hyperpay',

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
            'access_token' => 'OGFjOWE0Y2I2Y2ZiMjdkYzAxNmQzNDFkOWJhZTQ0YzF8dHhRazdHZVpSWg==',
            'entity_id' => '8ac9a4cb6cfb27dc016d3422a55644f7',
            'api_purchase_url' => 'https://test.oppwa.com/v1/payments',
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
    ]
];