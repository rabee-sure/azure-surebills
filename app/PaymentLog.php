<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'user_id',
        'results',
        'status',
        'data',
    ];

    protected $casts = [
        'results'   =>  'array',
        'data'   =>  'array',
    ];
}
