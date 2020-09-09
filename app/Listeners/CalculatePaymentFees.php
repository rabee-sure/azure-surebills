<?php

namespace App\Listeners;

use App\Events\BillPaid;
use App\Mail\SendBillPaidToCustomer;
use App\PaymentLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CalculatePaymentFees
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BillPaid  $event
     * @return void
     */
    public function handle(BillPaid $event)
    {
        $bill = $event->bill;
        $user = $event->bill->user;
        $log = $event->bill->success_payment;

        if($log){
            $bill->settled = false;
            $bill->pricing_fees_details = $user->price_percentage.'%,'. $user->price_fixed;
            $bill->payment_fees = $bill->sub_total * ($user->price_percentage / 100) + $user->price_fixed;
            $bill->save();
        }

    }
}
