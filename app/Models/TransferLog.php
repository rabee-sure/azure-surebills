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
        'transfer_status',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'results' => 'array',
    ];

    public function scopeUserId($query, $value)
    {
        return $query->where('user_id', $value);
    }
    /**
     * Get user.
     *
     * @return Collection
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id');
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
