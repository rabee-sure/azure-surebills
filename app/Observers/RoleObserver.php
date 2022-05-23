<?php

namespace App\Observers;

use App\Models\Role;

class RoleObserver
{
    private $permissions = null;
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
        $this->permissions = $role->admin_permissions;
        unset($role->admin_permissions);

        // unset($role->admin_permissions);
        // if($role->isDirty('admin_permissions'))
        // {
        //     $role->permissions()->sync($role->admin_permissions);
        //     unset($role->admin_permissions);
        // }
    }


    public function saved(Role $role)
    {
        dd($this->permissions);
        $role->permissions()->sync($this->permissions);

        // if($role->isDirty('admin_permissions'))
        // {
        //     $role->permissions()->sync($this->$permissions);
        //     // unset($role->admin_permissions);
        // }
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
