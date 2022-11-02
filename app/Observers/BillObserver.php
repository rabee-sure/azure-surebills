<?php

namespace App\Observers;

use App\Models\Bill;

class BillObserver
{
    /**
     * Handle the Bill "created" event.
     *
     * @param  \App\Models\Bill  $bill
     * @return void
     */
    public function created(Bill $bill)
    {
        //
    }

    /**
     * Handle the Bill "updated" event.
     *
     * @param  \App\Models\Bill  $bill
     * @return void
     */
    public function updating(Bill $bill)
    {
        if($bill->isDirty('status')){
            switch ($bill->status) {
                case 'paid':
                    $bill->payment_way = 'online';
                    break;
            
                case 'paid_cash':
                    $bill->payment_way = 'cash';
                    break;

                case 'paid_bank_transfer':
                    $bill->payment_way = 'bank_transfer';
                    break;
                
                case 'paid_machine':
                    $bill->payment_way = 'payment_machine';
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    }

    /**
     * Handle the Bill "deleted" event.
     *
     * @param  \App\Models\Bill  $bill
     * @return void
     */
    public function deleted(Bill $bill)
    {
        //
    }

    /**
     * Handle the Bill "restored" event.
     *
     * @param  \App\Models\Bill  $bill
     * @return void
     */
    public function restored(Bill $bill)
    {
        //
    }

    /**
     * Handle the Bill "force deleted" event.
     *
     * @param  \App\Models\Bill  $bill
     * @return void
     */
    public function forceDeleted(Bill $bill)
    {
        //
    }
}
