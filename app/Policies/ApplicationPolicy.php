<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use PHPUnit\Framework\Constraint\IsFalse;

class ApplicationPolicy
{
    use HandlesAuthorization;

    public function updateMerchantApplication(User $user, Application $application)
    {
        $mainUserID = $user->store_main_user_id == null ? $user->id : $user->store_main_user_id;
        $authrization  = ($application->user_id == $mainUserID) ? true : false;
        return $authrization;
    }

    public function deleteMerchantApplication(User $user, Application $application)
    {
        $mainUserID = $user->store_main_user_id == null ? $user->id : $user->store_main_user_id;
        $authrization  = ($application->user_id == $mainUserID) ? true : false;
        return $authrization;
    }
}
