<?php

return [
    'enviroment' => env('ZATCA_ENVIROMENT'),
    'server' => env('ZATCA_SERVER'),
    'username' => env('ZATCA_USERNAME'),
    'password' => env('ZATCA_PASSWORD'),
    'tax_value' => 15,
    'rate_limiter_per_day' => env('ZATCA_RATE_LIMITER_PER_DAY', 0),
    'rate_limiter_emails' => env('ZATCA_RATE_LIMITER_EMAILS', []),
    'tries' => env('ZATCA_TRIES'),
    'freelancer_max_sales_volume' => env('FREELANCER_MAX_SALES_VOLUME'),
    'zatca_api_key' => env('ZATCA_API_KEY'),
];