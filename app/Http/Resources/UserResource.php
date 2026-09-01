<?php

namespace App\Http\Resources;

use App\Services\OtpService;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'balance' => round($this->balance, 2),
            'business_name' => $this->business_name,
            'business_name_en' => $this->business_name_en,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'mobile_sent_at' => $this->mobile_sent_at->timestamp ?? null,
            'name' => $this->name,

            'bank_id' => $this->bank_id,
            'bank' => $this->bank,
            'iban_number' => $this->iban_number,
            'beneficiary_name' => $this->beneficiary_name,
            'diff_in_sec' => $this->getDiff(),
            'language' => \App::getLocale(),
        ];
    }

    protected function getDiff(){
        return OtpService::secondsUntilResend($this->mobile_sent_at);
    }
}
