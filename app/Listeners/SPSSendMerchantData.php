<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SPSSendMerchantData
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
     * @param  \App\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        //Get merchant account data
        $user = $event->user;

        //prepare data of object
        $data = array();
        $data['MerchantNameAr'] = $user->name;
        $data['MerchantNameEn'] = $user->name;
        $data['CommercialNameAr'] = $user->business_name_ar;
        $data['CommercialNameEn'] = $user->business_name_en;
        $data['CRNumber'] = $user->cr_number;
        $data['Email'] = $user->email;
        $data['Mobile'] = $user->mobile;
        $data['BeneficiaryBank'] = $user->bank->name;
        $data['BeneficiaryName'] = $user->beneficiary_name;
        $data['BeneficiaryIban'] = $user->iban_number;
        $data['BeneficiaryCity'] = $user->city;
        $data['BeneficiaryStreet'] = $user->street_name;
        //Send merchant account data to sps api
    }
}
