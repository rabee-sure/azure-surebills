<?php

namespace App\Http\Controllers;

use App\Bill;
use App\Events\BillPaid;
use App\Events\BillStatusUpdated;
use App\Http\Requests\AccountInformationRequest;
use App\Http\Requests\BankInformationRequest;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\Jobs\CallbackWebhook;
use App\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function test(Request $request)
    {
        $bill = Bill::find('08774b43-fef5-4864-9fbf-2a3352ce646d');
        $log = $bill->payment_logs->last();
        dd($log->results['description']);
        CallbackWebhook::dispatch($bill);
        
        return view('emails.auth.passwords.reset_password');
        $bill = Bill::all()->random();
        if($request->has('id')){
            $bill = Bill::find($request->get('id'));
        }
        event( new BillStatusUpdated($bill) );
        dd($bill);
    } 


 
}
