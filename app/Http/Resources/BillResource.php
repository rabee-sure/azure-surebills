<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
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
            'name' => $this->number  .'-'. $this->customer_name,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_method_type' => $this->payment_method_details,
            'user_id' => $this->user_id, 
            'customer_id' => $this->customer_id, 
            'business_name' => $this->business_name, 
            'customer_name' => $this->customer_name, 
            'customer_mobile' => $this->customer_mobile,
            'customer_email' => $this->customer_email,
            'customer_notes' => $this->customer_notes,
            'reference_id' => $this->reference_id,
            'due_date' => $this->due_date,
            'expiry_date' => $this->expiry_date,
            'add_discount' => $this->add_discount,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'add_tax' => $this->add_tax,
            'tax_name' => $this->tax_name,
            'tax_value' => $this->tax_value,
            'send_sms' => $this->send_sms,
            'send_email' => $this->send_email,
            'sub_total' => $this->sub_total,
            'vat' => $this->vat,
            'discount' => $this->discount,
            'total' => $this->total,
            'paid_at' => $this->paid_at->format('d/m/Y H:i'),
            'canceled_at' => $this->canceled_at,
            'pay_url' => $this->pay_url,
            'payment_fees' => $this->payment_fees,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'hyperpay_id' => $this->payment_logs[0]['results']['response']['id'],
        ];
    }
}
