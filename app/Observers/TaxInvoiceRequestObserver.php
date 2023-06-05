<?php

namespace App\Observers;

use App\Events\AddActionLogEvent;
use App\Models\TaxInvoiceRequest;
use Illuminate\Support\Facades\Auth;

class TaxInvoiceRequestObserver
{
    /**
     * Handle the TaxInvoiceRequest "created" event.
     *
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return void
     */
    public function created(TaxInvoiceRequest $taxInvoiceRequest)
    {
        //
    }

    /**
     * Handle the TaxInvoiceRequest "updated" event.
     *
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return void
     */
    public function updated(TaxInvoiceRequest $taxInvoiceRequest)
    {
        if(Auth::guard('admins')->check()){
            $fields = config('taxInvoiceRequestfields');
    
            $fieldsChanges = [];
            foreach($fields as $field){
                if($taxInvoiceRequest->isDirty($field)){
                    $fieldsChanges[$field] = [
                        'old_value' => $taxInvoiceRequest->getOriginal($field),
                        'new_value' => $taxInvoiceRequest->$field
                    ];
                }
            }
            event(new AddActionLogEvent(
                'update_tax_invoice_request', 
                Auth::id(), 
                [
                    'message' => [
                        'adminname' => Auth::user()->name,
                        'time' => $taxInvoiceRequest->updated_at,
                    ],
                    'changes' => $fieldsChanges,
                ], 
                $taxInvoiceRequest->id, 
                TaxInvoiceRequest::class
            ));
        }
    }

    /**
     * Handle the TaxInvoiceRequest "deleted" event.
     *
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return void
     */
    public function deleted(TaxInvoiceRequest $taxInvoiceRequest)
    {
        //
    }

    /**
     * Handle the TaxInvoiceRequest "restored" event.
     *
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return void
     */
    public function restored(TaxInvoiceRequest $taxInvoiceRequest)
    {
        //
    }

    /**
     * Handle the TaxInvoiceRequest "force deleted" event.
     *
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return void
     */
    public function forceDeleted(TaxInvoiceRequest $taxInvoiceRequest)
    {
        //
    }
}
