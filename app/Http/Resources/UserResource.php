<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        $diff = Carbon::now()->diffInSeconds($this->mobile_sent_at);
        if ($diff > 60){
            return 0;
        }else{
            return 60 - $diff;
        }
    }
}
