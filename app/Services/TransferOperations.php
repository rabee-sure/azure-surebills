<?php

namespace App\Services;

use App\Events\TransferCreated;
use App\Events\AddActionLogEvent;
use App\Jobs\SendCompletedTransferEmailToMerchant;
use App\Models\Transaction;
use App\Models\TransferLog;
use App\Models\Transfer;
use App\Services\TransferService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class TransferOperations
{
    /**
     * complete Transfars.
     * 
     */
    public function complete($transfers, $status, $user_id, $results=null, $from_sps=false)
    {
        foreach ($transfers as $transfer) {
            DB::transaction(function () use ($transfer, $status, $user_id, $results, $from_sps) {
                $lockedTransfer = Transfer::where('id', $transfer->id)->lockForUpdate()->first();

                if (! $lockedTransfer || ! in_array($lockedTransfer->status, ['pending', 'send_to_sps'], true)) {
                    return;
                }

                $type = $from_sps ? $status.' sps transfer' : $status.' transfer';

                $this->changeStatusAndCreateLog($lockedTransfer, $status, $type, $user_id, $results);

                $this->createTransferTransaction($lockedTransfer);

                $lockedTransfer->transactions()
                    ->chunkById(1000, function ($transactions_ids) {
                        Transaction::whereIn('id', $transactions_ids->pluck('id'))->update(['settled' => true]);
                    });

                SendCompletedTransferEmailToMerchant::dispatch($lockedTransfer);
            });
        }
    }

    /**
     * cancel Transfars.
     * 
     */
    public function cancel($transfers, $status, $user_id, $results=null, $from_sps=false)
    {
        foreach ($transfers as $transfer) {
            DB::transaction(function () use ($transfer, $status, $user_id, $results, $from_sps) {
                $lockedTransfer = Transfer::where('id', $transfer->id)->lockForUpdate()->first();

                if (! $lockedTransfer || $lockedTransfer->status != 'pending') {
                    return;
                }

                $type = $from_sps ? $status.' sps transfer' : $status.' transfer';
                $this->changeStatusAndCreateLog($lockedTransfer, $status, $type, $user_id, $results);

                $lockedTransfer->transactions()->update(['pending_settled' => false]);
            });
        }
    }


    /**
     * fail Transfars.
     * 
     */
    public function fail($transfers, $status, $user_id, $results=null, $from_sps=false)
    {
        foreach ($transfers as $transfer) {
            DB::transaction(function () use ($transfer, $status, $user_id, $results, $from_sps) {
                $lockedTransfer = Transfer::where('id', $transfer->id)->lockForUpdate()->first();

                if (! $lockedTransfer || $lockedTransfer->status != 'send_to_sps') {
                    return;
                }

                $type = $from_sps ? $status.' sps transfer' : $status.' transfer';
                $this->changeStatusAndCreateLog($lockedTransfer, $status, $type, $user_id, $results);

                $lockedTransfer->transactions()->update(['pending_settled' => false]);
            });
        }
    }

    /**
     * pending Transfars.
     * 
     */
    public function pending($transfers, $status, $user_id, $results=null, $from_sps=false)
    {
        foreach ($transfers as $transfer) {
            DB::transaction(function () use ($transfer) {
                $lockedTransfer = Transfer::where('id', $transfer->id)->lockForUpdate()->first();

                if (! $lockedTransfer || $lockedTransfer->status != 'pending') {
                    return;
                }

                $lockedTransfer->transactions()->update(['pending_settled' => true]);
            });
        }
    }

    /**
     * send transfers To Sps.
     * 
     */
    public function sendToSps($transfers, $status, $user_id, $results=null, $from_sps=false, $sendHttp=true)
    {
        $body = [];

        foreach ($transfers as $transfer) {
            DB::transaction(function () use ($transfer, $status, $user_id, $results, &$body) {
                $lockedTransfer = Transfer::where('id', $transfer->id)->lockForUpdate()->first();

                if (! $lockedTransfer || ! in_array($lockedTransfer->status, ['pending', 'send_to_sps'], true)) {
                    return;
                }

                $type = $status.' sps transfer';
                $this->changeStatusAndCreateLog($lockedTransfer, $status, $type, $user_id, $results);
                $lockedTransfer->transactions()->update(['pending_settled' => true]);
                $body[] = $this->transformToSPS($lockedTransfer);
            });
        }

        if ($sendHttp && $body) {
            $this->dispatchSpsHttp($body);
        }
    }

    public function postToSps(array $transfers): void
    {
        $body = [];
        foreach ($transfers as $transfer) {
            if ($transfer) {
                $body[] = $this->transformToSPS($transfer);
            }
        }

        if ($body) {
            $this->dispatchSpsHttp($body);
        }
    }

    protected function dispatchSpsHttp(array $body): void
    {
        $url = 'https://surebill-api.surepay.sa/api/Transfer/Transfer';

        \Log::channel('send_to_sps')->info("request body", $body);

        $postData = json_encode([
            'transfers' => $body,
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS,$postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $server_output = curl_exec($ch);
        curl_close ($ch);

        \Log::channel('send_to_sps')->info("SPS response", ['response' => $server_output]);
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
