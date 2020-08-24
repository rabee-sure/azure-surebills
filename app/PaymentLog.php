<?php

namespace App;

use App\Bill;
use Hashids\Hashids;
use Ramsey\Uuid\Uuid;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'user_id',
        'bill_id',
        'payment_method',
        'results',
        'status',
        'data',
    ];

    protected $casts = [
        'results'   =>  'array',
        'data'   =>  'array',
    ];
    
    public function getHashIdAttribute()
    {
        $hashids = new Hashids();
        return $hashids->encodeHex($this->id);
    }

    static public function decodeId($hashed_id)
    {
        $hashids = new Hashids();
        $id = $hashids->decodeHex($hashed_id);

        return self::find($id);
    }

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
