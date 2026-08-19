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
        $applicationFeeConfig = $this->resource->relationLoaded('displayFeeConfig')
            ? $this->resource->getRelation('displayFeeConfig')
            : $this->resource;

        $channel = $this->channel;
        if ($channel && $this->resource->relationLoaded('displayChannelFeeConfig')) {
            $channelFeeConfig = $this->resource->getRelation('displayChannelFeeConfig');
            $channel = clone $channel;

            foreach ([
                'name',
                'mada_fixed',
                'mada_percentage',
                'credit_cards_fixed',
                'credit_cards_percentage',
            ] as $field) {
                $channel->setAttribute($field, $channelFeeConfig->{$field});
            }
        }

        return [
            'id' => $this->id,
            'email' => $this->user->email,
            'name' => $this->name,
            'secret' => $this->secret,
            'redirect' => $this->redirect, 
            'fail_redirect_url' => $this->fail_redirect_url, 
            'webhook_url' => $this->webhook_url, 
            'webhook_secret' => $this->webhook_secret, 
            'channel' => $channel,
            'mada_fixed' => $applicationFeeConfig->mada_fixed,
            'mada_percentage' => $applicationFeeConfig->mada_percentage,
            'credit_cards_fixed' => $applicationFeeConfig->credit_cards_fixed,
            'credit_cards_percentage' => $applicationFeeConfig->credit_cards_percentage,
        ];
    }
}
