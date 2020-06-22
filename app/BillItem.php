<?php

namespace App;

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
}
