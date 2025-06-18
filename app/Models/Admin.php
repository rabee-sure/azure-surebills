<?php

namespace App\Models;

use App\Jobs\SendNovaAdminSetPasswordLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use CarlosCGO\Google2fa\Models\User2fa;

class Admin extends Authenticatable
{
    use HasFactory, HasRoles, SoftDeletes, Notifiable;
    protected $fillable = ['name', 'email', 'mobile', 'password', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    public function passwordsHistory()
    {
        return $this->hasMany(AdminPasswordHistory::class);
    }

    private static function generateTempPassword(){
        $tempPassword = Hash::make(Str::random(15));
        return $tempPassword;
    }

    /**
     * @return HasOne
     */
    public function user2fa()
    {
        return $this->hasOne(User2fa::class, 'user_id');
    }

    public function actionLogs(){
        return $this->hasMany(ActionLog::class, 'user_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function($admin)
        {
            $admin->password = Self::generateTempPassword();
            $admin->password_block = true;
        });

        static::created(function($admin)
        {
            SendNovaAdminSetPasswordLink::dispatch($admin);
        });

    }

}
