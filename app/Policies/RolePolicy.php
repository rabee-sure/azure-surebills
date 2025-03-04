<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Admin  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Admin $user)
    {
        return $user->can('show roles');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Admin  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Admin $user, Role $role)
    {
        return $user->can('show roles');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Admin  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Admin $user)
    {
        return $user->can('create role');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Admin  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $user, Role $role)
    {
        return $user->can('edit role');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Admin  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $user, Role $role)
    {
        $users = Admin::whereHas('roles', function($q) use ($role){
            $q->where([['name', $role->name], ['guard_name', 'admins']]);
        })->count();

        return $user->can('delete role') && $users == 0;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Admin  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Admin $user, Role $role)
    {
        return $user->can('delete role');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Admin  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Admin $user, Role $role)
    {
        $users = Admin::whereHas('roles', function($q) use ($role){
            $q->where([['name', $role->name], ['guard_name', 'admins']]);
        })->count();

        return $user->can('delete role') && $users == 0;
    }

    public function updateMerchantRole(User $user, Role $role)
    {
        if($role->guard_name == 'web' && ($role->user_id == $user->id || $role->user_id == $user->store_main_user_id)){
            return $user->can('update user');
        }
        return false;
    }

    public function deleteMerchantRole(User $user, Role $role)
    {
        $users = User::whereHas('roles', function($q) use ($role){
            $q->where([['name', $role->name], ['guard_name', 'web']]);
        })->count();

        return $user->can('delete role') && $users == 0;
    }
}
