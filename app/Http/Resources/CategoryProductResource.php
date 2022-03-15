<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryProductResource extends JsonResource
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
            'image' => $this->image,
            'sort_number' => $this->sort_number,
            'active' => $this->active,
            'parent_id' => $this->parent_id,
            'childiren' => $this->childiren,
            'products' => $this->products,
        ];
    }
}
