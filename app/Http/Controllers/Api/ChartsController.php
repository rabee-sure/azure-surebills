<?php

namespace App\Http\Controllers\Api;

use App\Models\Bill;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Closure;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;

class ChartsController extends Controller
{
    protected $dniw;
    protected $weeks;

    /** @var SureBillsCache */
    private $cache;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SureBillsCache $cache)
    {
        $this->cache = $cache;
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
        $user = $this->chartUser($request->user_id);
        $collection = $this->rememberChartAggregate($user, 'bills_paid_amount', $request->mode, function () use ($user, $request) {
            return $this->queryBillsPaidAmount($user, $request->mode);
        });

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
        $user = $this->chartUser($request->user_id);
        $collection = $this->rememberChartAggregate($user, 'bills_paid_count', $request->mode, function () use ($user, $request) {
            return $this->queryBillsPaidCount($user, $request->mode);
        });

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
        $user = $this->chartUser($request->user_id);
        $collection = $this->rememberChartAggregate($user, 'bills_count', $request->mode, function () use ($user, $request) {
            return $this->queryBillsCount($user, $request->mode);
        });

        return $this->datasets($collection, [
            'label' => __('Total bills'),
            'backgroundColor' => 'rgb(255, 99, 132, 0.51)',
            'borderColor' => 'rgb(255, 99, 132)',
        ]);
    }

    /**
     * @param  mixed  $userId
     * @return \App\Models\User|null
     */
    protected function chartUser($userId)
    {
        return User::find($userId);
    }

    /**
     * @param  \App\Models\User  $user
     * @param  mixed  $mode
     * @return array
     */
    protected function queryBillsPaidAmount($user, $mode)
    {
        $userIds = User::whereIn('id', [$user->id, $user->store_main_user_id])-> pluck('id')->toArray();

        $collection = [];

        switch ($mode) {
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

        return $collection;
    }

    /**
     * @param  \App\Models\User  $user
     * @param  mixed  $mode
     * @return array
     */
    protected function queryBillsPaidCount($user, $mode)
    {
        $userIds = User::whereIn('id', [$user->id, $user->store_main_user_id])-> pluck('id')->toArray();

        $collection = [];

        switch ($mode) {
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

        return $collection;
    }

    /**
     * @param  \App\Models\User  $user
     * @param  mixed  $mode
     * @return array
     */
    protected function queryBillsCount($user, $mode)
    {
        $userIds = User::whereIn('id', [$user->id, $user->store_main_user_id])-> pluck('id')->toArray();

        $collection = [];

        switch ($mode) {
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

        return $collection;
    }

    /**
     * Cache the numeric aggregate only. datasets() stays outside so labels follow the session locale.
     *
     * @param  \App\Models\User  $user
     * @param  mixed  $mode
     * @return array
     */
    protected function rememberChartAggregate($user, string $metric, $mode, Closure $query)
    {
        if (! in_array($mode, ['daily', 'weekly', 'monthly'], true) || ! $this->cache->enabled('merchant_charts')) {
            return $query();
        }

        [$from, $to] = $this->chartWindow($mode, $this->chartToday());

        return $this->cache->remember(
            CacheKeys::charts($user->id, $metric, $mode, $from, $to),
            $this->chartTtl($mode),
            function () use ($query) {
                return $this->plainAggregate($query());
            },
            'merchant_charts'
        );
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function chartWindow(string $mode, DateTimeImmutable $today): array
    {
        $today = $today->setTime(0, 0);

        if ($mode === 'weekly') {
            return [
                $today->modify('first day of this month')->format('Y-m-d'),
                $today->modify('last day of this month')->format('Y-m-d'),
            ];
        }

        if ($mode === 'monthly') {
            $year = $today->format('Y');

            return [$year . '-01-01', $year . '-12-31'];
        }

        return $this->mysqlWeekMode0Bounds($today);
    }

    protected function chartToday(): DateTimeImmutable
    {
        $timezone = config('app.timezone') ?: 'Asia/Riyadh';

        return new DateTimeImmutable('now', new DateTimeZone($timezone));
    }

    protected function chartTtl(string $mode): int
    {
        $name = [
            'daily' => 'charts_week',
            'weekly' => 'charts_month',
            'monthly' => 'charts_year',
        ][$mode];

        return (int) config('surebills_cache.ttl.' . $name);
    }

    /**
     * Sunday-start week used by MySQL WEEK() mode 0.
     * Days before the first Sunday of the year are week 0, so the key is clamped
     * to the dates that share that WEEK() value instead of the whole Sun–Sat span.
     *
     * @return array{0: string, 1: string}
     */
    protected function mysqlWeekMode0Bounds(DateTimeImmutable $today): array
    {
        $today = $today->setTime(0, 0);
        $week = $this->mysqlWeekMode0($today);
        $year = (int) $today->format('Y');
        $start = $today->modify('-' . (int) $today->format('w') . ' days');
        $end = $start->modify('+6 days');

        while ($this->mysqlWeekMode0($start) !== $week || (int) $start->format('Y') !== $year) {
            $start = $start->modify('+1 day');
        }

        while ($this->mysqlWeekMode0($end) !== $week || (int) $end->format('Y') !== $year) {
            $end = $end->modify('-1 day');
        }

        return [$start->format('Y-m-d'), $end->format('Y-m-d')];
    }

    protected function mysqlWeekMode0(DateTimeImmutable $date): int
    {
        $date = $date->setTime(0, 0);
        $year = (int) $date->format('Y');
        $jan1 = $date->setDate($year, 1, 1);
        $jan1Dow = (int) $jan1->format('w');
        $firstSunday = $jan1Dow === 0 ? $jan1 : $jan1->modify('+' . (7 - $jan1Dow) . ' days');

        if ($date < $firstSunday) {
            return 0;
        }

        return 1 + intdiv((int) $firstSunday->diff($date)->days, 7);
    }

    /**
     * @param  array<string, mixed>  $collection
     * @return array<string, array<int|string, mixed>>
     */
    protected function plainAggregate(array $collection): array
    {
        $plain = [];

        foreach ($collection as $series => $values) {
            $plain[$series] = $values instanceof Collection ? $values->all() : $values;
        }

        return $plain;
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
