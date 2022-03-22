<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoTransferTransfer extends Model
{
    use HasFactory;
    protected $table = 'auto_transfer_transfer';
    protected $fillable = ['auto_transfer_id', 'transfer_id'];
    public $timestamps = false;

}
