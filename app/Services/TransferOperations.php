<?php

namespace App\Services;

use App\Events\TransferCreated;
use App\Models\Transaction;
use App\Models\TransferLog;
use App\Services\TransferService;
use Illuminate\Support\Facades\Http;

class TransferOperations
{
    /**
     * complete Transfars.
     * 
     */
    public function complete($transfers, $status, $user_id, $results=null, $from_sps=false)
    {
        foreach($transfers as $transfer){
            if($transfer->status == 'pending' ){
                $type = $from_sps ? $status.' sps transfer':$status.' transfer';

                $this->changeStatusAndCreateLog($transfer, $status, $type, $user_id, $results);

                $this->createTransferTransaction($transfer);

            $transfer->transactions()
                ->chunkById(1000, function($transactions_ids){
                    Transaction::whereIn('id', $transactions_ids->pluck('id'))->update(['settled' => true]);
                });

                // $transfer->transactions()->update(['settled' => true]);

                event(new TransferCreated($transfer));
            }
        }

    }

    /**
     * cancel Transfars.
     * 
     */
    public function cancel($transfers, $status, $user_id, $results=null, $from_sps=false)
    {
        foreach($transfers as $transfer){
            if($transfer->status == 'pending' ){
                $type = $from_sps ? $status.' sps transfer':$status.' transfer';
                $this->changeStatusAndCreateLog($transfer, $status, $type, $user_id, $results);

                $transfer->transactions()->update(["pending_settled" => false]);
            }
        }
    }

    /**
     * pending Transfars.
     * 
     */
    public function pending($transfers, $status, $user_id, $results=null, $from_sps=false)
    {
        foreach($transfers as $transfer){
            if($transfer->status == 'pending' ){
                $transfer->transactions()->update(['pending_settled' => true]);
            }
        }
    }

    /**
     * send transfers To Sps.
     * 
     */
    public function sendToSps($transfers, $status, $user_id, $results=null, $from_sps=false)
    {
        $body = [];

        foreach($transfers as $transfer){
            if($transfer->status == 'pending' || $transfer->status == 'send_to_sps'){
                $type = $status.' sps transfer';
                $this->changeStatusAndCreateLog($transfer, $status, $type, $user_id, $results );
                $transfer->transactions()->update(["pending_settled" => true]);
                $body[] = $this->transformToSPS($transfer);
            }
        }

        $response = Http::post('http://10.2.2.45:8087/api/Transfer/Transfer', [
            'transfers' => $body
        ]);
    }

    /**
     * change Status And Create Log.
     * 
     */
    public function changeStatusAndCreateLog($transfer, $status, $type, $user_id, $results)
    {
        $transfer->status = $status;
        $transfer->save();

        $log = TransferLog::create([
            'type' =>  $type ,
            'user_id' => $user_id,
            'transfer_id' => $transfer->id,
            'transfer_status' => $transfer->status,
            'status' => $status,
            'results' => $results,
        ]);
    }

    /**
     * transform data To sent SPS.
     * 
     */
    protected function transformToSPS($transfer)
    {
        return [
            'referenceNumber' => (string) $transfer->id,
            'amount' => $transfer->amount,
            'beneficiaryName' => $transfer->user->beneficiary_name,
            'beneficiaryIban' => $transfer->user->iban_number,
            'beneficiaryStreet' => $transfer->user->business_address,
            'beneficiaryCountry' => 'SA',
            'beneficiaryBank' => $transfer->user->bank->code,
            "isSynced" => true,
            "transferRequest" => "string",
            "transferResponse" => "string",
            "transferStatusId" => 0,
            "transferStatusName" => "string",
            "beneficiaryCity" => "riyadh"
        ];
    }


    /**
     * create Transfer Transaction.
     *
     * @param  App\Models\Transfer  $transfer
     * @return void
     */
    protected function createTransferTransaction($transfer)
    {
        $bankCode   = $transfer->user->bank ? $transfer->user->bank->code : '-';
        $bankNumber = substr($transfer->user->iban_number, -4);

        $transaction = new Transaction;
        $transaction->user_id     = $transfer->user_id;
        $transaction->type        = 'debit';
        $transaction->amount      = $transfer->amount;
        $transaction->reference   = $transfer->id;
        $transaction->description = 'Transfer - ' . $bankCode . ' XXXX' . $bankNumber;
        $transaction->transaction_source = 'transfer';
        $transaction->save();    
    }
}
