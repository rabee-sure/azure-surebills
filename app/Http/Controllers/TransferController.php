<?php

namespace App\Http\Controllers;

use App\Events\TransferCreated;
use App\Http\Resources\TransferResource;
use App\Models\Bank;
use App\Models\Bill;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return new TransferResource($transfer);
    }

}
