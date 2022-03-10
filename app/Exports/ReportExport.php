<?php

namespace App\Exports;

use App\Report;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;

class ReportExport implements FromView
{
    use Exportable;

    protected $data;

    public function __construct( $data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\View
    */
    public function view(): View
    {
        return view('exports.reports.merchants-outstanding', [
            'data' => $this->data
        ]);
    }
}