<?php

return [
    'enabled' => env('MERCHANT_OTP_ENABLED', false), // true or false
    'channel' => env('MERCHANT_OTP_CHANNEL', 'email'), // email, sms, or both
    'expiration_minutes' => env('MERCHANT_OTP_EXPIRATION_MINUTES', 5), // 5 minutes
];