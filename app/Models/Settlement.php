<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $table = 'settlements';

    protected $fillable = [
		'amount',
		'user_id',
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
     * Get user.
     *
     * @return Collection
     */
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
