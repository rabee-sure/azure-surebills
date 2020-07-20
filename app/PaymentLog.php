<?php

namespace App;

use App\Bill;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'user_id',
        'bill_id',
        'results',
        'status',
        'data',
    ];

    protected $casts = [
        'results'   =>  'array',
        'data'   =>  'array',
    ];

    /**
     * Get bill.
     *
     * @return Collection
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
