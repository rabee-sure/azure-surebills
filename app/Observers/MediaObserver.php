<?php

namespace App\Observers;

use App\Models\Media;
use App\Models\User;
use App\Events\AddActionLogEvent;
use Illuminate\Support\Facades\Auth;

class MediaObserver
{
    /**
     * Handle the Media "created" event.
     *
     * @param  \App\Models\Media  $media
     * @return void
     */
    public function created(Media $media)
    {
        $user = User::find($media->model_id);

        if($media->model_type == "App\Models\User"){
            switch ($media->collection_name) {
                case 'business_documents':
                    $type = 'business';
                    break;

                case 'bank_documents':
                    $type = 'bank';
                    break;

                default:
                    # code...
                    break;
            }
            event(new AddActionLogEvent(
                'user_add_decouments',
                Auth::id(),
                [
                    'message' => [
                        'username' => $user->name,
                        'adminname' => Auth::user()->name,
                        'fields_group' => "documents",
                        'type' => $type,
                        'time' => $media->created_at,
                    ],
                    'changes' => [],
                ],
                $user->id,
                User::class
            ));
        }

    }

    /**
     * Handle the Media "updated" event.
     *
     * @param  \App\Models\Media  $media
     * @return void
     */
    public function updated(Media $media)
    {
        //
    }

    /**
     * Handle the Media "deleted" event.
     *
     * @param  \App\Models\Media  $media
     * @return void
     */
    public function deleted(Media $media)
    {
        $user = User::find($media->model_id);

        if($media->model_type == "App\Models\User"){
            switch ($media->collection_name) {
                case 'business_documents':
                    $type = 'business';
                    break;

                case 'bank_documents':
                    $type = 'bank';
                    break;

                default:
                    # code...
                    break;
            }
            event(new AddActionLogEvent(
                'user_delete_decouments',
                Auth::id(),
                [
                    'message' => [
                        'username' => $user->name,
                        'adminname' => Auth::user()->name,
                        'fields_group' => "documents",
                        'type' => $type,
                        'time' => $media->created_at,
                    ],
                    'changes' => [],
                ],
                $user->id,
                User::class
            ));
        }
    }

    /**
     * Handle the Media "restored" event.
     *
     * @param  \App\Models\Media  $media
     * @return void
     */
    public function restored(Media $media)
    {
        //
    }

    /**
     * Handle the Media "force deleted" event.
     *
     * @param  \App\Models\Media  $media
     * @return void
     */
    public function forceDeleted(Media $media)
    {
        //
    }
}
