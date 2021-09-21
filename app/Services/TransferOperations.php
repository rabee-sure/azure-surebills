<?php

namespace App\Services;

use App\Events\TransferCreated;
use App\Models\TransferLog;
use App\Services\TransferService;
use Illuminate\Support\Facades\Http;

class TransferOperations
{
    /**
     * complete Transfars.
     * 
     */
    public function complete($transfers, $status, $user_id, $results, $from_sps)
    {
        foreach($transfers as $transfer){
            if($transfer->status == 'pending' ){
                $type = $from_sps ? $status.' sps transfer':$status.' transfer';

                $this->changeStatusAndCreateLog($transfer, $status, $type, $user_id, $results);

                TransferService::createTransferTransaction($transfer);

                $bills = $transfer->bills;
                $user_id = $transfer->user_id;
                foreach ($bills as $bill) {
                    if($bill->user_id == $user_id){
                        $bill->settled = true;
                    }

                    if($bill->isHaveChannelOwenByUser($user_id)){
                       $bill->channel_settled = true; 
                    }
                    $bill->save();
                }
                event(new TransferCreated($transfer));
            }
        }

    }

    /**
     * cancel Transfars.
     * 
     */
    public function cancel($transfers, $status, $user_id, $results, $from_sps)
    {
        foreach($transfers as $transfer){
            if($transfer->status == 'pending' ){
                $type = $from_sps ? $status.' sps transfer':$status.' transfer';

                $this->changeStatusAndCreateLog($transfer, $status, $type, $user_id, $results);

                $bills = $transfer->bills()->update(["pending_settled" => false]);

                $transactions = $transfer->transactions()->update(["pending_settled" => false]);
            }
        }

    }

    /**
     * send transfers To Sps.
     * 
     */
    public function sendToSps($transfers, $status, $user_id, $results, $from_sps)
    {
        $body = [];

        foreach($transfers as $transfer){
            if($transfer->status == 'pending' || $transfer->status == 'send_to_sps'){
                $type = $status.' sps transfer';
                $this->changeStatusAndCreateLog($transfer, $status, $type, $user_id, $results );
                $body[] = $this->transformToSPS($transfer);
            }
        }

        $response = Http::post('https://surebill-api.surepay.sa/api/Transfer/Transfer', [
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
            'amount' => $transfer->net_amount,
            'beneficiaryName' => $transfer->user->beneficiary_name,
            'beneficiaryIban' => $transfer->user->iban_number,
            'beneficiaryStreet' => $transfer->user->business_address,
            'beneficiaryCountry' => 'Saudi Arabia',
            'beneficiaryBank' => $transfer->user->bank->code,
            "isSynced" => true,
            "transferRequest" => "string",
            "transferResponse" => "string",
            "transferStatusId" => 0,
            "transferStatusName" => "string",
            "beneficiaryCity" => "riyadh"
        ];
    }
}
