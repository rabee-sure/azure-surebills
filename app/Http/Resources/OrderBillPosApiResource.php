<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\BillPosItemsApiResource;

use App\Models\User;

class OrderBillPosApiResource extends JsonResource
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
            'bill_number' => $this->number,
            'bill_status' => $this->status,
            'reference_id' => $this->reference_id,
            'pay_url' => $this->when($this->is_pending, $this->pay_url),
            'payment_way' => $this->payment_way,
            'sub_total' => $this->sub_total,
            'add_discount' => $this->add_discount,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'discount' => $this->discount,
            'add_tax' => $this->add_tax,
            'tax_value' => $this->tax_value,
            'vat' => $this->vat,
            'total' => $this->total,
            'title' => $this->bill_title,
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at)),
            'items' => BillPosItemsApiResource::collection($this->items),
            'customer' => [
                'name' => $this->customer->name,
                'mobile' => $this->customer->mobile,
                'email' => $this->customer->email,
            ],
        ];
    }
}
