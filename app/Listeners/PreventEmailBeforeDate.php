<?php

namespace App\Listeners;

use App\Events\MessageSending;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use Illuminate\Mail\Events\MessageSending as EventsMessageSending;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class PreventEmailBeforeDate
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
     * @param  \App\Events\MessageSending  $event
     * @return void
     */
    public function handle(EventsMessageSending $event)
    {
        // Exclude production environment
        if (App::environment('production')) {
            return;
        }

        // Define the minimum required account age (e.g., users created before this date are blocked)
        $blockBeforeDate = Carbon::parse(env('BLOCK_EMAIL_BEFORE_DATE', null));
        if ($blockBeforeDate === null) {
            return;
        }

        // Get recipient emails
        $toEmails = collect($event->message->getTo())->keys()->toArray();

        // Fetch blocked users based on `created_at`
        $blockedUsers = User::whereIn('email', $toEmails)
            ->where('created_at', '<', $blockBeforeDate)
            ->pluck('email')
            ->toArray();

        // If any recipient is blocked, prevent the email from being sent
        foreach ($toEmails as $email) {
            if (in_array($email, $blockedUsers)) {
                return false; // Stops the email from being sent
            }
        }
    }
}
