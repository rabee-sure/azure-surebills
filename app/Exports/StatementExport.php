<?php

namespace App\Exports;

use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StatementExport implements FromView
{
    /**
    * @return \Illuminate\Support\View
    */
    public function view(): View
    {

        $channels = auth()->user()->channels;

        return view('statements.export', [
            'channels' => $channels,
            'statement' => auth()->user()->getStatement()
        ]);
    }
}
