<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Events\TransferCreated;
use App\Http\Resources\TransferResource;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

            $transfer = Transfer::create([
                'user_id' => $request->user_id,
                'amount' => $request->amount,
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
                $bill->settled = true;
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transfer $Transfer)
    {
        //
    }
}
