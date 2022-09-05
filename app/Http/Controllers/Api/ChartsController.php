<?php

namespace App\Http\Controllers\Api;

use App\Models\Bill;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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
        $collection = Bill::whereIn('user_id', $userIds)->get();

        $monthly = Bill::whereIn('user_id', $userIds)
        ->where('status', 'paid')
        ->select(DB::row());

        return $this->datasets($collection, 'getSumTotalBetweenDate', [
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
        $collection = Bill::whereIn('user_id', $userIds)->paid()->get();
        return $this->datasets($collection, 'getCountBetweenDate', [
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
        $collection = Bill::whereIn('user_id', $userIds)->get();
        return $this->datasets($collection, 'getCountBetweenDate', [
            'label' => __('Total bills'),
            'backgroundColor' => 'rgb(255, 99, 132, 0.51)',
            'borderColor' => 'rgb(255, 99, 132)',
        ]);
    }

    protected function getSumTotalBetweenDate($collection, $from, $to)
    {
        return (string) $collection->whereBetween('paid_at', [
            $from,
            $to
        ])->where('status', 'paid')->sum('total');
    }

    protected function getCountBetweenDate($collection, $from, $to)
    {
        return (string) $collection->whereBetween('created_at', [
            $from,
            $to
        ])->count();
    }

    protected function getCountPaidBetweenDate($collection, $from, $to)
    {
        return (string) $collection->whereBetween('paid_at', [
            $from,
            $to
        ])->count();
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
    protected function datasets($collection, $method, $data)
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
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime('-'.$this->dniw.' days')),
                                date('Y-m-d', strtotime('+'.(1-$this->dniw).' days'))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime('+'.(1-$this->dniw).' days')),
                                date('Y-m-d', strtotime('+'.(2-$this->dniw).' days'))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime('+'.(2-$this->dniw).' days')),
                                date('Y-m-d', strtotime('+'.(3-$this->dniw).' days'))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime('+'.(3-$this->dniw).' days')),
                                date('Y-m-d', strtotime('+'.(4-$this->dniw).' days'))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime('+'.(4-$this->dniw).' days')),
                                date('Y-m-d', strtotime('+'.(5-$this->dniw).' days'))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime('+'.(5-$this->dniw).' days')),
                                date('Y-m-d', strtotime('+'.(6-$this->dniw).' days'))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime('+'.(6-$this->dniw).' days')),
                                date('Y-m-d', strtotime('+'.(6-$this->dniw).' days'))
                            ),
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
                        'data'=> [
                            $this->{$method}($collection,
                                $this->weeks[0]['date_between'][0],
                                $this->weeks[0]['date_between'][1]
                            ),
                            $this->{$method}($collection,
                                $this->weeks[1]['date_between'][0],
                                $this->weeks[1]['date_between'][1]
                            ),
                            $this->{$method}($collection,
                                $this->weeks[2]['date_between'][0],
                                $this->weeks[2]['date_between'][1]
                            ),
                            $this->{$method}($collection,
                                $this->weeks[3]['date_between'][0],
                                $this->weeks[3]['date_between'][1]
                            ),
                            $this->{$method}($collection,
                                $this->weeks[4]['date_between'][0] ?? '',
                                $this->weeks[4]['date_between'][1] ?? ''
                            ),
                        ]
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
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-1-1'))),
                                date('Y-m-d', strtotime(date('Y-2-1')))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-2-1'))),
                                date('Y-m-d', strtotime(date('Y-3-1')))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-3-1'))),
                                date('Y-m-d', strtotime(date('Y-4-1')))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-4-1'))),
                                date('Y-m-d', strtotime(date('Y-5-1')))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-5-1'))),
                                date('Y-m-d', strtotime(date('Y-6-1')))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-6-1'))),
                                date('Y-m-d', strtotime(date('Y-7-1')))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-7-1'))),
                                date('Y-m-d', strtotime(date('Y-8-1')))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-8-1'))),
                                date('Y-m-d', strtotime(date('Y-9-1')))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-9-1'))),
                                date('Y-m-d', strtotime(date('Y-10-1')))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-10-1'))),
                                date('Y-m-d', strtotime(date('Y-11-1')))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-11-1'))),
                                date('Y-m-d', strtotime(date('Y-12-1')))
                            ),
                            $this->{$method}($collection,
                                date('Y-m-d', strtotime(date('Y-12-1'))),
                                date('Y-m-d', strtotime(date('Y-12-31')))
                            ),
                        ],
                    ]
                ],
            ],
        ];
    }
}
