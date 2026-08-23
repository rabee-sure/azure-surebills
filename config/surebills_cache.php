<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Master Kill Switch
    |--------------------------------------------------------------------------
    |
    | When false, SureBillsCache remember* paths bypass Redis and execute the
    | callback only. Domain flags below are also ignored until this is true.
    |
    */

    'enabled' => env('SUREBILLS_CACHE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Fail Open
    |--------------------------------------------------------------------------
    |
    | On cache store exceptions, read paths fall back to the callback / DB
    | path and write helpers return false/0 without throwing.
    |
    */

    'fail_open' => true,

    /*
    |--------------------------------------------------------------------------
    | Hit/Miss Log Sample Rate (PR-04 / P0-05)
    |--------------------------------------------------------------------------
    |
    | <= 0: structured cache logging OFF (including errors).
    | > 0: logging ON — sample successful hit/miss; log errors at 100%.
    | Uses existing SUREBILLS_CACHE_LOG_SAMPLE_RATE only (no new env vars).
    |
    */

    'log_sample_rate' => env('SUREBILLS_CACHE_LOG_SAMPLE_RATE', 0.05),

    /*
    |--------------------------------------------------------------------------
    | Domain Feature Flags
    |--------------------------------------------------------------------------
    |
    | All domains default false. Enable one domain at a time after soak.
    | Flag names must stay identical to the Admin config schema (PR-02).
    |
    */

    'domains' => [
        'basic_settings' => env('CACHE_DOMAIN_BASIC_SETTINGS', false),
        'banks' => env('CACHE_DOMAIN_BANKS', false),
        'merchant_settings' => env('CACHE_DOMAIN_MERCHANT_SETTINGS', false),
        'channels' => env('CACHE_DOMAIN_CHANNELS', false),
        'merchant_profile' => env('CACHE_DOMAIN_MERCHANT_PROFILE', false),
        'customers' => env('CACHE_DOMAIN_CUSTOMERS', false),
        'pending_bills' => env('CACHE_DOMAIN_PENDING_BILLS', false),
        'coupons' => env('CACHE_DOMAIN_COUPONS', false),
        'merchant_home' => env('CACHE_DOMAIN_MERCHANT_HOME', false),
        'admin_badges' => env('CACHE_DOMAIN_ADMIN_BADGES', false),
        'admin_home' => env('CACHE_DOMAIN_ADMIN_HOME', false),
        'merchant_charts' => env('CACHE_DOMAIN_MERCHANT_CHARTS', false),
        'merchant_transactions' => env('CACHE_DOMAIN_MERCHANT_TRANSACTIONS', false),
        'transfers' => env('CACHE_DOMAIN_TRANSFERS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | TTL Constants (seconds)
    |--------------------------------------------------------------------------
    |
    | Sourced from approved Architecture / Implementation Spec. Yearly charts
    | start at 24h (not 1 year) until Phase 4 promotion criteria are met.
    |
    */

    'ttl' => [
        'balance' => 60,
        'last_bills' => 600,
        'pending_bills' => 43200,
        'coupons' => 86400,
        'badges' => 60,
        'admin_home' => 120,
        'charts_week' => 604800,
        'charts_month' => 2592000,
        'charts_year' => 86400,
        'transfers' => 86400,
        'transactions' => 60,
    ],

];
