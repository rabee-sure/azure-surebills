<?php

namespace App\Observers;

use App\Events\UserVerifiedChanged;
use App\Events\AddActionLogEvent;
use App\Events\UserUpdated;
use App\Jobs\SetNewMerchantSettings;
use App\Models\User;
use App\Models\Role;
use App\Models\SystemAction;
use Illuminate\Support\Facades\Auth;


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
        
        SetNewMerchantSettings::dispatch($user);
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
        event(new UserUpdated($user));
        
        if(Auth::guard('admins')->check()){
            $fieldsArr = config('userfields');
    
            foreach($fieldsArr as $groupKey => $fieldsGroup){
                $fieldsChanges = [];
                if($user->isDirty($fieldsGroup)){
                    foreach($fieldsGroup as $field){
                        if($user->isDirty($field)){
                            $fieldsChanges[$field] = [
                                'old_value' => $user->getOriginal($field),
                                'new_value' => $user->$field
                            ];
                        }
                    }
                    event(new AddActionLogEvent(
                        'user_update', 
                        Auth::id(), 
                        [
                            'message' => [
                                'username' => $user->name,
                                'adminname' => Auth::user()->name,
                                'fields_group' => $groupKey,
                                'time' => $user->updated_at,
                            ],
                            'changes' => $fieldsChanges,
                        ], 
                        $user->id, 
                        User::class
                    ));
                }
            }
        }
        if($user->isDirty('verified')){
            UserVerifiedChanged::dispatch($user);
            
            if(Auth::guard('admins')->check()){
                event(new AddActionLogEvent(
                    $user->verified ? 'user_approved' : 'user_unapproved', 
                    Auth::id(), 
                    ['message' => [
                        'username' => $user->name,
                        'adminname' => Auth::user()->name,
                        'time' => $user->updated_at
                    ]], 
                    $user->id, 
                    User::class
                ));
            }
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
