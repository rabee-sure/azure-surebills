<?php

namespace App\Observers;

use App\Models\Admin;
use App\Events\AddActionLogEvent;
use Illuminate\Support\Facades\Auth;

class AdminObserver
{
    private static $role = null;

    public function saving(Admin $admin)
    {
        self::$role = $admin->role;
        unset($admin->role);
    }

    public function saved(Admin $admin)
    {
        if(self::$role)
        {
            $admin->roles()->detach();
            $admin->assignRole(self::$role);
        }
    }

    /**
     * Handle the Admin "created" event.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    public function created(Admin $admin)
    {
        if(Auth::guard('admins')->check()){
            event(new AddActionLogEvent(
                'create_user',
                Auth::id(),
                [
                    'message' => [
                        'username' => $admin->name,
                        'adminname' => Auth::user()->name,
                        'time' => $admin->created_at,
                    ],
                    'changes' => [],
                ],
                $admin->id,
                Admin::class
            ));
        }
    }

    /**
     * Handle the Admin "updated" event.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    public function updated(Admin $admin)
    {
        if(Auth::guard('admins')->check()){
            $fields = config('adminfields');
    
            $fieldsChanges = [];
            foreach($fields as $field){
                if($admin->isDirty($field)){
                    $fieldsChanges[$field] = [
                        'old_value' => $admin->getOriginal($field),
                        'new_value' => $admin->$field
                    ];
                }
            }
            event(new AddActionLogEvent(
                'update_user', 
                Auth::id(), 
                [
                    'message' => [
                        'username' => $admin->name,
                        'adminname' => Auth::user()->name,
                        'time' => $admin->updated_at,
                    ],
                    'changes' => $fieldsChanges,
                ], 
                $admin->id, 
                Admin::class
            ));
        }
    }

    /**
     * Handle the Admin "deleted" event.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    public function deleted(Admin $admin)
    {
        if(Auth::guard('admins')->check()){
            event(new AddActionLogEvent(
                'delete_user',
                Auth::id(),
                [
                    'message' => [
                        'username' => $admin->name,
                        'adminname' => Auth::user()->name,
                        'time' => $admin->created_at,
                    ],
                    'changes' => [],
                ],
                $admin->id,
                Admin::class
            ));
        }
    }

    /**
     * Handle the Admin "restored" event.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    public function restored(Admin $admin)
    {
        if(Auth::guard('admins')->check()){
            event(new AddActionLogEvent(
                'restore_user',
                Auth::id(),
                [
                    'message' => [
                        'username' => $admin->name,
                        'adminname' => Auth::user()->name,
                        'time' => $admin->created_at,
                    ],
                    'changes' => [],
                ],
                $admin->id,
                Admin::class
            ));
        }
    }

    /**
     * Handle the Admin "force deleted" event.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    public function forceDeleted(Admin $admin)
    {
        if(Auth::guard('admins')->check()){
            event(new AddActionLogEvent(
                'force_delete_user',
                Auth::id(),
                [
                    'message' => [
                        'username' => $admin->name,
                        'adminname' => Auth::user()->name,
                        'time' => $admin->created_at,
                    ],
                    'changes' => [],
                ],
                $admin->id,
                Admin::class
            ));
        }
    }
}
