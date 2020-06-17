<?php

namespace App\Http\Resources;

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
            'business_name' => $this->business_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'mobile_sent_at' => $this->mobile_sent_at->timestamp,
            'name' => $this->name,
        ];
    }
}
