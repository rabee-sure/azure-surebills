<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Channel extends Model
{

    /**
     * set fillable.
     *
     * @return array
     */
    protected $fillable = [
        'name', 
        'user_id',
        'mada_fixed',
        'mada_percentage',
        'credit_cards_fixed',
        'credit_cards_percentage',
        'secret_token',
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

    public static function boot()
    {
        parent::boot();
        static::creating(function($channel)
        {
            $channel->secret_token = Str::random(30);
        });
    }
}
