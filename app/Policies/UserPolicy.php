<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin as Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Admin $admin)
    {
        return $admin->can('show merchants');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Admin $admin, User $model)
    {
        return $admin->can('show merchants');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Admin $admin)
    {
        return $admin->can('create merchant');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $admin, User $model)
    {
        return $admin->can('edit merchant');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $admin, User $model)
    {
        return $admin->can('delete merchant');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Admin $admin, User $model)
    {
        return $admin->can('delete merchant');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Admin $admin, User $model)
    {
        return $admin->can('delete merchant');
    }

    public function checkPermission(User $user, User $model)
    {
        $mainUser = $user->store_main_user_id == null ? $user : $user->mainStoreUser;
        if (($model->id == $mainUser->id) || in_array($model->id, $mainUser->users->pluck('id')->toArray()) || ($model->store_main_user_id == $user->store_main_user_id && !empty($model->store_main_user_id))) {
            return true;
        }
        return false;
    }

    public function updateMerchantUser(User $user, User $model)
    {

        if($user->can('update user')){
            if($user->store_main_user_id){
                if($model->id == $user->id || $model->store_main_user_id == $user->store_main_user_id){
                    return true;
                }
            }else{
                if($model->store_main_user_id == $user->id){
                    return true;
                }
            }
        }
        
        return false;
    }
    
    public function deleteMerchantUser(User $user, User $model)
    {
        if($user->can('delete user')){
            if($user->store_main_user_id){
                if($model->id != $user->id && $model->store_main_user_id == $user->store_main_user_id){
                    return true;
                }
            }else{
                if($model->store_main_user_id == $user->id){
                    return true;
                }
            }
        }
        
        return false;
    }
}
