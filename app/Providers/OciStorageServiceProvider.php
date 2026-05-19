<?php

namespace App\Providers;

use App\Filesystem\FallbackFilesystemAdapter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class OciStorageServiceProvider extends ServiceProvider
{
    /**
     * Register the fallback driver in register() so it exists before any
     * other provider boot() calls Storage::disk('public').
     */
    public function register(): void
    {
        Storage::extend('fallback', function ($app, array $config) {
            $primary = Storage::disk($config['primary']);
            $fallback = Storage::disk($config['fallback']);

            if (! $primary instanceof FilesystemAdapter || ! $fallback instanceof FilesystemAdapter) {
                throw new \InvalidArgumentException(
                    'Fallback disk requires primary and fallback to be FilesystemAdapter instances.'
                );
            }

            return new FallbackFilesystemAdapter($primary, $fallback);
        });
    }

    public function boot(): void
    {
        //
    }
}
