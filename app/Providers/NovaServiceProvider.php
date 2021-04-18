<?php

namespace App\Providers;

use Anaseqal\NovaSidebarIcons\NovaSidebarIcons;
use App\Nova\Metrics\BillsPerDay;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\TotalCommissions;
use App\Nova\Metrics\TotalDue;
use App\Nova\Metrics\TotalIncome;
use App\Nova\Metrics\TotalPaid;
use App\Nova\Metrics\TotalVatOnCommissions;
use Bakerkretzmar\NovaSettingsTool\SettingsTool;
use ChrisWare\NovaBreadcrumbs\NovaBreadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Sure\HomeAnalytics\HomeAnalytics;
use Sure\Settlements\Settlements;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::userTimezone(function (Request $request) {
            return 'Asia/Riyadh';
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, explode(',', env('NOVA_ALLOWED_ADMINS')));
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [     
            // new NewUsers,
            (new HomeAnalytics)->width('full'),

            (new TotalIncome)->width('1/5'),
            (new TotalCommissions)->width('1/5'),
            (new TotalVatOnCommissions)->width('1/5'),
            (new TotalPaid)->width('1/5'),
            (new TotalDue)->width('1/5'),
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            new Settlements,
            new NovaSidebarIcons,
            new NovaBreadcrumbs,
            new SettingsTool,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
