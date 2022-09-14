<?php

namespace App\Services;

use App\Events\TransferCreated;
use App\Events\AddActionLogEvent;
use App\Models\Transaction;
use App\Models\TransferLog;
use App\Models\Transfer;
use App\Services\TransferService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransferOperations
{
    /**
     * complete Transfars.
     * 
     */
    public function complete($transfers, $status, $user_id, $results=null, $from_sps=false)
    {
        foreach($transfers as $transfer){
            if($transfer->status == 'pending' || $transfer->status == 'send_to_sps' ){
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
     * fail Transfars.
     * 
     */
    public function fail($transfers, $status, $user_id, $results=null, $from_sps=false)
    {
        foreach($transfers as $transfer){
            if($transfer->status == 'send_to_sps' ){
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
                $this->changeStatusAndCreateLog($transfer, $status, $type, $user_id, $results);
                $transfer->transactions()->update(["pending_settled" => true]);
                $body[] = $this->transformToSPS($transfer);
            }
        }

        // $response = Http::post('http://10.2.2.45:8087/api/Transfer/Transfer', [
        // $response = Http::post('https://surebill-api.surepay.sa/api/Transfer/Transfer', [
        //     'transfers' => $body
        // ]);

        $client = new Client();
        $url = 'https://surebill-api.surepay.sa/api/Transfer/Transfer';
        $postData = [
            'transfers' => $body
        ];

        $response = $client->request('POST', $url, ['verify' => config('guzzle.certification'),'body'=>json_encode($postData)]);                                            
        //log $reponse
        log::send_to_sps('SPS response', $response->getBody()->getContents());
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

        if(in_array($status, ['canceled', 'completed', 'send_to_sps'])){
            $log_type = '';
    
            switch ($status) {
                case 'canceled':
                    $log_type = 'reject_transfer';
                    break;
    
                case 'completed':
                    $log_type = 'accept_transfer';
                    break;
            
                case 'send_to_sps':
                    $log_type = 'send_to_sps';
                    break;
                
                default:
                    # code...
                    break;
            }
    
            if(Auth::guard('admins')->check()){
                event(new AddActionLogEvent(
                    $log_type,
                    Auth::id(),
                    [
                        'message' => [
                            'username' => $transfer->user->name,
                            'adminname' => Auth::user()->name,
                            'id' => $transfer->id,
                            'amount' => $transfer->net_amount,
                            'time' => $transfer->created_at,
                        ],
                        'changes' => [],
                    ],
                    $transfer->id,
                    Transfer::class
                ));
            }
        }
    }

    /**
     * transform data To sent SPS.
     * 
     */
    protected function transformToSPS($transfer)
    {
        return [
            'referenceNumber' => (string) $transfer->id,
            'amount' => $transfer->net_amount,
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

        //condition added for prevent the duplication of transactions
        $duplicatedTransaction = Transaction::where('reference', $transfer->id)->where('transaction_source', 'transfer')->count();
        if($duplicatedTransaction == 0){
            $transaction = new Transaction;
            $transaction->user_id     = $transfer->user_id;
            $transaction->type        = 'debit';
            $transaction->amount      = $transfer->amount;
            $transaction->reference   = $transfer->id;
            $transaction->description = 'Transfer - ' . $bankCode . ' XXXX' . $bankNumber;
            $transaction->transaction_source = 'transfer';
            $transaction->save();    
        }else{
            \Log::channel('transfer')->info("transactions for this transfer duplicated for transfer number $transfer->id for merchant number $transfer->user_id");
        }
    }
}
