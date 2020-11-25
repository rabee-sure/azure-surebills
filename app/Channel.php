<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
        protected $fillable = [
        'name', 
        'user_id',
        'mada_fixed',
        'mada_percentage',
        'credit_cards_fixed',
        'credit_cards_percentage',
    ];
    /**
     * Get applications.
     *
     * @return Collection
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
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
}
