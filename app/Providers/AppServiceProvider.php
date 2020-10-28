<?php

namespace App\Providers;

use App\Observers\TransferObserver;
use App\Transfer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Spatie\NovaTranslatable\Translatable::defaultLocales(['en', 'ar']);

        Transfer::observe(TransferObserver::class);
        Schema::defaultStringLength(191);
    }
}
