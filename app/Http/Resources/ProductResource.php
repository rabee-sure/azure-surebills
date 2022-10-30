<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductImageResource;
use App\Http\Resources\ProductCustomizationResource;

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
            'name' => [
                'en' => $this->getTranslation('name', 'en'),
                'ar' => $this->getTranslation('name', 'ar'),
            ],
            'discription' => [
                'en' => $this->getTranslation('discription', 'en'),
                'ar' => $this->getTranslation('discription', 'ar'),
            ],
            'price' => $this->price,
            'sort_number' => $this->sort_number,
            'active' => $this->active,
            'category_id' => $this->category_id,
            'images' => ProductImageResource::collection($this->images),
            'enable_customizations' => $this->enable_customizations,
            'customizations' => $this->enable_customizations ? ProductCustomizationResource::collection($this->customizations) : [],
        ];
    }
}
