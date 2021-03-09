<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Settings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateSettingsForUser
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
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $settings = Settings::updateOrCreate([
            'user_id' => $event->user->id, 
        ],[
            'add_tax' => false,
            'tax_value' => 0,
            'default_lang' => 'ar',
            'active_lang' => 'all',
            'create_send_sms' => false,
            'create_send_email' => false,
            'paid_send_sms' => false,
            'paid_send_email' => false,
        ]);
    }
}
