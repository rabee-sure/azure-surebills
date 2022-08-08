<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

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
            'App\Listeners\SPSSendMerchantData',
        ],
        WebhookCallSucceededEvent::class => [
            'App\Listeners\Webhook\SaveWebhookSucceededLog',
        ],
        WebhookCallFailedEvent::class => [
            'App\Listeners\Webhook\SaveWebhookFailedLog',
        ],
        'App\Events\BillCreated' => [
            'App\Listeners\SendBillPayEmail',
            'App\Listeners\SendBillPaySms',
        ],
        'App\Events\UserUpdated' => [
            'App\Listeners\SPSSendMerchantData',
        ],
        'App\Events\PosSendBill' => [
            'App\Listeners\PosSendBillPayEmail',
        ],
        'App\Events\UserCreated' => [
            'App\Listeners\CreateSettingsForUser',
        ],
        'App\Events\BillStatusUpdated' => [
            'App\Listeners\CallbackApplication',
            'App\Listeners\CalculateOfflinePayment',
        ],
        'App\Events\PosBillPaid' => [
            'App\Listeners\CalculateOfflinePosPayment',
        ],
        'App\Events\BillPaid' => [
            'App\Listeners\CalculatePayment',
            'App\Listeners\SendBillPaidEmailToOwner',
            'App\Listeners\SendBillPaidEmailToCustomer',
        ],
        'App\Events\BillRefunded' => [
            'App\Listeners\CalculateRefundedPayment',
        ],
        'App\Events\BillPartialRefunded' => [
            'App\Listeners\CalculatePartialRefundedPayment',
        ],
        'App\Events\BillOfflineRefunded' => [
            'App\Listeners\CalculateOfflineRefundedPayment',
        ],
        'App\Events\BillOfflinePartialRefunded' => [
            'App\Listeners\CalculateOfflinePartialRefundedPayment',
        ],
        'App\Events\TransferCreated' => [
            'App\Listeners\SendMailTransferMailToCustomer',
        ],
        'App\Events\UserVerifiedChanged' => [
            'App\Listeners\SendSubAccountStatusWebhook',
        ],
        'App\Events\TransferCompleted' => [
            'App\Listeners\SendSubAccountSettledWebhook',
        ],
        'App\Events\TransferFileGenerated' => [
            'App\Listeners\SendRequestTransferFile',
        ],
        'App\Events\GenerateReport' => [
            'App\Listeners\SendReportFile',
        ],
        'App\Events\GenerateBillReport' => [
            'App\Listeners\SendBillReportFile',
        ],
        'App\Events\OrderCreated' => [
            'App\Listeners\CreateOrderBill',
        ],
        'App\Events\AddActionLogEvent'::class => [
            'App\Listeners\StoreActionLog',
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
