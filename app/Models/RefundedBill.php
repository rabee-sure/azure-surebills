<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class RefundedBill extends Model
{
    use HasFactory;
    use UsesUuid;

    protected $fillable = [
        'bill_id',
        'user_id',
        'amount',
    ];

    public function scopeUserId($query, $value)
    {
        return $query->where('user_id', $value);
    }

    public function bill(){
        return $this->belongsTo(Bill::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getNumber()
    {
        $number = self::max('number');
        $billNumber = Bill::max('number');

        if($number > $billNumber){
            return $number == 0 ? 1000001 : $number + 1;
        }else{
            return $billNumber == 0 ? 1000001 : $billNumber + 1;
        }

    }

    public function getHashedIdAttribute()
    {
        $uuid = Uuid::fromString($this->id);
        $hex = $uuid->getHex();
        $hashids = new Hashids();
        return $hashids->encodeHex($hex);
    }

    static public function decodeId($hashed_id)
    {
        $hashids = new Hashids();
        $hex = $hashids->decodeHex($hashed_id);
        $id = array_reduce([20, 16, 12, 8], function ($uuid, $offset) {
            return substr_replace($uuid, '-', $offset, 0);
        }, str_pad($hex, 32, '0', STR_PAD_LEFT));

        return self::find($id ?? null);
    }

    public function getNetRefundedAmountAttribute()
    {
        return round($this->amount / ((100/100) + ($this->bill->tax_value / 100)), 2);
    }

    public function getRefundedVatAttribute()
    {
        return round($this->net_refunded_amount * ($this->bill->tax_value / 100), 2);
    }
}
