<?php

namespace App\Policies;

use App\Models\Statement;
use App\Models\Admin as User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatementPolicy
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
        return true;//$user->can('show statements') && $user->can('show merchants');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Statement  $statement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Statement $statement)
    {
        return $user->can('show statements') && $user->can('show merchants');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Statement  $statement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Statement $statement)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Statement  $statement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Statement $statement)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Statement  $statement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Statement $statement)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Statement  $statement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Statement $statement)
    {
        return false;
    }
}
