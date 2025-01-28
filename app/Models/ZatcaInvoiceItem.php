<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZatcaInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'product_name',
        'product_price',
        'quantity',
        'total',
    ];

    public function bill(){
        return $this->belongsTo(ZatcaInvoice::class, 'bill_id', 'id');
    }
}
