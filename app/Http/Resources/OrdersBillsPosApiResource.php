<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\BillPosItemsApiResource;

class OrdersBillsPosApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $payment_type = '';
        switch ($this->payment_way) {
            case 'online':
                $payment_type = 'payment_electronical';
                break;
            case 'cash':
                $payment_type = 'cash';
                break;
            case 'bank_transfer':
                $payment_type = 'bank_transfer';
                break;
            case 'payment_machine':
                $payment_type = 'payment_machine';
                break;
            
            default:
                # code...
                break;
        }
        return [
            'bill_id' => $this->id,
            'bill_number' => $this->number,
            'bill_status' => $this->status,
            'reference_id' => $this->reference_id,
            'pay_url' => $this->when($this->is_pending, $this->pay_url),
            'total' => $this->total,
            'title' => $this->bill_title,
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at)),
            'payment_type' => $payment_type,
            'items' => BillPosItemsApiResource::collection($this->items),
        ];
    }
}
