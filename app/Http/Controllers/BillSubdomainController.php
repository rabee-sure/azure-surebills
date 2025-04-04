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

}
