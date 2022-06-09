<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\BillPosItemsApiResource;

class BillPosApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->user->settings->add_tax_invoice){
            $qr_code = generateQRcode($this);
        }

        return [
            'bill_id' => $this->id,
            'bill_number' => $this->number,
            'bill_status' => $this->status,
            'reference_id' => $this->reference_id,
            'pay_url' => $this->when($this->is_pending, $this->pay_url),
            'qr_code' => $qr_code ?? null,
            'total' => $this->total,
            'title' => $this->bill_title,
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at)),
            'items' => BillPosItemsApiResource::collection($this->items),
        ];
    }
}
