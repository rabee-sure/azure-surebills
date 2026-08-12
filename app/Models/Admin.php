<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, HasRoles, SoftDeletes, Notifiable;

    protected $fillable = ['name', 'email', 'mobile', 'password', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    public function passwordsHistory()
    {
        return $this->hasMany(AdminPasswordHistory::class);
    }

    public function actionLogs()
    {
        return $this->hasMany(ActionLog::class, 'user_id');
    }
}
