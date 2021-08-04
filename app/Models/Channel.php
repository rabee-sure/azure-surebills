<?php

namespace App\Models;

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
        'activate',
        'mada_fixed',
        'mada_percentage',
        'credit_cards_fixed',
        'credit_cards_percentage',
        'secret_token',
        'sub_account_status_webhook',
        'sub_account_settled_webhook',
        'disable_login_sub_accounts',
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
     * Get all of the bills for the channel.
     */
    public function bills()
    {
        return $this->hasManyThrough(Bill::class, Application::class);
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
     * get only Active
     */
    public function scopeActivate($query){
        $query->where('activate', true);
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
