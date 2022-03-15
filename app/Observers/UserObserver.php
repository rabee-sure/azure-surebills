<?php

namespace App\Observers;

use App\Events\UserVerifiedChanged;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $this->letUserSuperAdmin($user);
    }

    public function saved(User $user)
    {
        $this->letUserSuperAdmin($user);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        if($user->isDirty('verified')){
            UserVerifiedChanged::dispatch($user);
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }

    private function letUserSuperAdmin($user)
    {
        if(!$user->store_main_user_id)
        {
            $role = Role::where('name', 'super admin')->first();
            $user->assignRole($role->id);
        }
    }
}
