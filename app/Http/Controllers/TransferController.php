<?php

namespace App\Http\Controllers;

use App\Events\TransferCreated;
use App\Http\Resources\TransferResource;
use App\Transfer;
use Illuminate\Http\Request;

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
        $sett = Transfer::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'created_by_id' => auth()->user()->id,
        ]);
        event(new TransferCreated($sett));
        return new TransferResource($sett);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function show(Transfer $Transfer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transfer  $Transfer
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
     * @param  \App\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transfer $Transfer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transfer $Transfer)
    {
        //
    }
}
