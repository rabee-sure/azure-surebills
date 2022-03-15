<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
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
            'name' => $this->name,
            'discription' => $this->discription,
            'price' => $this->price,
            'sort_number' => $this->sort_number,
            'active' => ($this->active == 1) ? __('Active') : __('Disactive'),
            'category' => $this->category->name,
        ];
    }
}
