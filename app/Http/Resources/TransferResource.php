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
            'note' => $this->note,
            'attachment' => $this->attachment,
            'created_by_name' => $this->created_by->name ?? 'NAN',
            'created_at' => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}
