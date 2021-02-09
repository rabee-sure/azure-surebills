<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Application;

class ApplicationObserver
{
    /**
     * Handle the application "created" event.
     *
     * @param  \App\Models\Application  $application
     * @return void
     */
    public function created(Application $application)
    {
        if($application->has('channel')){
            
            if($application->mada_fixed == null && $application->mada_percentage == null && $application->credit_cards_fixed == null && $application->credit_cards_percentage == null){
                $application->mada_fixed = $application->channel->mada_fixed ?? null;
                $application->mada_percentage = $application->channel->mada_percentage ?? null;
                $application->credit_cards_fixed = $application->channel->credit_cards_fixed ?? null;
                $application->credit_cards_percentage = $application->channel->credit_cards_percentage ?? null;
                $application->save();
            }
        }
    }

    /**
     * Handle the application "updated" event.
     *
     * @param  \App\Models\Application  $application
     * @return void
     */
    public function updated(Application $application)
    {
        //
    }

    /**
     * Handle the application "deleted" event.
     *
     * @param  \App\Models\Application  $application
     * @return void
     */
    public function deleted(Application $application)
    {
        //
    }

    /**
     * Handle the application "restored" event.
     *
     * @param  \App\Models\Application  $application
     * @return void
     */
    public function restored(Application $application)
    {
        //
    }

    /**
     * Handle the application "force deleted" event.
     *
     * @param  \App\Models\Application  $application
     * @return void
     */
    public function forceDeleted(Application $application)
    {
        //
    }
}
