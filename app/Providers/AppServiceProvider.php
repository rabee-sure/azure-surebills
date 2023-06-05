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
        // Model::preventLazyLoading(! app()->isProduction());

        \Spatie\NovaTranslatable\Translatable::defaultLocales(['en', 'ar']);

        AutoTransfer::observe(AutoTransferPolicy::class);
        Transfer::observe(TransferObserver::class);
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
}
