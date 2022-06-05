<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\Transfer;
use App\Models\AutoTransfer;
use App\Models\Admin as User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AutoTransferPolicy
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
     * @param  \App\Models\AutoTransfer  $auto_tranfer
     * @return mixed
     */
    public function view(User $user, AutoTransfer $auto_tranfer)
    {
        // return in_array($user->email, explode(',', env('NOVA_ALLOWED_ADMINS')));;
        return in_array($user->email, explode(',', auth()->user()->email));;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AutoTransfer  $auto_tranfer
     * @return mixed
     */
    public function viewTransfers(User $user, AutoTransfer $auto_tranfer)
    {
        // return in_array($user->email, explode(',', env('NOVA_ALLOWED_ADMINS')));;
        return in_array($user->email, explode(',', auth()->user()->email));;
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
     * @param  \App\Models\AutoTransfer  $auto_tranfer
     * @return mixed
     */
    public function update(User $user, AutoTransfer $auto_tranfer)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AutoTransfer  $auto_tranfer
     * @return mixed
     */
    public function delete(User $user, AutoTransfer $auto_tranfer)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AutoTransfer  $auto_tranfer
     * @return mixed
     */
    public function restore(User $user, AutoTransfer $auto_tranfer)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AutoTransfer  $auto_tranfer
     * @return mixed
     */
    public function forceDelete(User $user, AutoTransfer $auto_tranfer)
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
    public function attachTransfer(User $user, AutoTransfer $auto_tranfer, Transfer $bill)
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
    public function detachTransfer(User $user, AutoTransfer $auto_tranfer, Transfer $bill)
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
    public function attachAnyTransfer(User $user, AutoTransfer $auto_tranfer)
    {
        return false;
    }

}
