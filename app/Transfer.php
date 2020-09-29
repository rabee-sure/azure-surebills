<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = 'settlements';
    protected $fillable = [
		'amount',
		'user_id',
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
     * Get user.
     *
     * @return Collection
     */
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
