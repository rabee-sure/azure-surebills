<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductImageResource;

class ProductResource extends JsonResource
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
            'type' => 'product',
            'name' => $this->name,
            'discription' => $this->discription,
            'price' => $this->price,
            'sort_number' => $this->sort_number,
            'active' => $this->active,
            'category_id' => $this->category_id,
            'images' => ProductImageResource::collection($this->images),
        ];
    }
}
