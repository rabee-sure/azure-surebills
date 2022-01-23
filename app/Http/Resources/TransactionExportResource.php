<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'created_at'    => $this->created_at->format('d/m/Y H:i'),
            'description'    => $this->description,
            'type'           => $this->type,
            'amount'         => round($this->amount, 2),
            'bill_id'        => $this->bill_id,
            'card_brand'     => $this->card_brand,
            'card'           => $this->card,
            'source'         => $this->transaction_source,

            'bill_reference_id' => $this->bill->reference_id ?? '',
            'bill_number' => $this->bill->number  ?? '',
            'bill_user_id' => $this->bill->user_id?? '', 
            'bill_business_name' => $this->bill->business_name?? '', 
            "bill_application_channel_id" => ($this->bill && $this->bill->application) ? $this->bill->application->channel_id : null,
            "bill_application_channel_name" => ($this->bill && $this->bill->application && $this->bill->application->channel_id) ? $this->bill->application->channel->name : null,
        ];
    }
}
