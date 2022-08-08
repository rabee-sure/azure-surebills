<?php

namespace App\Models;

use App\Models\Bill;
use App\Models\User;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class Statement extends Transaction
{
    protected $table = 'transactions';
}
