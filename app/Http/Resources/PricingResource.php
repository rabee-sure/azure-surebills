<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingResource extends JsonResource
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
            'credit_cards_percentage' => $this->credit_cards_percentage,
            'mada_percentage' => $this->mada_percentage,            
            'credit_cards_fixed' => $this->credit_cards_fixed,
            'mada_fixed' => $this->mada_fixed,
            'credit_cards_pay_fees' => $this->credit_cards_pay_fees,
            'mada_pay_fees' => $this->mada_pay_fees,
        ];
    }
}
