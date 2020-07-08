<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['name', 'secret', 'redirect', 'fail_redirect_url', 'webhook_url', 'webhook_secret'];

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
