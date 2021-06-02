<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\PaymentLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentLogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentLog  $log
     * @return mixed
     */
    public function view(User $user, PaymentLog $log)
    {
        return $log->bill->user_id == $user->id;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentLog  $log
     * @return mixed
     */
    public function viewBills(User $user, PaymentLog $log)
    {
        return $log->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentLog  $log
     * @return mixed
     */
    public function update(User $user, PaymentLog $log)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentLog  $log
     * @return mixed
     */
    public function delete(User $user, PaymentLog $log)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentLog  $log
     * @return mixed
     */
    public function restore(User $user, PaymentLog $log)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentLog  $log
     * @return mixed
     */
    public function forceDelete(User $user, PaymentLog $log)
    {
        return true;
    }

    /**
     * Determine whether the user can attach a bill to a podcast.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Podcast  $podcast
     * @param  \App\Models\Bill  $bill
     * @return mixed
     */
    public function attachBill(User $user, PaymentLog $log, Bill $bill)
    {
        return false;
    }

        /**
     * Determine whether the user can detach a tag from a podcast.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Podcast  $podcast
     * @param  \App\Tag  $tag
     * @return mixed
     */
    public function detachBill(User $user, PaymentLog $log, Bill $bill)
    {
        return false;
    }

    
    /**
     * Determine whether the user can attach any tags to the podcast.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Podcast  $podcast
     * @return mixed
     */
    public function attachAnyBill(User $user, PaymentLog $log)
    {
        return false;
    }

}
