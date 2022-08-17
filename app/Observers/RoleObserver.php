<?php

namespace App\Observers;

use App\Models\Role;
use App\Events\AddActionLogEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RoleObserver
{
    private static $permissions = null;
    private static $oldPermissions = null;
    private static $newPermissions = null;
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
        
    }

    public function saving(Role $role)
    {
        self::$oldPermissions = $role->permissions()->pluck('name')->toArray();

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

        self::$newPermissions = $role->permissions()->pluck('name')->toArray();

        if(request()->has('editMode') && request()->editMode == 'update'){
            if(Auth::guard('admins')->check()){
                $fieldsChanges = [];
        
                if($role->isDirty('name')){
                    $fieldsChanges['name'] = [
                        'old_value' => $role->getOriginal('name'),
                        'new_value' => $role->name
                    ];
                }
    
                $old_permissions = self::$oldPermissions;
                $new_permissions = self::$newPermissions;
                if(!empty(array_diff($old_permissions,$new_permissions)) || !empty(array_diff($new_permissions,$old_permissions)))
                {
                    $fieldsChanges['permissions'] = [
                        'old_value' => implode('<br>', self::$oldPermissions),
                        'new_value' => implode('<br>', self::$newPermissions)
                    ];
                }
        
                event(new AddActionLogEvent(
                    'update_role', 
                    Auth::id(), 
                    [
                        'message' => [
                            'adminname' => Auth::user()->name,
                            'time' => Carbon::now(),
                        ],
                        'changes' => $fieldsChanges,
                    ], 
                    $role->id, 
                    Role::class
                ));
            }
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
