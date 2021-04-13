<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bank;
use App\Models\Bill;
use App\Models\Transfer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Events\TransferCreated;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TransferResource;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('transfers.index', ['transfers' => auth()->user()->transfers]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bills(Transfer $transfer, Request $request)
    {
        return view('transfers.bills', [
            'transfer' => $transfer,
            'bills' => $transfer->bills,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function request(Request $request)
    {
        $from = $this->getToDate($user);
        $bills = $this->getbillsBetweenDate($from, $to, $user);
        $amount = $this->getAmount($bills, $user);
        $this->info("transfer to $user->name amount: $amount");

        $transfer = DB::transaction(function () use($user, $bills, $amount){
            $bank = $user->bank;
            $transfer = Transfer::create([
                'status' => 'pending',
                'user_id' => $user->id,
                'amount' => $amount,
                'transfer_fees' => $bank->fees+ ($bank->fees * 0.15),
                'net_amount' => $amount - $bank->fees+ ($bank->fees * 0.15),
                'note' => 'automatic transfer',
                'created_by_id' => null,
                'bank_id' => $bank->id,
                'iban_number' => $user->iban_number,
                'beneficiary_name' => $user->beneficiary_name,
                'filters' => [
                    'date' => [
                        "from" => '',
                        "to" => '',
                    ]
                ],
            ]);

            $transfer->bills()->attach($bills->pluck('id')->toArray());

            return $transfer;
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $transfers = Transfer::orderBy('id', 'desc')
            ->where('status', 'pending')
            ->orWhereNull('attachment')
            ->paginate($request->per_page);

        return TransferResource::collection($transfers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fromDate = new Carbon($request->from);
        $fromDate = $fromDate->addDays(1);
        $toDate = new Carbon($request->to);
        $toDate = $toDate->addDays(1);

        $transfer = DB::transaction(function () use($request, $fromDate, $toDate){
            $bank = Bank::find($request->bank_id);
            $transfer = Transfer::create([
                'user_id' => $request->user_id,
                'amount' => $request->amount,
                'transfer_fees' => $bank->fees+ ($bank->fees * 0.15),
                'net_amount' => $request->amount - $bank->fees+ ($bank->fees * 0.15),
                'note' => $request->note,
                'attachment' => $request->attachment,
                'created_by_id' => auth()->user()->id,
                'bank_id' => $request->bank_id,
                'iban_number' => $request->iban_number,
                'beneficiary_name' => $request->beneficiary_name,
                'filters' => [
                    'date' => [
                        "from" => $fromDate,
                        "to" => $toDate,
                    ]
                ],
            ]);

            foreach ($request->bills_ids as $bill_id) {
                $bill = Bill::find($bill_id);
                if($bill->user_id == $request->user_id){
                    $bill->settled = true;
                }

                if($bill->isHaveChannelOwenByUser($request->user_id)){
                   $bill->channel_settled = true; 
                }
                $bill->save();
            }
            $transfer->bills()->attach($request->bills_ids);

            return $transfer;
        });
        event(new TransferCreated($transfer));

        return new TransferResource($transfer);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function show(Transfer $Transfer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function edit(Transfer $Transfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transfer $Transfer)
    {
        //
    }


    /**
     * change Status.
     *
     * @param  \App\Models\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request, Transfer $transfer)
    {
        $transfer->status = $request->status;
        $transfer->save();
        if($request->status == 'completed'){

            $bankCode   = $transfer->user->bank ? $transfer->user->bank->code : '-';
            $bankNumber = substr($transfer->user->iban_number, -4);
    
            $transaction = new Transaction;
            $transaction->user_id     = $transfer->user_id;
            $transaction->type        = 'debit';
            $transaction->amount      = $transfer->amount;
            $transaction->reference   = $transfer->id;
            $transaction->description = 'Transfer - ' . $bankCode . ' XXXX' . $bankNumber;
            $transaction->transaction_source = 'transfer';
            $transaction->save();

            $bills = $transfer->bills;
            $user_id = $transfer->user_id;
            foreach ($bills as $bill) {
                if($bill->user_id == $user_id){
                    $bill->settled = true;
                }

                if($bill->isHaveChannelOwenByUser($user_id)){
                   $bill->channel_settled = true; 
                }
                $bill->save();
            }
            event(new TransferCreated($transfer));
        }
        return new TransferResource($transfer);
    }

}
