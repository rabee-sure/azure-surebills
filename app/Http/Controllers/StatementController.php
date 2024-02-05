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
    public function __construct()
    {
        $this->middleware('permission:show statement');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $user->userId = auth()->user()->store_main_user_id ?? auth()->user()->id;
        $date_start = $request->date_start ?? Carbon::today()->firstOfMonth()->format('m/d/Y');
        $date_to = $request->date_to ?? Carbon::today()->format('m/d/Y');
        if (Carbon::parse($date_to)->diffInDays($date_start) > 30){
            return redirect(route('statement.index', [
                'date_start' => $date_start,
                'date_to' => Carbon::parse($date_start)->addDays(30)->format('m/d/Y')
            ]))->with('message', __('You cant export more than one month excel'));
        }
        $channel = ($request->has('channel_id') && !in_array($request->channel_id, ['all','undefined']))? Channel::find($request->channel_id) : null;
        $application = ($request->has('application_id') && !in_array($request->application_id, ['all','undefined']))? Application::find($request->application_id) : null;

        $statementQuery = auth()->user()->getStatement();


        $channels = Channel::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)->get();
        $applications = ($channel) ? $channel->applications : [];

        $totals = [];
        $statementQueryAllCredit = clone $statementQuery;
        $statementQueryAllDebit = clone $statementQuery;
        $statement = $statementQuery->paginate(50);
        $credit = $statementQueryAllCredit->where('type', 'credit')->selectRaw("SUM(amount) AS credit");
        $debit = $statementQueryAllDebit->where('type', 'debit')->selectRaw("SUM(amount) AS debit");

        $totals['credit'] = ($credit->first() != null) ? round2($credit->first()->credit) : 0;
        $totals['debit'] = ($debit->first() != null) ? round2($debit->first()->debit) : 0;
        $totals['all'] = round($totals['credit'] - $totals['debit'], 2);

        return view('statements.index', compact('statement', 'date_start',
            'date_to', 'channels', 'channel', 'applications', 'application','totals'));
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
