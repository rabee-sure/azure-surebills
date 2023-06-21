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

class Admin extends Authenticatable
{
    use HasFactory, HasRoles, SoftDeletes, Notifiable;
    protected $fillable = ['name', 'email', 'mobile', 'password', 'is_active'];

    public function passwordsHistory()
    {
        return $this->hasMany(AdminPasswordHistory::class);
    }

    private function generateTempPassword(){
        $tempPassword = Hash::make(Str::random(15));
        return $tempPassword;
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
