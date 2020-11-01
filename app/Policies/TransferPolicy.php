<?php

namespace App\Policies;

use App\Bill;
use App\Transfer;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransferPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Transfer  $transfer
     * @return mixed
     */
    public function view(User $user, Transfer $transfer)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Transfer  $transfer
     * @return mixed
     */
    public function update(User $user, Transfer $transfer)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Transfer  $transfer
     * @return mixed
     */
    public function delete(User $user, Transfer $transfer)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Transfer  $transfer
     * @return mixed
     */
    public function restore(User $user, Transfer $transfer)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Transfer  $transfer
     * @return mixed
     */
    public function forceDelete(User $user, Transfer $transfer)
    {
        return true;
    }

    /**
     * Determine whether the user can attach a bill to a podcast.
     *
     * @param  \App\User  $user
     * @param  \App\Podcast  $podcast
     * @param  \App\Bill  $bill
     * @return mixed
     */
    public function attachBill(User $user, Transfer $transfer, Bill $bill)
    {
        return false;
    }

        /**
     * Determine whether the user can detach a tag from a podcast.
     *
     * @param  \App\User  $user
     * @param  \App\Podcast  $podcast
     * @param  \App\Tag  $tag
     * @return mixed
     */
    public function detachBill(User $user, Transfer $transfer, Bill $bill)
    {
        return false;
    }

    
    /**
     * Determine whether the user can attach any tags to the podcast.
     *
     * @param  \App\User  $user
     * @param  \App\Podcast  $podcast
     * @return mixed
     */
    public function attachAnyBill(User $user, Transfer $transfer)
    {
        return false;
    }

}
