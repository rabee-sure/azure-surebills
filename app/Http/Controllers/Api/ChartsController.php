<?php

namespace App\Http\Controllers\Api;

use App\Application;
use App\Events\UserCreated;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserStatResource;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChartsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bills(Request $request)
    {
        $carbon = $this->getWeekAndDays(2020, 2);

        

        dd( $carbon);
        return [
            'daily' => [
                "labels" => [],
                "datasets" => [],
            ],
            'weekly' => [
                "labels" =>[],
                "datasets" => [],
            ],
            'monthly' => [
                "labels" =>[],
                "datasets" => [],
            ],
        ];
    }

    public function getWeekAndDays($year, $month)
    {
        $weeksInMonth = Carbon::createFromDate($year, $month)->endOfMonth()->weekOfMonth;   
        $weekBegin = Carbon::createFromDate($year, $month)->startOfMonth();

        dd(Carbon::now()->weekOfYear());
        $weeks = [];

        for($i=1; $i<=$weeksInMonth; $i++)
        {
            $weeks[] = $i;
        }

        return $weeks;
    }
}
