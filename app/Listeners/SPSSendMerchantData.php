<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use App\Events\UserUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

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
     * @param  \App\Events\UserUpdated  $event
     * @return void
     */
    public function handle(UserUpdated $event)
    {
        //Get merchant account data
        $user = $event->user;

        //prepare data of object
        $data = array();
        $data['userId'] = $user->id;
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
        $data['ExternalMerchantId'] = $user->id;
        $data['Verified'] = $user->verified;

        //Send merchant account data to sps api
        $link = config('sps.base_url').'/'.config('sps.routes.save_merchants');
        $client = new Client;
        $response = $client->request('POST', $link, [
            'body' => json_encode($data),
            // 'curl' => [CURLOPT_SSLVERSION => CURL_SSLVERSION_SSLv3]
        ]);

        //Log Api faild response
        log::info('SPS response', json_decode($response->getBody()));
        return $response;
    }
}
