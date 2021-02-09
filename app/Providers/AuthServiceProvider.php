<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Channel' => 'App\Policies\ChannelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Gate::define('viewWebSocketsDashboard', function ($user = null) {
            return in_array($user->email, [
                'faisal@toot.im',
                'eabdelsabour@sure.com.sa',
                'aghanem@sure.com.sa',
                'mjarad@sure.com.sa',
            ]);
        });

    }
}
