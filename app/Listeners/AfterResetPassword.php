<?php

namespace App\Listeners;

use App\Models\AdminPasswordHistory;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class AfterResetPassword
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        $className = class_basename(get_class($event->user));
        if($className == 'Admin'){
            $event->user->last_change_password_at = Carbon::now()->toDateTimeString();
            $event->user->password_block = false;
            $event->user->save();
    
            $password = new AdminPasswordHistory(['password' => ($event->user->password)]);
            $event->user->passwordsHistory()->save($password);
        }
    }
}
