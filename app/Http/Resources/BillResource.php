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
            'number' => $this->number,
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
            'discount_value' => round($this->discount_value, 2),
            'add_tax' => $this->add_tax,
            'tax_name' => $this->tax_name,
            'tax_value' => round($this->tax_value, 2),
            'send_sms' => $this->send_sms,
            'send_email' => $this->send_email,
            'sub_total' => $this->sub_total,
            'vat' => $this->vat,
            'payment_fees_vat' => round($this->payment_fees_vat, 2),
            'net' => round($this->total - $this->payment_fees - $this->payment_fees_vat, 2),
            'discount' => $this->discount,
            'total' => $this->total,
            'paid_at' => ($this->paid_at) ?$this->paid_at->format('d/m/Y H:i') : null,
            'canceled_at' => $this->canceled_at,
            'pay_url' => $this->pay_url,
            'payment_fees' => $this->payment_fees,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'total' => $this->total,
            'title' => $this->bill_title,
            'pricing' => $this->pricing,
            'source' => $this->application_id ? 'Api' : 'Manual',
            "related_channel" => ($request->channel_user_id) ? ($request->channel_user_id != $this->user_id) : false,
            "channel_name" => ($request->channel_user_id && $request->channel_user_id != $this->user_id) ? $this->application->channel->name : null,
            "channel_relation" => ($request->channel_user_id && $request->channel_user_id != $this->user_id) ? 'Channel' : 'Owner',
            "payment_channel_fees" =>  round($this->payment_channel_fees, 2),
            "payment_channel_fees_vat" =>round( $this->payment_channel_fees_vat, 2),
            "total_due" => ($request->channel_user_id && $request->channel_user_id != $this->user_id) ? round($this->payment_channel_fees + $this->payment_channel_fees_vat, 2) : round($this->total - $this->payment_fees - $this->payment_fees_vat, 2),

            "payment_surebills_fees" =>  round($this->payment_surebills_fees, 2),
            "payment_surebills_fees_vat" =>round( $this->payment_surebills_fees_vat
                , 2),

            "application_channel_id" => ($this->application) ? $this->application->channel_id : null,
            "application_channel_name" => ($this->application && $this->application->channel_id) ? $this->application->channel->name : null,
            'user' => $this->user,
            'refund_amount' => $this->refund_amount,
        ];

    }
}
