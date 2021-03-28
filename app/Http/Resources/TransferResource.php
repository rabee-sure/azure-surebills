<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
            'amount' => $this->amount,
            'transfer_fees' => $this->transfer_fees,
            'net_amount' => $this->net_amount,
            'note' => $this->note,
            'status' => $this->status,
            'status_bool' =>(bool) ($this->status == 'completed'),
            'attachment' => $this->attachment,
            'filter_from' => Carbon::parse($this->filters['date']['from'])->format('d/m/Y H:i'),
            'filter_to' => Carbon::parse($this->filters['date']['to'])->format('d/m/Y H:i'),
            'created_by_name' => $this->created_by->name ?? 'NAN',
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'user_business_name_en' => $this->user->business_name_en,
        ];
    }
}
