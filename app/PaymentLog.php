<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'user_id',
        'results',
        'status',
    ];

    protected $casts = [
        'results'   =>  'array',
    ];
}
