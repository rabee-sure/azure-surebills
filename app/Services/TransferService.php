<?php

namespace App\Services;

use App\Events\TransferCreated;
use App\Exports\BillsExport;
use App\Exports\TransactionsExport;
use App\Http\Resources\BillResource;
use App\Http\Resources\TransactionResource;
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
    public static function getbillsBetweenDate($from_s, $to_s, $user, $excel_file_name = null)
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

            if($excel_file_name){
                self::createBillsExcel($bills, $excel_file_name);
                self::createTransactionsExcel($bills, $excel_file_name);
            }

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
     * create Bills Excel.
     *
     * @param  App\Bill  $bills
     * @param  string  $file_name
     * @return boolean
     */
    public static function createBillsExcel($bills, $file_name)
    {
        $data = json_decode((BillResource::collection($bills))->toJson(), true);
        return Excel::store(new BillsExport($data), $file_name);
    }

    /**
     * create Excel.
     *
     * @param  App\Bill  $bills
     * @param  string  $file_name
     * @return boolean
     */
    public static function createTransactionsExcel($bills, $file_name)
    {
        $array = explode('/', $file_name);
        $array[2] = 'transactions-'.$array[2];
        $file_name = implode('/', $array);
        logger([$array, $file_name]);
        $transactions = Transaction::whereIn('bill_id', $bills->pluck('id'))->get();
        $data = json_decode((TransactionResource::collection($transactions))->toJson(), true);
        return Excel::store(new TransactionsExport($data), $file_name);
    }
}
