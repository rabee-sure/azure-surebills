<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        
        \Log::error('I am here');
        \Log::error($request->all());
        \Log::error('transaction id = ' . session()->get('transaction_id'));
        
    }

}
