<?php

namespace App\Http\Controllers;

use App\Exports\StatementExport;
use App\Models\Application;
use App\Models\Channel;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

        $statement = auth()->user()->getStatement();
        $channels = auth()->user()->channels;
        $applications = ($channel) ? $channel->applications : [];

        return view('statements.index', compact('statement', 'date_start', 'date_to', 'channels', 'channel', 'applications', 'application'));
    }

    public function export() 
    {
        return Excel::download(new StatementExport, 'statement.xlsx');
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
}
