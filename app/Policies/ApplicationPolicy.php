<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use App\Models\Admin as Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use PHPUnit\Framework\Constraint\IsFalse;

class ApplicationPolicy
{
    use HandlesAuthorization;

    public function updateMerchantApplication(User $user, Application $application)
    {
        $mainUserID = $user->store_main_user_id == null ? $user->id : $user->store_main_user_id;
        $authrization  = ($application->user_id == $mainUserID) ? true : false;
        return $authrization;
    }

    public function deleteMerchantApplication(User $user, Application $application)
    {
        if($user->can('delete application')){
            $mainUserID = $user->store_main_user_id == null ? $user->id : $user->store_main_user_id;
            if($application->channel_id){
                $authrization  = ($application->channel->user_id == $mainUserID) ? true : false;
            }else{
                $authrization  = ($application->user_id == $mainUserID) ? true : false;
            }
        }
        return $authrization;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Admin $admin)
    {
        return $admin->can('show channels');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Admin $admin, Application $application)
    {
        return $admin->can('show channels');
    }
}
