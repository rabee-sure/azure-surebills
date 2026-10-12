<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class TransactionController extends Controller
{
    /** @var SureBillsCache */
    private $cache;

    public function __construct(SureBillsCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($this->apiPageIsCacheable($request) && $this->cache->enabled('merchant_transactions')) {
            $page = Paginator::resolveCurrentPage();
            $payload = $this->cache->remember(
                $this->apiPageCacheKey($page),
                (int) config('surebills_cache.ttl.transactions'),
                function () use ($request) {
                    return $this->apiDisplayPayload($request);
                },
                'merchant_transactions'
            );

            return response()->json($payload);
        }

        return $this->apiResourceResponse($request);
    }

    /**
     * The API list is 10 rows and a different JSON shape from the statement page,
     * so the page key gets an :api suffix without changing CacheKeys::txPage().
     */
    protected function apiPageCacheKey(int $page): string
    {
        $uid = $this->statementOwnerId();

        return CacheKeys::txPage($uid, $this->transactionYm(), $page).':api';
    }

    protected function apiPageIsCacheable(Request $request): bool
    {
        if ($this->statementOwnerId() === null || ! $this->isDefaultMonthToDate($request)) {
            return false;
        }

        if ($request->has('per_page') && (string) $request->input('per_page') !== '10') {
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

        if ($request->filled('channel')) {
            return false;
        }

        if ($request->filled('search')) {
            return false;
        }

        return true;
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    protected function apiResourceResponse(Request $request)
    {
        $all_transactions = $this->query($request)->get();
        $transactions = $this->query($request)->with('bill', 'bill.application', 'bill.application.channel')->paginate($request->get('per_page', 10));

        return TransactionResource::collection($transactions)->additional(['meta' => [
            'balance' => round($all_transactions->where('type', 'credit')->sum('amount')-$all_transactions->where('type', 'debit')->sum('amount'), 2),
            'total_credit' => round($all_transactions->where('type', 'credit')->sum('amount'), 2),
            'total_debit' => round($all_transactions->where('type', 'debit')->sum('amount'), 2),
        ]]);
    }

    /**
     * @return array
     */
    protected function apiDisplayPayload(Request $request): array
    {
        return json_decode($this->apiResourceResponse($request)->toResponse($request)->getContent(), true);
    }

    /**
     * Month-to-date in the application timezone, matching query().
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
     * Same owner as User::statement(). Do not switch this to $request->user.
     *
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
    
    protected function query ($request){
        $user = $request->user;
        $date_start = $request->date_start ?? Carbon::today()->firstOfMonth()->format('m/d/Y');
        $date_to = $request->date_to ?? Carbon::today()->format('m/d/Y');
        $transaction_type = $request->transaction_type ?? null;
        $transaction_source = $request->transaction_source ?? null;
        $channel = $request->channel ?? null;

        return $user->statement()
        ->when($date_start, function($q) use($date_start, $date_to){
            $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                ->whereDate('created_at', '<=', Carbon::parse($date_to));
        })
        ->when($transaction_type == 'debit' || $transaction_type == 'credit', function($q) use($transaction_type){
            $q->whereType($transaction_type);
        })
        ->when(isset($transaction_source) && $transaction_source != 'all' && $transaction_source != 'undefined', function($q) use($transaction_source){
            $q->whereTransactionSource($transaction_source);
        })
        ->when($channel, function($q) use($channel){
            $q->whereHas('bill.application', function ( $query) use($channel){
                $query->where('channel_id', $channel->id);
            });
        });
    }
}
