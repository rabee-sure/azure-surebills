<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosOrderItem extends Model
{
    protected $fillable = [
    	'order_id',
		'product_name',
        'product_category',
		'product_price',
		'quantity',
		'total',
        'product_parent',
	];

    /**
     * Get items.
     *
     * @return Collection
     */
    public function order()
    {
    	return $this->belongsTo(PosOrder::class, 'order_id');
    }

    public function customizations()
    {
        return $this->hasMany(PosOrderItem::class, 'product_parent', 'id');
    }

}
