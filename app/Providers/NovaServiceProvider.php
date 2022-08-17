<?php

namespace App\Providers;

use Anaseqal\NovaSidebarIcons\NovaSidebarIcons;
use App\Models\Admin;
use App\Models\Role;
use App\Nova\Metrics\BillsPerDay;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\TotalCommissions;
use App\Nova\Metrics\TotalDue;
use App\Nova\Metrics\TotalIncome;
use App\Nova\Metrics\TotalPaid;
use App\Nova\Metrics\TotalVatOnCommissions;
use App\Observers\AdminObserver;
use App\Observers\RoleObserver;
use Bakerkretzmar\NovaSettingsTool\SettingsTool;
use Beyondcode\Reports\Reports;
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

        Nova::serving(function () {
            Role::observe(RoleObserver::class);
            Admin::observe(AdminObserver::class);
        });

        Nova::userTimezone(function (Request $request) {
            return 'Asia/Riyadh';
        });

        Nova::style('admin', asset('css/nova.css'));
        Nova::style('admin-font-awesome', asset('font-awesome/css/font-awesome.css'));
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
            return $user->is_active;
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        $cards = [];
        $user = auth()->guard('admins')->user();

        if($user->canany(['show merchants', 'show bills', 'show transfers']))
        {
            array_push($cards, (new HomeAnalytics)->width('full'));
        }

        if($user->can('show bills'))
        {
            array_push($cards, (new TotalIncome)->width('1/5'));
            array_push($cards, (new TotalCommissions)->width('1/5'));
            array_push($cards, (new TotalVatOnCommissions)->width('1/5'));
            array_push($cards, (new TotalPaid)->width('1/5'));
            array_push($cards, (new TotalDue)->width('1/5'));
        }

        return $cards;
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
        $tools = [
            new Reports,
            new Settlements,
            new NovaSidebarIcons,
            new NovaBreadcrumbs,

        ];

        if(request()->user()->hasRole('super admin'))
        {
            $tools[] = new SettingsTool;
        }

        return $tools;
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


    /**
     * Configure the Nova authorization services.
     *
     * @return void
     */
    protected function authorization()
    {
        $this->gate();

        Nova::auth(function ($request) {
            return Gate::check('viewNova', function() use ($request){
                return $request->user()->is_active;
            });
        });
    }
}
