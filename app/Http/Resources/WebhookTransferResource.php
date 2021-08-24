<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookTransferResource extends JsonResource
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
            'id' => $this->id,
            'account_id' => $this->user_id,
            'amount' => $this->amount,
            'transfer_fees' => $this->transfer_fees,
            'net_amount' => $this->net_amount,
            'cycle_date' => isset($this->filters['date']['to']) ? Carbon::parse($this->filters['date']['to'])->format('d/m/Y H:i') : null,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}
