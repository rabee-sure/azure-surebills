<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Customer $customer)
    {
        $mainUserID = $user->store_main_user_id == null ? $user->id : $user->store_main_user_id;
        if (($customer->user_id == $mainUserID) or in_array($customer->user_id, $user->users->pluck('id')->toArray())) {
            return true;
        }
        return false;
    }

    public function delete(User $user, Customer $customer)
    {
        $mainUserID = $user->store_main_user_id == null ? $user->id : $user->store_main_user_id;
        if (($customer->user_id == $mainUserID) or in_array($customer->user_id, $user->users->pluck('id')->toArray())) {
            return true;
        }
        return false;
    }
}
