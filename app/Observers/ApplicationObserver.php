<?php

namespace App\Observers;

use App\Models\Application;
use App\Services\ApplicationFeeConfigService;
use App\Services\Cache\CacheKeys;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApplicationObserver
{
    public function __construct(
        private readonly ApplicationFeeConfigService $applicationCache,
    ) {}

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

        $this->putSafely($application);
    }

    /**
     * Handle the application "updated" event.
     *
     * @param  \App\Models\Application  $application
     * @return void
     */
    public function updated(Application $application)
    {
        $this->putSafely($application);
    }

    /**
     * Handle the application "deleted" event.
     *
     * @param  \App\Models\Application  $application
     * @return void
     */
    public function deleted(Application $application)
    {
        $this->forgetSafely($application);
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

    private function putSafely(Application $application): void
    {
        try {
            $this->applicationCache->put($application);
        } catch (Throwable $e) {
            Log::warning('channels cache rewrite failed after application mutation', [
                'domain' => 'channels',
                'key' => CacheKeys::application($application->id),
                'operation' => 'put',
                'exception' => $e::class,
            ]);
        }
    }

    private function forgetSafely(Application $application): void
    {
        try {
            $this->applicationCache->forget((int) $application->id);
        } catch (Throwable $e) {
            Log::warning('channels cache forget failed after application delete', [
                'domain' => 'channels',
                'key' => CacheKeys::application($application->id),
                'operation' => 'forget',
                'exception' => $e::class,
            ]);
        }
    }
}
