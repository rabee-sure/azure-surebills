<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BillSubdomainController extends Controller
{
    public function verifyOwnershipForApplePay($file)
    {
        if($file === 'apple-developer-merchantid-domain-association.txt')
        {
            return file_get_contents(public_path('.well-known/apple-developer-merchantid-domain-association.txt'));
        }
        
        abort(404);
    }

    public function cybersourceReturn(Request $request){
        
        $data = json_encode($request->all());
        \Log::build(['driver' => 'single', 'path' => storage_path('logs/return-enrollement-log' . '.log'), 'level' => 'debug'])->error($data);
        $card = json_encode(Cache::get('card_data'));
        \Log::build(['driver' => 'single', 'path' => storage_path('logs/return-enrollement-log' . '.log'), 'level' => 'debug'])->error($card);

        return redirect()->route('contact');
 

        // dd(session()->get('card_data'));

        // dd('cookie = '. Cookie::get('transaction_id'), 'session = '.session()->get('transaction_id'));
        
    }

    public function cybersourceReturn2(Request $request){

        // dd('cookie = '. Cookie::get('transaction_id'), 'session = '.session()->get('transaction_id'));

    }

}
