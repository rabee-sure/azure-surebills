<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OCI Object Storage Feature Toggle
    |--------------------------------------------------------------------------
    |
    | When enabled, the "public" and "private" filesystem disks transparently
    | use OCI Object Storage (S3-compatible API) for writes while reads fall
    | back to local storage for files not yet migrated.
    |
    */

    'enabled' => filter_var(env('OCI_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | S3-Compatible API Credentials
    |--------------------------------------------------------------------------
    |
    | Generate Customer Secret Keys in OCI: Identity > Users > Customer Secret Keys
    | Endpoint format: https://{namespace}.compat.objectstorage.{region}.oraclecloud.com
    |
    */

    'access_key' => env('OCI_ACCESS_KEY'),
    'secret_key' => env('OCI_SECRET_KEY'),
    'region' => env('OCI_REGION'),
    'bucket' => env('OCI_BUCKET'),
    'endpoint' => env('OCI_ENDPOINT'),
    'use_path_style_endpoint' => filter_var(env('OCI_USE_PATH_STYLE_ENDPOINT', true), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Optional: Separate Private Bucket
    |--------------------------------------------------------------------------
    |
    | When set, the "private" disk uses this bucket instead of OCI_BUCKET.
    |
    */

    'private_bucket' => env('OCI_PRIVATE_BUCKET'),

    /*
    |--------------------------------------------------------------------------
    | Object Visibility & Signed URLs
    |--------------------------------------------------------------------------
    |
    | "public"  – objects are readable via the bucket URL (or OCI_URL).
    | "private" – objects require temporary signed URLs.
    |
    */

    'visibility' => env('OCI_VISIBILITY', 'private'),
    'signed_url_expiration' => (int) env('OCI_SIGNED_URL_EXPIRATION', 30),

    /*
    |--------------------------------------------------------------------------
    | Public URL Override
    |--------------------------------------------------------------------------
    |
    | Optional CDN or custom domain for public objects. When empty, Laravel
    | builds the URL from the S3 endpoint and bucket configuration.
    |
    */

    'url' => env('OCI_URL'),

];
