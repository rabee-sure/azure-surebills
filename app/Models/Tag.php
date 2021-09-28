<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
    ];

    /**
     * Get user.
     *
     * @return Collection
     */
    public function bills()
    {
        return $this->belongsToMany(Bill::class);
    }
}
