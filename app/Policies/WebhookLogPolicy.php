<?php

namespace App\Policies;

use App\Models\Admin as User;
use App\Models\WebhookLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebhookLogPolicy
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
        return $user->can('show webhook logs');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WebhookLog  $webhookLog
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, WebhookLog $webhookLog)
    {
        return $user->can('show webhook logs');
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
     * @param  \App\Models\WebhookLog  $webhookLog
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, WebhookLog $webhookLog)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WebhookLog  $webhookLog
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, WebhookLog $webhookLog)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WebhookLog  $webhookLog
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, WebhookLog $webhookLog)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WebhookLog  $webhookLog
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, WebhookLog $webhookLog)
    {
        return false;
    }
}
