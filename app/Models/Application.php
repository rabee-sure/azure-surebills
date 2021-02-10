<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'user_id', 
        'channel_id', 
        'name', 
        'secret', 
        'redirect', 
        'fail_redirect_url', 
        'webhook_url', 
        'webhook_secret',

        'channel_id',
        'mada_fixed',
        'mada_percentage',
        'credit_cards_fixed',
        'credit_cards_percentage',
    ];

    /**
     * Get channel.
     *
     * @return Collection
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
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

    public function getBackUrlAttribute()
    {
        $parse = parse_url($this->redirect);
        // dd($parse);
        return $parse['scheme'].'://'.$parse['host'];
    }
}
