<?php

namespace App\Providers;

use App\Models\Application;
use App\Observers\ApplicationObserver;
use App\Observers\TransferObserver;
use App\Models\Transfer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        Application::observe(ApplicationObserver::class);
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

    }
}
