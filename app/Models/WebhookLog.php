<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'status',
        'response',
        'payload',
        'status_code',
        'error_message',
        'user_id',
        'bill_id',
        'application_id',
    ];

    protected $casts = [
        'response'   =>  'array',
        'payload'   =>  'array',
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

    /**
     * Get bill.
     *
     * @return Collection
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }


    /**
     * Get application.
     *
     * @return Collection
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
