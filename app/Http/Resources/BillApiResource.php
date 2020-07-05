<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BillApiResource extends JsonResource
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
            'bill_id' => $this->id,
            'bill_status' => $this->status,
            'refrenece_id' => $this->refrenece_id,
            'pay_url' => $this->when($this->is_pending, $this->pay_url),
        ];
    }
}
