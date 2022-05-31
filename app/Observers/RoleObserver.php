<?php

namespace App\Observers;

use App\Models\Role;

class RoleObserver
{
    private static $permissions = null;
    /**
     * Handle the Role "created" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function created(Role $role)
    {
        //
    }

    /**
     * Handle the Role "updated" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function updated(Role $role)
    {
        //
    }

    public function retrieved(Role $role)
    {
        $role->admin_permissions = $role->permissions->pluck('id')->toArray();
    }

    public function updating(Role $role)
    {
        if($role->isDirty('admin_permissions'))
        {
            $role->permissions()->sync($role->admin_permissions);
            unset($role->admin_permissions);
        }
    }

    public function saving(Role $role)
    {
        if($role->isDirty('admin_permissions'))
        {
            self::$permissions = $role->admin_permissions;
            unset($role->admin_permissions);
            $role->name = request()->name;
        }
    }

    public function saved(Role $role)
    {
        if(self::$permissions)
        {
            $role->permissions()->sync(self::$permissions);
        }
    }

    /**
     * Handle the Role "deleted" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function deleted(Role $role)
    {
        //
    }

    /**
     * Handle the Role "restored" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function restored(Role $role)
    {
        //
    }

    /**
     * Handle the Role "force deleted" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function forceDeleted(Role $role)
    {
        //
    }
}
