<?php

namespace App\Http\Controllers;

use App\Bill;
use App\Http\Requests\AccountInformationRequest;
use App\Http\Requests\BankInformationRequest;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\BillResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\TransferResource;
use App\Http\Resources\UserResource;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $users = User::all();
        return UserResource::collection($users);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function transfers(Request $request, User $user)
    {
        $Transfers = $user->Transfers()
        ->orderBy('id', 'desc')
        ->paginate($request->per_page);

        return TransferResource::collection($Transfers);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function transactions(Request $request, User $user)
    {
        $from = Carbon::parse($request->from)->toDateTimeString();
        $to = Carbon::parse($request->to);

        $start_day = $to->copy()->startOfDay();
        if($start_day != $to)
            $to = $to->copy()->toDateTimeString();
        else
            $to = $to->copy()->endOfDay()->toDateTimeString();

        $billsids = Bill::
            //get user bills
            where(function ($query) use($user, $request, $from, $to){
                $query->where('user_id', $user->id)
                    ->paid()
                    ->when($request->from, function ($query) use($from, $to) {
                        return $query->whereBetween('paid_at', [$from, $to]);
                    })
                    ->when($request->not_settled, function ($query) use($request){
                        return $query->where('settled', false);
                    });
            })
            //get user "channels" bills
            ->orWhere(function ($query) use($user, $request, $from, $to){
                $query->whereIn('application_id', $user->channelsApplications->pluck('id')->toArray())
                    ->paid()
                    ->when($request->from, function ($query) use($from, $to) {
                        return $query->whereBetween('paid_at', [$from, $to]);
                    })
                    ->when($request->not_settled, function ($query) use($request){
                        return $query->where('settled', false);
                    });
            })
            ->orderBy('id', 'desc')
            ->get()
            ->pluck('id')
            ->toArray();


        $transactions = Transaction::whereIn('bill_id', $billsids)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'ASC')
            ->orderBy('receipt', 'ASC')
            ->get();

        return (TransactionResource::collection($transactions))->additional(['meta' => [
                'balance' => $transactions->where('type', 'credit')->sum('amount')-$transactions->where('type', 'debit')->sum('amount'),
            ]]);;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bills(Request $request, User $user)
    {
        $from = Carbon::parse($request->from)->toDateTimeString();
        $to = Carbon::parse($request->to);

        $start_day = $to->copy()->startOfDay();
        if($start_day != $to)
            $to = $to->copy()->toDateTimeString();
        else
            $to = $to->copy()->endOfDay()->toDateTimeString();

        $request->request->add(['channel_user_id' => $user->id]);

        $bills = Bill::
            //get user bills
            where(function ($query) use($user, $request, $from, $to){
                $query->where('user_id', $user->id)
                    ->paid()
                    ->when($request->from, function ($query) use($from, $to) {
                        return $query->whereBetween('paid_at', [$from, $to]);
                    })
                    ->when($request->not_settled, function ($query) use($request){
                        return $query->where('settled', false);
                    });
            })
            //get user "channels" bills
            ->orWhere(function ($query) use($user, $request, $from, $to){
                $query->whereIn('application_id', $user->channelsApplications->pluck('id')->toArray())
                    ->paid()
                    ->when($request->from, function ($query) use($from, $to) {
                        return $query->whereBetween('paid_at', [$from, $to]);
                    })
                    ->when($request->not_settled, function ($query) use($request){
                        return $query->where('settled', false);
                    });
            })
            
            ->orderBy('paid_at', 'asc')
            ->get();

        return BillResource::collection($bills);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }
}
