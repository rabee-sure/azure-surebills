<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    protected $fillable = [
    	'bill_id',
		'product_name',
		'product_price',
		'quantity',
		'total',
	];

    /**
     * Get items.
     *
     * @return Collection
     */
    public function bill()
    {
    	return $this->belongsTo(Bill::class);
    }
}
