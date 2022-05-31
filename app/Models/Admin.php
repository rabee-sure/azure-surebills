<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, HasRoles, SoftDeletes;
    protected $fillable = ['name', 'email', 'mobile', 'password'];

    // public function role()
    // {
    //     // return $this->belongsTo(Role::class);
    // }


    public function adminRole()
    {
        return $this->belongsTo(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions')
        );
    }


}
