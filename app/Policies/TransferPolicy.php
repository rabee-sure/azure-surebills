<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\Admin as User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransferPolicy
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
        return $user->can('show transfer');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transfer  $transfer
     * @return mixed
     */
    public function view(User $user, Transfer $transfer)
    {
        // return $transfer->user_id == $user->id || in_array($user->email, explode(',', env('NOVA_ALLOWED_ADMINS')));;
        return $transfer->user_id == $user->id || in_array($user->email, explode(',', auth()->user()->email));;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transfer  $transfer
     * @return mixed
     */
    public function viewBills(User $user, Transfer $transfer)
    {
        // return $transfer->user_id == $user->id || in_array($user->email, explode(',', env('NOVA_ALLOWED_ADMINS')));;
        return $transfer->user_id == $user->id || in_array($user->email, explode(',', auth()->user()->email));;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transfer  $transfer
     * @return mixed
     */
    public function viewTransactions(User $user, Transfer $transfer)
    {
        // return $transfer->user_id == $user->id || in_array($user->email, explode(',', env('NOVA_ALLOWED_ADMINS')));;
        return $transfer->user_id == $user->id || in_array($user->email, explode(',', auth()->user()->email));;
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
     * @param  \App\Models\Transfer  $transfer
     * @return mixed
     */
    public function update(User $user, Transfer $transfer)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transfer  $transfer
     * @return mixed
     */
    public function delete(User $user, Transfer $transfer)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transfer  $transfer
     * @return mixed
     */
    public function restore(User $user, Transfer $transfer)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transfer  $transfer
     * @return mixed
     */
    public function forceDelete(User $user, Transfer $transfer)
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
    public function attachBill(User $user, Transfer $transfer, Bill $bill)
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
    public function detachBill(User $user, Transfer $transfer, Bill $bill)
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
    public function attachAnyBill(User $user, Transfer $transfer)
    {
        return false;
    }

    /**
     * Determine whether the user can attach a bill to a podcast.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Podcast  $podcast
     * @param  \App\Models\Bill  $bill
     * @return mixed
     */
    public function attachTransaction(User $user, Transfer $transfer, Transaction $bill)
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
    public function detachTransaction(User $user, Transfer $transfer, Transaction $bill)
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
    public function attachAnyTransaction(User $user, Transfer $transfer)
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
    public function TranferBillsExcelDownload(User $user, Transfer $transfer)
    {
        return true;
    }

}
