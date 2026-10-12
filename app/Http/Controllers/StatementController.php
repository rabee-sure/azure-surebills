<?php

namespace App\Http\Controllers;

use App\Exports\StatementExport;
use App\Models\Application;
use App\Models\Bill;
use App\Models\Channel;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;

class StatementController extends Controller
{
    /** @var SureBillsCache */
    private $cache;

    public function __construct(SureBillsCache $cache)
    {
        $this->cache = $cache;
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

        $channel = ($request->has('channel_id') && !in_array($request->channel_id, ['all','undefined']))? Channel::find($request->channel_id) : null;
        $application = ($request->has('application_id') && !in_array($request->application_id, ['all','undefined']))
            ? Application::where('id',$request->application_id)
                ->with(['user' => function ($query) {
                    $query->withTrashed()->with('mainStoreUser');
                }])
                ->first() 
            : null;

        $channels = Channel::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)->get();
        $applications = ($channel)
            ? Application::where('channel_id', $channel->id)
                ->with(['user' => function ($query) {
                    $query->withTrashed()->with('mainStoreUser');
                }])
                ->get()
            : [];

        [$statement, $totals] = $this->resolveStatementPage($request);

        return view('statements.index', compact('statement', 'date_start',
            'date_to', 'channels', 'channel', 'applications', 'application','totals'));
    }

    /**
     * Default month-to-date pages are cached. Filtered and export paths stay on MySQL.
     *
     * @return array{0: \Illuminate\Contracts\Pagination\LengthAwarePaginator, 1: array}
     */
    protected function resolveStatementPage(Request $request): array
    {
        if (! $this->statementPageIsCacheable($request) || ! $this->cache->enabled('merchant_transactions')) {
            return $this->loadStatementPage(false);
        }

        $page = Paginator::resolveCurrentPage();
        $payload = $this->cache->remember(
            $this->statementPageCacheKey($page),
            (int) config('surebills_cache.ttl.transactions'),
            function () {
                [$statement, $totals] = $this->loadStatementPage(true);

                return $this->snapshotStatementPage($statement, $totals);
            },
            'merchant_transactions'
        );

        return [
            $this->restoreStatementPaginator($payload, $request),
            $payload['totals'],
        ];
    }

    /**
     * Same date window and filters as User::getStatement(). Absence of a parameter is not enough.
     */
    protected function statementPageIsCacheable(Request $request): bool
    {
        if ($this->statementOwnerId() === null || ! $this->isDefaultMonthToDate($request)) {
            return false;
        }

        $type = $request->input('transaction_type');
        if ($type === 'debit' || $type === 'credit') {
            return false;
        }

        $source = $request->input('transaction_source');
        if ($source !== null && $source !== '' && $source !== 'all' && $source !== 'undefined') {
            return false;
        }

        if ($request->has('channel_id') && ! in_array($request->input('channel_id'), ['all', 'undefined'], true)) {
            return false;
        }

        if ($request->has('application_id') && ! in_array($request->input('application_id'), ['all', 'undefined'], true)) {
            return false;
        }

        if ($request->filled('search')) {
            return false;
        }

        return true;
    }

    /**
     * @return array{0: \Illuminate\Contracts\Pagination\LengthAwarePaginator, 1: array}
     */
    protected function loadStatementPage(bool $withDisplayRelations): array
    {
        $statementQuery = auth()->user()->getStatement();
        $creditQuery = clone $statementQuery;
        $debitQuery = clone $statementQuery;

        if ($withDisplayRelations) {
            // Capture the bill names the Blade view prints. Bill::toArray() is avoided
            // because its appends run refund and balance queries.
            $statementQuery->with([
                'bill.application',
                'bill.user.mainStoreUser',
            ]);
        }

        $statement = $statementQuery->paginate(50);
        $credit = $creditQuery->where('type', 'credit')->selectRaw("SUM(amount) AS credit");
        $debit = $debitQuery->where('type', 'debit')->selectRaw("SUM(amount) AS debit");

        $totals = [];
        $totals['credit'] = ($credit->first() != null) ? round2($credit->first()->credit) : 0;
        $totals['debit'] = ($debit->first() != null) ? round2($debit->first()->debit) : 0;
        $totals['all'] = round($totals['credit'] - $totals['debit'], 2);

        return [$statement, $totals];
    }

    protected function statementPageCacheKey(int $page): string
    {
        return CacheKeys::txPage(
            $this->statementOwnerId(),
            $this->transactionYm(),
            $page
        );
    }

    /**
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator  $statement
     * @param  array  $totals
     * @return array
     */
    protected function snapshotStatementPage($statement, array $totals): array
    {
        $items = [];
        foreach ($statement as $transaction) {
            $items[] = $this->snapshotTransaction($transaction);
        }

        return [
            'items' => $items,
            'total' => (int) $statement->total(),
            'per_page' => (int) $statement->perPage(),
            'current_page' => (int) $statement->currentPage(),
            'totals' => [
                'credit' => $totals['credit'],
                'debit' => $totals['debit'],
                'all' => $totals['all'],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function snapshotTransaction(Transaction $transaction): array
    {
        $row = $transaction->getAttributes();
        $bill = $transaction->relationLoaded('bill') ? $transaction->getRelation('bill') : null;
        $row['bill'] = $this->snapshotBill($bill);

        return $row;
    }

    /**
     * @param  Bill|null  $bill
     * @return array|null
     */
    protected function snapshotBill($bill): ?array
    {
        if ($bill === null) {
            return null;
        }

        $application = $bill->relationLoaded('application') ? $bill->getRelation('application') : null;
        $user = $bill->relationLoaded('user') ? $bill->getRelation('user') : null;
        $main = ($user && $user->relationLoaded('mainStoreUser')) ? $user->getRelation('mainStoreUser') : null;

        return [
            'application_id' => $bill->application_id,
            'application' => $application ? [
                'channel_id' => $application->channel_id,
            ] : null,
            'user' => $user ? [
                'business_name_en' => $user->business_name_en,
                'business_name_ar' => $user->business_name_ar,
                'main_store_user' => $main ? [
                    'business_name_en' => $main->business_name_en,
                    'business_name_ar' => $main->business_name_ar,
                ] : null,
            ] : null,
        ];
    }

    /**
     * @param  array  $payload
     * @return LengthAwarePaginator
     */
    protected function restoreStatementPaginator(array $payload, Request $request)
    {
        $items = collect($payload['items'])->map(function ($row) {
            return $this->restoreTransaction($row);
        });

        return (new LengthAwarePaginator(
            $items,
            (int) $payload['total'],
            (int) $payload['per_page'],
            (int) $payload['current_page'],
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        ))->appends($request->query());
    }

    /**
     * @param  array  $row
     * @return Transaction
     */
    protected function restoreTransaction(array $row)
    {
        $bill = $row['bill'] ?? null;
        unset($row['bill']);

        $transaction = (new Transaction)->newFromBuilder($row);
        $transaction->setRelation('bill', $this->restoreBill(is_array($bill) ? $bill : null));

        return $transaction;
    }

    /**
     * @param  array|null  $bill
     * @return Bill|null
     */
    protected function restoreBill($bill)
    {
        if (! is_array($bill)) {
            return null;
        }

        $model = (new Bill)->newFromBuilder([
            'application_id' => $bill['application_id'] ?? null,
        ]);

        if (! empty($bill['application']) && is_array($bill['application'])) {
            $model->setRelation('application', (new Application)->newFromBuilder([
                'channel_id' => $bill['application']['channel_id'] ?? null,
            ]));
        } else {
            $model->setRelation('application', null);
        }

        if (! empty($bill['user']) && is_array($bill['user'])) {
            $user = (new User)->newFromBuilder([
                'business_name_en' => $bill['user']['business_name_en'] ?? null,
                'business_name_ar' => $bill['user']['business_name_ar'] ?? null,
            ]);
            if (! empty($bill['user']['main_store_user']) && is_array($bill['user']['main_store_user'])) {
                $user->setRelation('mainStoreUser', (new User)->newFromBuilder([
                    'business_name_en' => $bill['user']['main_store_user']['business_name_en'] ?? null,
                    'business_name_ar' => $bill['user']['main_store_user']['business_name_ar'] ?? null,
                ]));
            } else {
                $user->setRelation('mainStoreUser', null);
            }
            $model->setRelation('user', $user);
        } else {
            $model->setRelation('user', null);
        }

        return $model;
    }

    /**
     * Month-to-date in the application timezone, matching the uncached statement query.
     */
    protected function isDefaultMonthToDate(Request $request): bool
    {
        $today = Carbon::today();
        $start = $today->copy()->firstOfMonth()->startOfDay();
        $end = $today->copy()->startOfDay();

        if ($request->filled('date_start')) {
            $given = Carbon::parse($request->input('date_start'))->startOfDay();
            if (! $given->equalTo($start)) {
                return false;
            }
        }

        if ($request->filled('date_to')) {
            $given = Carbon::parse($request->input('date_to'))->startOfDay();
            if (! $given->equalTo($end)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return int|string|null
     */
    protected function statementOwnerId()
    {
        $user = auth()->user();
        if ($user === null) {
            return null;
        }

        return $user->store_main_user_id ?? $user->id;
    }

    protected function transactionYm(): string
    {
        return Carbon::today()->format('Ym');
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
