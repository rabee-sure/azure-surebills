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
}
