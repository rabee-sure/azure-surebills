<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Channel;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date_start = $request->date_start ?? Carbon::today()->firstOfMonth()->format('m/d/Y');
        $date_to = $request->date_to ?? Carbon::today()->format('m/d/Y');
        
        $channel = ($request->has('channel_id') && !in_array($request->channel_id, ['all','undefined']))? Channel::find($request->channel_id) : null;
        $application = ($request->has('application_id') && !in_array($request->application_id, ['all','undefined']))? Application::find($request->application_id) : null;

        $statement = auth()->user()
            ->statement()
            ->when($date_start, function($q) use($date_start, $date_to){
                $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                    ->whereDate('created_at', '<=', Carbon::parse($date_to));
            })
            ->when($request->transaction_type == 'debit' || $request->transaction_type == 'credit', function($q) use($request){
                $q->whereType($request->transaction_type);
            })
            ->when(isset($request->transaction_source) && $request->transaction_source != 'all' && $request->transaction_source != 'undefined', function($q) use($request){
                $q->whereTransactionSource($request->transaction_source);
            })
            ->when($channel, function($q) use($channel){
                $q->whereHas('bill.application', function ( $query) use($channel){
                    $query->where('channel_id', $channel->id);
                });
            })
            ->when($application, function($q) use($application){
                $q->whereHas('bill', function ( $query) use($application){
                    $query->where('application_id', $application->id);
                });
            })
            ->get();

        $channels = auth()->user()->channels;
        $applications = ($channel) ? $channel->applications : [];
        return view('statements.index', compact('statement', 'date_start', 'date_to', 'channels', 'channel', 'applications', 'application'));
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
        //
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
