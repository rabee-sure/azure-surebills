<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SPSSendTransaction
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
     * @param  \App\Events\TransactionCreated  $event
     * @return void
     */
    public function handle(TransactionCreated $event)
    {
        $transaction = $event->transaction;

        $data['TrxNumber'] = $transaction->id; 
        $data['TrxType'] = $transaction->type; 
        $data['TrxDate'] = $transaction->created_at; 
        $data['MaskedCard'] = $transaction->; 
        $data['Amount'] = $transaction->amount; 
        $data['NetAmount'] = $transaction->amount; 
        $data['Vat'] = $transaction->; 
        $data['VatPercentage'] = $transaction->; 
        $data['AuthCode'] = $transaction->; 
        $data['CardType'] = $transaction->; 
        $data['ReconciliationDate'] = $transaction->; 
        $data['ReconciliationNo'] = $transaction->; 
        $data['TrxCertificate'] = $transaction->; 
        $data['Fees'] = $transaction->; 
        $data['MerchantName'] = $transaction->; 
        $data['MerchantId'] = $transaction->; 
    }
}
