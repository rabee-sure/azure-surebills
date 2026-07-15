<?php

$ociEnabled = (bool) config('oci.enabled', filter_var(env('OCI_ENABLED', false), FILTER_VALIDATE_BOOLEAN));
$ociPublicDiskEnabled = (bool) config('oci.public_disk_enabled', filter_var(env('OCI_PUBLIC_DISK_ENABLED', true), FILTER_VALIDATE_BOOLEAN));
$useOciPublicDisk = $ociEnabled && $ociPublicDiskEnabled;

$ociS3Disk = [
    'driver' => 's3',
    'key' => config('oci.access_key', env('OCI_ACCESS_KEY')),
    'secret' => config('oci.secret_key', env('OCI_SECRET_KEY')),
    'region' => config('oci.region', env('OCI_REGION')),
    'bucket' => config('oci.bucket', env('OCI_BUCKET')),
    'endpoint' => config('oci.endpoint', env('OCI_ENDPOINT')),
    'use_path_style_endpoint' => config('oci.use_path_style_endpoint', filter_var(env('OCI_USE_PATH_STYLE_ENDPOINT', true), FILTER_VALIDATE_BOOLEAN)),
    'url' => config('oci.url', env('OCI_URL')),
    'root' => (string) config('oci.bucket_prefix', env('OCI_BUCKET_PREFIX', '')),
    'visibility' => config('oci.visibility', env('OCI_VISIBILITY', 'private')),
    'throw' => false,
];

$ociPrivateS3Disk = array_merge($ociS3Disk, [
    'bucket' => config('oci.private_bucket') ?: config('oci.bucket', env('OCI_BUCKET')),
    'visibility' => 'private',
]);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'cloud' => env('FILESYSTEM_CLOUD', $ociEnabled ? 'oci' : 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | When OCI_ENABLED=true and OCI_PUBLIC_DISK_ENABLED=true (config oci.public_disk_enabled):
    |   - "oci"              – direct OCI Object Storage (S3-compatible)
    |   - "public" / "private" – fallback disks (OCI primary, local fallback)
    |   - "public-local"     – original local public disk (used as fallback)
    |
    | When OCI is disabled or public_disk_enabled is false:
    |   - "public" / "private" – unchanged local disks (production default)
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        /*
         * Always-local public disk. Used as the read fallback when OCI is enabled.
         */
        'public-local' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/media',
            'visibility' => 'public',
        ],

        /*
         * OCI Object Storage (S3-compatible API).
         */
        'oci' => $ociS3Disk,

        'oci-private' => $ociPrivateS3Disk,

        'public' => $useOciPublicDisk ? [
            'driver' => 'fallback',
            'primary' => 'oci',
            'fallback' => 'public-local',
        ] : [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/media',
            'visibility' => 'public',
        ],

        'private' => $useOciPublicDisk ? [
            'driver' => 'fallback',
            'primary' => config('oci.private_bucket') ? 'oci-private' : 'oci',
            'fallback' => 'public-local',
        ] : [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'visibility' => 'private',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
        ],

        'oci' => [
            'driver'                  => 's3',
            'key'                     => env('OCI_ACCESS_KEY_ID'),
            'secret'                  => env('OCI_SECRET_ACCESS_KEY'),
            'region'                  => env('OCI_REGION'),
            'bucket'                  => env('OCI_BUCKET'),
            'endpoint'                => env('OCI_ENDPOINT'),
            'bucket_endpoint'         => false,
            'use_path_style_endpoint' => true,
            'throw'                   => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
