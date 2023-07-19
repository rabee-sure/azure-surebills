<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPasswordHistory extends Model
{
    use HasFactory;
    protected $fillable = ['admin_id', 'password',];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
