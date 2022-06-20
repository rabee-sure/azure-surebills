<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PosUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $authUser = $this;
        if($authUser->store_main_user_id != null){
            $user = $authUser->mainStoreUser;
        }else{
            $user = $authUser;
        }

        return [
            'id' => $this->id,
            'balance' => round($user->balance, 2),
            'business_name' => $user->business_name,
            'business_name_en' => $user->business_name_en,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'mobile_sent_at' => $this->mobile_sent_at->timestamp ?? null,
            'name' => $this->name,
            'bank_id' => $user->bank_id,
            'bank' => $user->bank,
            'iban_number' => $user->iban_number,
            'beneficiary_name' => $user->beneficiary_name,
            'diff_in_sec' => $this->getDiff(),
            'language' => \App::getLocale(),
            'token' => $this->createToken('pos-api-token')->accessToken,
            'settngs' => $user->settings,
            'vat_registration_number' => $this->vat_registration_number,
            'store_main_user_id' => $this->store_main_user_id,
            'business_address' => $this->business_address,
            'business_mobile' => $this->business_mobile,
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
