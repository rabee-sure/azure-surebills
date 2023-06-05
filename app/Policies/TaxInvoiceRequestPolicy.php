<?php

namespace App\Policies;

use App\Models\TaxInvoiceRequest;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxInvoiceRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Admin $user)
    {
        return $user->can('receive tax invoice request');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Admin $user, TaxInvoiceRequest $taxInvoiceRequest)
    {
        return $user->can('receive tax invoice request');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $user, TaxInvoiceRequest $taxInvoiceRequest)
    {
        return $user->can('receive tax invoice request');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $user, TaxInvoiceRequest $taxInvoiceRequest)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Admin $user, TaxInvoiceRequest $taxInvoiceRequest)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Admin $user, TaxInvoiceRequest $taxInvoiceRequest)
    {
        //
    }
}
