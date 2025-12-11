<?php

namespace App\Http\Controllers\Api;

use App\Models\Bill;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;

class ChartsController extends Controller
{
    protected $dniw;
    protected $weeks;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->dniw = date('w');
        $this->weeks = collect($this->getWeekAndDays(date("Y"), date("m")));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function billsPaidAmount(Request $request)
    {
        $user = User::find($request->user_id);
        $userIds = User::whereIn('id', [$user->id, $user->store_main_user_id])-> pluck('id')->toArray();

        $collection = [];

        switch ($request->mode) {
            case 'daily':
                $collection['daily'] = Bill::whereIn('user_id', $userIds)
                ->where('status', 'paid')
                ->where(DB::raw("WEEK(paid_at)"), '=', DB::raw('WEEK(now())'))
                ->select(DB::raw("DAYOFWEEK(paid_at) AS Day, SUM(total) AS Total"))
                ->groupby('Day')
                ->orderby('Day')
                ->pluck('Total', 'Day');
                break;

            case 'weekly':
                $collection['weekly'] = Bill::whereIn('user_id', $userIds)
                ->where('status', 'paid')
                ->where(DB::raw("MONTH(paid_at)"), '=', DB::raw('MONTH(now())'))
                ->select(DB::raw("WEEK(paid_at) AS Week, SUM(total) AS Total"))
                ->groupby('Week')
                ->orderby('Week')
                ->pluck('Total', 'Week');
                break;

            case 'monthly':
                $collection['monthly'] = Bill::whereIn('user_id', $userIds)
                ->where('status', 'paid')
                ->where(DB::raw("Year(paid_at)"), '=', date('Y'))
                ->select(DB::raw("MONTH(paid_at) AS Month, SUM(total) AS Total"))
                ->groupby('Month')
                ->orderby('Month')
                ->pluck('Total', 'Month');
                break;
            
            default:
                # code...
                break;
        }
  
        return $this->datasets($collection, [
            'label' => __('The amount of the payments'),
            'backgroundColor' => 'rgba(224, 123, 57, 0.51)',
            'borderColor' => 'rgb(224, 123, 57)',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function billsPaidCount(Request $request)
    {
        $user = User::find($request->user_id);
        $userIds = User::whereIn('id', [$user->id, $user->store_main_user_id])-> pluck('id')->toArray();

        $collection = [];

        switch ($request->mode) {
            case 'daily':
                $collection['daily'] = Bill::whereIn('user_id', $userIds)
                ->where('status', 'paid')
                ->where(DB::raw("WEEK(paid_at)"), '=', DB::raw('WEEK(now())'))
                ->select(DB::raw("DAYOFWEEK(paid_at) AS Day, COUNT(id) AS BillCounter"))
                ->groupby('Day')
                ->orderby('Day')
                ->pluck('BillCounter', 'Day');
                break;

            case 'weekly':
                $collection['weekly'] = Bill::whereIn('user_id', $userIds)
                ->where('status', 'paid')
                ->where(DB::raw("MONTH(paid_at)"), '=', DB::raw('MONTH(now())'))
                ->select(DB::raw("WEEK(paid_at) AS Week, COUNT(id) AS BillCounter"))
                ->groupby('Week')
                ->orderby('Week')
                ->pluck('BillCounter', 'Week');
                break;

            case 'monthly':
                $collection['monthly'] = Bill::whereIn('user_id', $userIds)
                ->where('status', 'paid')
                ->where(DB::raw("Year(paid_at)"), '=', date('Y'))
                ->select(DB::raw("MONTH(paid_at) AS Month, COUNT(id) AS BillCounter"))
                ->groupby('Month')
                ->orderby('Month')
                ->pluck('BillCounter', 'Month');
                break;
            
            default:
                # code...
                break;
        }

        return $this->datasets($collection, [
            'label' => __('The number of bills paid'),
            'backgroundColor' => 'rgb(25, 121, 169, 0.51)',
            'borderColor' => 'rgb(25, 121, 169)',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function billsCount(Request $request)
    {
        $user = User::find($request->user_id);
        $userIds = User::whereIn('id', [$user->id, $user->store_main_user_id])-> pluck('id')->toArray();

        $collection = [];

        switch ($request->mode) {
            case 'daily':
                $collection['daily'] = Bill::whereIn('user_id', $userIds)
                ->where(DB::raw("WEEK(created_at)"), '=', DB::raw('WEEK(now())'))
                ->select(DB::raw("DAYOFWEEK(created_at) AS Day, COUNT(id) AS BillCounter"))
                ->groupby('Day')
                ->orderby('Day')
                ->pluck('BillCounter', 'Day');
                break;

            case 'weekly':
                $collection['weekly'] = Bill::whereIn('user_id', $userIds)
                ->where(DB::raw("MONTH(created_at)"), '=', DB::raw('MONTH(now())'))
                ->select(DB::raw("WEEK(created_at) AS Week, COUNT(id) AS BillCounter"))
                ->groupby('Week')
                ->orderby('Week')
                ->pluck('BillCounter', 'Week');
                break;

            case 'monthly':
                $collection['monthly'] = Bill::whereIn('user_id', $userIds)
                ->where(DB::raw("Year(created_at)"), '=', date('Y'))
                ->select(DB::raw("MONTH(created_at) AS Month, COUNT(id) AS BillCounter"))
                ->groupby('Month')
                ->orderby('Month')
                ->pluck('BillCounter', 'Month');
                break;
            
            default:
                # code...
                break;
        }

        return $this->datasets($collection, [
            'label' => __('Total bills'),
            'backgroundColor' => 'rgb(255, 99, 132, 0.51)',
            'borderColor' => 'rgb(255, 99, 132)',
        ]);
    }

    protected function getWeekAndDays($year,$month, $day=1)
    {
        $list= [];
        $first_date = $year."-".$month."-".$day;
        $last_date = date("Y-m-t",strtotime($first_date));
        $first_week = date("W",strtotime($first_date));
        $last_week = date("W",strtotime($last_date));

        //in new year somtimes get week from old year
        if($first_week > $last_week){
            $old_first_week = $first_week ;
            $first_week = 0;
        }

        for($i=$first_week;$i<=$last_week;$i++){
            $week_number = $i;
            if(isset($old_first_week) && $i == 0){
                $week_number = $old_first_week;
            }
            $list[]= [
                'number' => (string) $week_number,
                'date_between' => [
                    $this->daysInWeek($week_number)[0],
                    $this->daysInWeek($week_number)[7],
                ],
                'first_day' => $this->daysInWeek($week_number)[0],
                'last_day' => $this->daysInWeek($week_number)[6],
            ];
        }
        return $list;
    }

    protected function daysInWeek($weekNum)
    {
        $result = array();
        $datetime = new \DateTime('00:00:00');
        $datetime->setISODate((int)$datetime->format('o'), $weekNum, 1);
        $interval = new \DateInterval('P1D');
        $week = new \DatePeriod($datetime, $interval, 7);

        foreach($week as $day){
            $result[] = $day->format('Y-m-d');
        }
        return $result;
    }

    /**
     * generate Datasets
     *
     * @param  elquent  $collection
     * @return array
     */
    protected function datasets($collection, $data)
    {

        return [
            'daily' => [
                "labels" => [
                    __('Sunday'),
                    __('Monday'),
                    __('Tuesday'),
                    __('Wednesday'),
                    __('Thursday'),
                    __('Friday'),
                    __('Saturday'),
                ],
                "datasets" => [
                    [
                        'label' => $data['label'],
                        'backgroundColor' => $data['backgroundColor'],
                        'borderColor' => $data['borderColor'],
                        'data'=> [
                            ($collection['daily'] ?? [])[1] ?? 0,
                            ($collection['daily'] ?? [])[2] ?? 0,
                            ($collection['daily'] ?? [])[3] ?? 0,
                            ($collection['daily'] ?? [])[4] ?? 0,
                            ($collection['daily'] ?? [])[5] ?? 0,
                            ($collection['daily'] ?? [])[6] ?? 0,
                            ($collection['daily'] ?? [])[7] ?? 0,
                        ]
                    ]
                ],
            ],
            'weekly' => [
                "labels" => $this->weeks->pluck('number'),
                "datasets" => [
                    [
                        'label' => $data['label'],
                        'backgroundColor' => $data['backgroundColor'],
                        'borderColor' => $data['borderColor'],
                        'data'=> $this->weeks->map(function ($week) use ($collection) {
                            return ($collection['weekly'] ?? [])[(int) $week['number']] ?? 0;
                        })->values()->toArray(),
                    ]
                ],
            ],
            'monthly' => [
                "labels" => [
                    __('January'),
                    __('February'),
                    __('March'),
                    __('April'),
                    __('May'),
                    __('June'),
                    __('July'),
                    __('August'),
                    __('September'),
                    __('October'),
                    __('November'),
                    __('December'),
                ],
                "datasets" => [
                    [
                        'label' => $data['label'],
                        'backgroundColor' => $data['backgroundColor'],
                        'borderColor' => $data['borderColor'],
                        'data'=> [
                            ($collection['monthly'] ?? [])[1] ?? 0,
                            ($collection['monthly'] ?? [])[2] ?? 0,
                            ($collection['monthly'] ?? [])[3] ?? 0,
                            ($collection['monthly'] ?? [])[4] ?? 0,
                            ($collection['monthly'] ?? [])[5] ?? 0,
                            ($collection['monthly'] ?? [])[6] ?? 0,
                            ($collection['monthly'] ?? [])[7] ?? 0,
                            ($collection['monthly'] ?? [])[8] ?? 0,
                            ($collection['monthly'] ?? [])[9] ?? 0,
                            ($collection['monthly'] ?? [])[10] ?? 0,
                            ($collection['monthly'] ?? [])[11] ?? 0,
                            ($collection['monthly'] ?? [])[12] ?? 0,
                        ],
                    ]
                ],
            ],
        ];
    }
}
