<?php

namespace App\Observers;

use App\Models\Role;
use App\Events\AddActionLogEvent;
use Illuminate\Support\Facades\Auth;

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
        if(Auth::guard('admins')->check()){
            event(new AddActionLogEvent(
                'create_role',
                Auth::id(),
                [
                    'message' => [
                        'adminname' => Auth::user()->name,
                        'time' => $role->created_at,
                    ],
                    'changes' => [],
                ],
                $role->id,
                Role::class
            ));
        }
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
        if(Auth::guard('admins')->check()){
            $fieldsChanges = [];
    
            if($role->isDirty('name')){
                $fieldsChanges['name'] = [
                    'old_value' => $role->getOriginal('name'),
                    'new_value' => $role->name
                ];
            }
    
            if($role->isDirty('admin_permissions'))
            {
                $old_permissions = $role->permissions();
                $role->permissions()->sync($role->admin_permissions);
                $new_permissions = $role->admin_permissions;
                unset($role->admin_permissions);
    
                $fieldsChanges['permissions'] = [
                    'old_value' => $old_permissions,
                    'new_value' => $new_permissions
                ];
            }
    
            event(new AddActionLogEvent(
                'update_role', 
                Auth::id(), 
                [
                    'message' => [
                        'adminname' => Auth::user()->name,
                        'time' => $role->updated_at,
                    ],
                    'changes' => $fieldsChanges,
                ], 
                $role->id, 
                Role::class
            ));
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
        if(Auth::guard('admins')->check()){
            event(new AddActionLogEvent(
                'delete_role',
                Auth::id(),
                [
                    'message' => [
                        'adminname' => Auth::user()->name,
                        'time' => $role->created_at,
                    ],
                    'changes' => [],
                ],
                $role->id,
                Role::class
            ));
        }
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
