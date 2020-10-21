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
        ->get();

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
        

        $transactions = $user->transactions()
            ->when($request->from, function ($query) use($from, $to) {
                return $query->whereBetween('created_at', [$from, $to]);
            })
            ->when($request->bills_not_settled, function ($query) use($request){
                return $query->whereHas('bill', function ( $q) {
                    $q->where('settled', false);
                });
            })
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
        $bills = $user->bills()
            ->when($request->from, function ($query) use($request){
                return $query->whereBetween('created_at', [$request->from, $request->to]);
            })
            ->when($request->not_settled, function ($query) use($request){
                return $query->where('settled', false);
            })
            ->paid()
            ->orderBy('id', 'desc')
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
