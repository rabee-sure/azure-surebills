<?php

namespace App\Policies;

use App\Models\Bank;
use App\Models\Admin as User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if(request()->route('resource') == 'banks')
        {
            return $user->can('show banks');
        }
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Bank $bank)
    {
        return $user->can('show banks');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create bank');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Bank $bank)
    {
        return $user->can('edit bank');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Bank $bank)
    {
        return $user->can('delete bank');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Bank $bank)
    {
        return $user->can('delete bank');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Bank $bank)
    {
        return $user->can('delete bank');
    }
}
