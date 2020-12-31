<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\BillCreated' => [
            // 'App\Listeners\SendBillPayEmail',
            'App\Listeners\SendBillPaySms',
        ],
        'App\Events\UserCreated' => [
            'App\Listeners\CreateSettingsForUser',
        ],             
        'App\Events\BillStatusUpdated' => [
            'App\Listeners\CallbackApplication',
        ],        
        'App\Events\BillPaid' => [
            'App\Listeners\CalculatePayment',
            // 'App\Listeners\SendBillPaidEmailToOwner',
            // 'App\Listeners\SendBillPaidEmailToCustomer',
            // 'App\Listeners\SendBillPaidWebhook',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
