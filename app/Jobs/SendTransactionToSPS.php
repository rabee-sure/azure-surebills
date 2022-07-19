<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\PaymentLog;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTransactionToSPS
{
    use Dispatchable, SerializesModels;

    protected $bill;

    protected $log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bill $bill, PaymentLog $payment_log)
    {
        $this->bill = $bill;
        $this->log = $payment_log;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $logResualt = json_decode($this->log->results);

        //Prepare Api data
        // $data['TrxNumber'] = $transaction->id; 
        // $data['TrxType'] = $transaction->type; 
        // $data['TrxDate'] = $transaction->created_at; 
        $data['MaskedCard'] = $this->log->card_number; 
        // $data['Amount'] = $logResualt['amount']; 
        // $data['NetAmount'] = $transaction->amount; 
        // $data['Vat'] = $transaction->; 
        // $data['VatPercentage'] = $transaction->; 
        $data['AuthCode'] = $transaction->; 
        $data['CardType'] = $this->log->brand; 
        $data['ReconciliationDate'] = $transaction->; 
        $data['ReconciliationNo'] = $transaction->; 
        $data['TrxCertificate'] = $transaction->; 
        $data['Fees'] = $transaction->; 
        $data['MerchantName'] = $transaction->; 
        $data['MerchantId'] = $transaction->; 

        //Send transaction data to sps api
        $link = config('sps.base_url').'/'.config('sps.routes.Save_transaction');
        $client = new Client;
        $response = $client->request('POST', $link, ['body' => json_encode($data)]);

        //Log Api faild response

        return $response;
    }
}
