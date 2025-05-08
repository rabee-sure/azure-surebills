<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CybersourceTransactionDetailsResource extends JsonResource
{
    private $transactionDetails;
    private $fromCapture;
    public function __construct($transactionDetails, $fromCapture) {
        $this->transactionDetails = $transactionDetails;
        $this->fromCapture = $fromCapture;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $transactionDetails = $this->transactionDetails;

        $transactionType = null;
        $amount = null;
        $status = $this->fromCapture;

        if($transactionDetails['applicationInformation']['applications'][1]['returnCode'] == 1260000){
            $transactionType = 'payment';
        }elseif($transactionDetails['applicationInformation']['applications'][0]['returnCode'] == 1030000){
            $transactionType = 'refund';
            $amount = $transactionDetails['orderInformation']['amountDetails']['totalAmount'];
        }

        if(!$this->fromCapture){
            if(in_array($transactionDetails['applicationInformation']['status'], ['TRANSMITTED', 'PENDING'])) $status = true;
        }

        return [
            'bank_message' => $transactionDetails['errorInformation']['message'] ?? null,
            'bank_transaction_id' => $transactionDetails['id'],
            'brand' => $transactionDetails['processingInformation']['paymentSolution'],
            'card_number' => 'xxxx xxxx xxxx '.$transactionDetails['paymentInformation']['card']['suffix'],
            'result' => $transactionDetails,
            'bill_id' => $transactionDetails['clientReferenceInformation']['code'],
            'type' => $transactionType,
            'status' => $status,
            'amount' => $amount
        ];
    }
}
