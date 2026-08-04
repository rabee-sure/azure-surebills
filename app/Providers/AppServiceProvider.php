<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\AutoTransfer;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Bill;
use App\Models\Media;
use App\Models\Channel;
use App\Models\Bank;
use App\Models\TaxInvoiceRequest;
use App\Observers\ApplicationObserver;
use App\Observers\TransactionObserver;
use App\Observers\TransferObserver;
use App\Observers\UserObserver;
use App\Observers\BillObserver;
use App\Observers\MediaObserver;
use App\Observers\ChannelObserver;
use App\Observers\BankObserver;
use App\Observers\TaxInvoiceRequestObserver;
use App\Policies\AutoTransferPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\Transaction;

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

        // PR-01: Nova boot path is optional (NOVA_ENABLED). Default false — Nova retired (ADR-014).
        if ($this->isNovaBootEnabled()) {
            $this->app->register(NovaServiceProvider::class);
            $this->registerNovaBindings();
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            ensure_merchant_logo_directory();
            ensure_bills_background_directory();
        }

        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        // Model::preventLazyLoading(! app()->isProduction());

        // PR-01: Gate NovaTranslatable — do not hard-require Nova field package on merchant boot.
        if ($this->isNovaBootEnabled()
            && class_exists(\Spatie\NovaTranslatable\Translatable::class)
        ) {
            \Spatie\NovaTranslatable\Translatable::defaultLocales(['en', 'ar']);
        }

        AutoTransfer::observe(AutoTransferPolicy::class);
        Transfer::observe(TransferObserver::class);
        Transaction::observe(TransactionObserver::class);
        Application::observe(ApplicationObserver::class);
        User::observe(UserObserver::class);
        Bill::observe(BillObserver::class);
        Media::observe(MediaObserver::class);
        Channel::observe(ChannelObserver::class);
        Bank::observe(BankObserver::class);
        TaxInvoiceRequest::observe(TaxInvoiceRequestObserver::class);
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $buffer = explode('.', $attribute);
                            $attributeField = array_pop($buffer);
                            $relationPath = implode('.', $buffer);
                            $query->orWhereHas($relationPath, function (Builder $query) use ($attributeField, $searchTerm) {
                                $query->where($attributeField, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });
            return $this;
        });


        /**
         * Paginate a standard Laravel Collection.
         *
         * @param int $perPage
         * @param int $total
         * @param int $page
         * @param string $pageName
         * @return array
         */
        Collection::macro('paginate', function($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

    }

    /**
     * Whether the optional Nova boot path is enabled.
     *
     * Default false: merchant boot must not require Nova runtime classes.
     * Set NOVA_ENABLED=true only for temporary rollback / residual Nova access.
     */
    protected function isNovaBootEnabled(): bool
    {
        return filter_var(env('NOVA_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Register Nova Login/Reset binds and Nova view overrides when Nova boot is enabled.
     */
    protected function registerNovaBindings(): void
    {
        if (! class_exists(\Laravel\Nova\Http\Controllers\LoginController::class)
            || ! class_exists(\Laravel\Nova\Http\Controllers\ResetPasswordController::class)
        ) {
            return;
        }

        $this->app->bind(
            \Laravel\Nova\Http\Controllers\LoginController::class,
            \App\Http\Controllers\Nova\NovaLoginController::class
        );
        $this->app->bind(
            \Laravel\Nova\Http\Controllers\ResetPasswordController::class,
            \App\Http\Controllers\Nova\NovaResetPasswordController::class
        );

        // Custom Nova view overrides (published to resources/views/vendor/nova)
        $novaViews = resource_path('views/vendor/nova');
        if (is_dir($novaViews)) {
            $this->loadViewsFrom($novaViews, 'nova');
        }
    }
}
