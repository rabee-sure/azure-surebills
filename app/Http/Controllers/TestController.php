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
        $bill = Bill::find('2d47049c-dc24-49ea-8540-ed2822955054');
        app()->setLocale('ar');
            $message = __('Hello :name, You’ve got a new bill of :total SAR, From :business_name, Pay now :url', [
                'total' => round($bill->total, 2), 
                'business_name' => $bill->user->business_name, 
                'name' => $bill->customer_name, 
                'url' => $bill->pay_url
            ]);
            dd( $message);
    } 


 
}
