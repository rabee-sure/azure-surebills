<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelApplicationResource extends JsonResource
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
            'email' => $this->user->email,
            'name' => $this->name,
            'secret' => $this->secret,
            'redirect' => $this->redirect, 
            'fail_redirect_url' => $this->fail_redirect_url, 
            'webhook_url' => $this->webhook_url, 
            'webhook_secret' => $this->webhook_secret, 
            'channel' => $this->channel, 
            'mada_fixed' => $this->mada_fixed, 
            'mada_percentage' => $this->mada_percentage, 
            'credit_cards_fixed' => $this->credit_cards_fixed, 
            'credit_cards_percentage' => $this->credit_cards_percentage, 
        ];
    }
}
