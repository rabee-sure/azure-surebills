<?php

return [
    'enabled' => env('MERCHANT_OTP_ENABLED', false), // true or false
    'environment' => env('APP_ENV', 'MERCHANT_OTP_ENVIRONMENT', 'testing'), // testing, production
    'channel' => env('MERCHANT_OTP_CHANNEL', 'email'), // email, sms, or both
    'expiration_minutes' => env('MERCHANT_OTP_EXPIRATION_MINUTES', 5), // 5 minutes
    'throttle_attempts' => env('MERCHANT_OTP_THROTTLE_ATTEMPTS', 3), // 3 attempts
    'throttle_time' => env('MERCHANT_OTP_THROTTLE_TIME', 5), // 5 minutes
];