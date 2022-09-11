<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundedBill extends Model
{
    use HasFactory;
    use UsesUuid;

    protected $fillable = [
        'bill_id',
        'user_id',
        'amount',
    ];

    public function bill(){
        return $this->belongsTo(Bill::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getNumber()
    {
        $number = self::max('number');

        return $number == 0 ? 1000001 : $number + 1;
    }
}
