<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'amount'         => round($this->amount, 2),
            'user_id'        => $this->user_id,
            'bill_id'        => $this->bill_id,
            'reference'      => $this->reference,
            'description'    => $this->description,
            'type'           => $this->type,
            'balance'        => round($this->balance,2),
            'card'           => $this->card,
            'card_brand'     => $this->card_brand,
            'auth_id'        => $this->auth_id,
            'receipt'        => $this->receipt,
            'customer_notes' => $this->bill->customer_notes ?? null,
            'reference_id'   => $this->bill->reference_id ?? null,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}
