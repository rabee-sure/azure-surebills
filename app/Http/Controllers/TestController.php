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
        $user = User::find(789);
        $bills = $user->bills->pluck('total')->toArray();
        $bills_setteld = $user->bills()->settled()->pluck('total')->toArray();
        $bills_not_settled = $user->bills()->not_settled()->pluck('total')->toArray();

        dd([
            'balance' => $user->balance,
            'bills' => $bills,
            'bills_sum' => array_sum($bills),
            'bills_setteld' => $bills_setteld,
            'bills_setteld_sum' => array_sum($bills_setteld),
            'bills_setteld' => $not_settled,
        ]);
    }



}
