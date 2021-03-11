<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Http\Resources\BillResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\TransferResource;
use App\Http\Resources\UserResource;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
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
        $bills = $this->getbills($request, $user);
        $billsids = $bills->pluck('id')->toArray();

        $transactions = Transaction::whereIn('bill_id', $billsids)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'ASC')
            ->orderBy('receipt', 'ASC')
            ->get();

        return (TransactionResource::collection($transactions))->additional(['meta' => [
                'balance' => round($transactions->where('type', 'credit')->sum('amount')-$transactions->where('type', 'debit')->sum('amount'), 2),
            ]]);;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bills(Request $request, User $user)
    {
        $bills = $this->getbills($request, $user);
        return BillResource::collection($bills);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function getbills($request, $user)
    {
        $from = Carbon::parse($request->from)->toDateTimeString();
        $to = Carbon::parse($request->to);

        $start_day = $to->copy()->startOfDay();
        if($start_day != $to)
            $to = $to->copy()->toDateTimeString();
        else
            $to = $to->copy()->endOfDay()->toDateTimeString();

        $request->request->add(['channel_user_id' => $user->id]);

        return Bill::
            //get user bills
            where(function ($query) use($user, $request, $from, $to){
                $query->where('user_id', $user->id)
                    ->paid()
                    ->when($request->from, function ($query) use($from, $to) {
                        return $query->whereBetween('paid_at', [$from, $to]);
                    })
                    ->when($request->not_settled || $request->bills_not_settled, function ($query) use($request){
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
                    ->when($request->not_settled || $request->bills_not_settled, function ($query) use($request){
                        return $query->where('channel_settled', false);
                    });
            })
            
            ->orderBy('paid_at', 'asc')
            ->get();
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
