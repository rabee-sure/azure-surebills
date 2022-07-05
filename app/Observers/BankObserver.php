<?php

namespace App\Observers;

use App\Models\Bank;
use App\Events\AddActionLogEvent;
use Illuminate\Support\Facades\Auth;

class BankObserver
{
    /**
     * Handle the Bank "created" event.
     *
     * @param  \App\Models\Bank  $bank
     * @return void
     */
    public function created(Bank $bank)
    {
        if(Auth::guard('admins')->check()){
            event(new AddActionLogEvent(
                'create_bank',
                Auth::id(),
                [
                    'message' => [
                        'adminname' => Auth::user()->name,
                        'time' => $bank->created_at,
                    ],
                    'changes' => [],
                ],
                $bank->id,
                Bank::class
            ));
        }
    }

    /**
     * Handle the Bank "updated" event.
     *
     * @param  \App\Models\Bank  $bank
     * @return void
     */
    public function updated(Bank $bank)
    {
        if(Auth::guard('admins')->check()){
            $fields = config('bankfields');
    
            $fieldsChanges = [];
            foreach($fields as $field){
                if($bank->isDirty($field)){
                    $fieldsChanges[$field] = [
                        'old_value' => $bank->getOriginal($field),
                        'new_value' => $bank->$field
                    ];
                }
            }
            event(new AddActionLogEvent(
                'update_bank', 
                Auth::id(), 
                [
                    'message' => [
                        'adminname' => Auth::user()->name,
                        'time' => $bank->updated_at,
                    ],
                    'changes' => $fieldsChanges,
                ], 
                $bank->id, 
                Bank::class
            ));
        }
    }

    /**
     * Handle the Bank "deleted" event.
     *
     * @param  \App\Models\Bank  $bank
     * @return void
     */
    public function deleted(Bank $bank)
    {
        if(Auth::guard('admins')->check()){
            event(new AddActionLogEvent(
                'delete_bank',
                Auth::id(),
                [
                    'message' => [
                        'adminname' => Auth::user()->name,
                        'time' => $bank->created_at,
                    ],
                    'changes' => [],
                ],
                $bank->id,
                Bank::class
            ));
        }
    }

    /**
     * Handle the Bank "restored" event.
     *
     * @param  \App\Models\Bank  $bank
     * @return void
     */
    public function restored(Bank $bank)
    {
        //
    }

    /**
     * Handle the Bank "force deleted" event.
     *
     * @param  \App\Models\Bank  $bank
     * @return void
     */
    public function forceDeleted(Bank $bank)
    {
        //
    }
}
