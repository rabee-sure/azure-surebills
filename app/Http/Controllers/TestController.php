<?php

namespace App\Http\Controllers;

use App\Bill;
use App\Events\BillPaid;
use App\Http\Requests\AccountInformationRequest;
use App\Http\Requests\BankInformationRequest;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\UserResource;
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
    public function test()
    {
        $bill = Bill::find('44986673-55c1-4406-964b-f4a0bf83ce6a');
        $url = $bill->application->redirect.'?reference_id='.$bill->reference_id.'&status='.$bill->status;
        return redirect($url);
    } 

    public function decode(string $hex): string
    {
        return array_reduce([20, 16, 12, 8], function ($uuid, $offset) {
            return substr_replace($uuid, '-', $offset, 0);
        }, str_pad($hex, 32, '0', STR_PAD_LEFT));
    }
 
}
