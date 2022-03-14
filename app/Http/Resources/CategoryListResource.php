<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryListResource extends JsonResource
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
            'active' => ($this->active == 1) ? __('Active') : __('Disactive'),
            'parent' => ($this->parent_id != 0) ? $this->parent->name : __('Main'),
        ];
    }
}
