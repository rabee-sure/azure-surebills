<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'status',
        'results',
        'transfer_id',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'results' => 'array',
    ];

    /**
     * Get user.
     *
     * @return Collection
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }   

    /**
     * Get bank.
     *
     * @return Collection
     */
    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }   

}
