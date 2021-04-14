<?php

namespace App\Services;

use App\Events\TransferCreated;
use App\Exports\BillsExport;
use App\Http\Resources\BillResource;
use App\Mail\AutoTransferMail;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class TransferService 
{
    /**
     * get To Date foryouser.
     *
     * @return Carbon\Carbon
     */
    public static function getFromDate($user)
    {
        $last_transfer = $user->transfers()->latest()->first();
        
        if(isset($last_transfer) && isset($last_transfer->filters['date']['to'])){
            return Carbon::parse($last_transfer->filters['date']['to']);
        }else{
            return $user->created_at;
        }
    }

    /**
     * get bills Between Date.
     *
     * @return \Illuminate\Http\Response
     */
    public static function getbillsBetweenDate($from_s, $to_s, $user)
    {
        $to = $to_s->copy()->endOfDay()->toDateTimeString();
        $from = $from_s->copy()->startOfDay()->toDateTimeString();

        $bills =  Bill::
            //get user bills
            where(function ($query) use($user, $from, $to){
                // dd($from);
                $query->where('user_id', $user->id)
                    ->paid()
                    ->whereBetween('paid_at', [$from, $to])
                    ->where('settled', false);
            })
            //get user "channels" bills
            ->orWhere(function ($query) use($user, $from, $to){
                $query->whereIn('application_id', $user->channelsApplications->pluck('id')->toArray())
                    ->paid()
                    ->whereBetween('paid_at', [$from, $to])
                    ->where('channel_settled', false);
            })
            
            ->orderBy('paid_at', 'asc')
            ->get();

            self::createExcel($bills, $user, $from, $to, $to_s->timestamp);
        return $bills;

    }


    /**
     * get Amount.
     *
     * @param  App\Bill  $bills
     * @param  App\User  $user
     * @return double
     */
    public static function getAmount($bills, $user)
    {
        $billsids = $bills->pluck('id')->toArray();
        $transactions = Transaction::whereIn('bill_id', $billsids)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'ASC')
            ->orderBy('receipt', 'ASC')
            ->get();
        return round($transactions->where('type', 'credit')->sum('amount')-$transactions->where('type', 'debit')->sum('amount'), 2);
    }    

    /**
     * create Excel.
     *
     * @param  App\Bill  $bills
     * @param  App\User  $user
     * @param  Carbon\Carbon  $from
     * @param  Carbon\Carbon  $to
     * @param  integer  $timestamp
     * @return boolean
     */
    public static function createExcel($bills, $user, $from, $to, $timestamp)
    {
        $title = "bills/$timestamp/Bills-{$user->business_name_en}-FROM-{$from}-TO-{$to}.xlsx";
        $data = json_decode((BillResource::collection($bills))->toJson(), true);
        return Excel::store(new BillsExport($data), $title);
    }
}
