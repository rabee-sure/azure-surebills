<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'folder',
        'zip_file',
        'merchants_file',
        'channels_file',
        'tranfer_ids',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tranfer_ids' => 'array',
    ];

    /**
     * Get transfers.
     *
     * @return Collection
     */
    public function transfers()
    {
        return $this->belongsToMany(Transfer::class);
    }

}
