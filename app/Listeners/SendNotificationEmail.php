<?php

namespace App\Listeners;

use App\Events\UserUpdateNotification;
use App\Mail\SendUpdatedUserNotification;
use App\Models\Bank;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendNotificationEmail implements ShouldQueue
{
    use IsMonitored;
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
     * @param  \App\Events\UserUpdateNotification  $event
     * @return void
     */
    public function handle(UserUpdateNotification $event)
    {
        $data['changes'] = [];
        $data['user'] = $event->user_id;
        $data['mode'] = $event->mode;

        $dirtyFields = $this->getDiff(Arr::except($event->oldData, 'documents'), Arr::except($event->updatedData, 'documents'));
        foreach($dirtyFields as $field){
            $line = $field.' change from '.$this->getValue($field, $event->oldData[$field]).' to '.$this->getValue($field, $event->updatedData[$field]);
            array_push($data['changes'], $line);
        }

        $diffDocs = false;
        if(Arr::has($event->oldData, 'documents') || Arr::has($event->updatedData, 'documents')){
            $diffDocs = $this->getDiffDocs(Arr::only($event->oldData, 'documents'), Arr::only($event->updatedData, 'documents'));
        }
        if($diffDocs){
            $data['documents']['old'] = $event->oldData['documents'];
            $data['documents']['updated'] = $event->updatedData['documents'];
        }

        if(!empty($data['changes']) || (isset($data['documents']) && !empty($data['documents']))){
            Mail::to('mzain@sure.com.sa')->send(new SendUpdatedUserNotification($data));
        }

    }

    private function getDiff($old, $updated){
        return array_keys(array_diff($old, $updated));
    }

    private function getDiffDocs($old, $updated){
        // Sort the array elements
        sort($old['documents']);
        sort($updated['documents']);
        
        // Check for equality
        if($old['documents'] == $updated['documents']){
            return false;
        }else{
            return true;
        }
    }

    private function getValue($field, $value){
        if($value == null){
            $value = 'Nan';
        }elseif(is_bool($value)){
            $value = $value ? 1 : 0;
        }elseif($field == 'bank_id'){
            $bank = Bank::find($value);
            $value = $bank->name;
        }else{
            $readableValue = config('UserFieldsValues.'.$field.'.'.$value);
            if($readableValue != null){
                $value = $readableValue;
            }
        }

        return $value;
    }
}
