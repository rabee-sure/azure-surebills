<?php

namespace App\Http\Controllers;

use App\Events\ContactSendEmail;
use App\Http\Requests\ContactRequest;
use App\Models\Bill;
use App\Models\RefundedBill;
use App\Services\BasicSettingsService;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Throwable;

class HomeController extends Controller
{
    /** @var BasicSettingsService */
    private $basicSettingsService;

    /** @var SureBillsCache */
    private $cache;

    public function __construct(BasicSettingsService $basicSettingsService, ?SureBillsCache $cache = null)
    {
        $this->basicSettingsService = $basicSettingsService;
        $this->cache = $cache ?? app(SureBillsCache::class);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function landing()
    {
        if (auth()->user()) {
            return redirect('/home');
        }

        return view('landing/home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(auth()->user()->source == 'pos')
        {
            return redirect(route('reports.paymentRecord'));
        }

        $user = auth()->user();
        $ownerId = $user->store_main_user_id ?? $user->id;
        $user->userId = $ownerId;

        $latest = $this->displayLastBills($ownerId);
        $balance = $this->displayBalance($user);
        $totals = $this->displayTotals($ownerId);

        $settings = $this->basicSettingsService->getSettings();

        return view('home', [
            'user' =>  $user,
            'balance' =>  $balance ?? 0,
            'latest' =>  $latest,
            'total_paid' =>  $totals['total_paid'],
            'total_bills' =>  $totals['total_bills'],
            'total_paid_bills' =>  $totals['total_paid_bills'],
            'mobile_number' => $settings['mobile_number'] ?? null,
        ]);
    }

    /**
     * Home display balance. Financial callers keep using User::getBalanceAttribute().
     *
     * @param  object  $user
     * @return mixed
     */
    protected function displayBalance($user)
    {
        $ownerId = $user->store_main_user_id ?? $user->id;
        $read = function () use ($user) {
            return $this->readDisplayBalance($user);
        };

        if (! $this->cache->enabled('merchant_home')) {
            return $read();
        }

        try {
            return $this->cache->remember(
                CacheKeys::uiBalance($ownerId),
                (int) config('surebills_cache.ttl.balance'),
                $read,
                'merchant_home'
            );
        } catch (Throwable $e) {
            return $read();
        }
    }

    /**
     * @param  object  $user
     * @return mixed
     */
    protected function readDisplayBalance($user)
    {
        return $user->balance;
    }

    /**
     * @param  int|string  $ownerId
     * @return \Illuminate\Support\Collection
     */
    protected function displayLastBills($ownerId)
    {
        if (! $this->cache->enabled('merchant_home')) {
            return $this->queryLastBills($ownerId);
        }

        try {
            $payload = $this->cache->remember(
                CacheKeys::homeLastBills($ownerId),
                (int) config('surebills_cache.ttl.last_bills'),
                function () use ($ownerId) {
                    return $this->lastBillPayloads($this->queryLastBills($ownerId));
                },
                'merchant_home'
            );
        } catch (Throwable $e) {
            return $this->queryLastBills($ownerId);
        }

        if (! $this->isLastBillPayload($payload)) {
            return $this->queryLastBills($ownerId);
        }

        return $this->billsFromPayload($payload);
    }

    /**
     * @param  int|string  $ownerId
     * @return \Illuminate\Support\Collection
     */
    protected function queryLastBills($ownerId)
    {
        $bills = Bill::userId($ownerId);

        $latestQuery = (clone $bills)->select([
            'id',
            'number',
            'customer_name',
            'fixed_total',
            'status',
            'payment_way as method',
            'created_at',
            'user_id',
            DB::raw("CASE WHEN debit_note_bill_id IS NULL THEN 'bill' ELSE 'debit_note' END as type"),
        ])->union(
            RefundedBill::userId($ownerId)->select([
                'id',
                'number',
                'customer_name',
                'amount as fixed_total',
                'status',
                'method',
                'created_at',
                'user_id',
                DB::raw("'credit_note' as type"),
            ])
        );

        return $latestQuery->orderBy('created_at', 'desc')->take(6)->get();
    }

    /**
     * @param  iterable  $bills
     * @return array<int, array<string, mixed>>
     */
    private function lastBillPayloads($bills): array
    {
        $payload = [];

        foreach ($bills as $bill) {
            $createdAt = $bill->created_at;
            if ($createdAt instanceof DateTimeInterface) {
                $createdAt = $createdAt->format('Y-m-d H:i:s');
            }

            $payload[] = [
                'id' => $bill->id,
                'number' => $bill->number,
                'customer_name' => $bill->customer_name,
                'fixed_total' => $bill->fixed_total,
                'status' => $bill->status,
                'method' => $bill->method,
                'created_at' => $createdAt,
                'user_id' => $bill->user_id,
                'type' => $bill->type,
            ];
        }

        return $payload;
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return \Illuminate\Support\Collection
     */
    private function isLastBillPayload($payload): bool
    {
        if (! is_array($payload)) {
            return false;
        }

        foreach ($payload as $row) {
            if (! is_array($row) || ! array_key_exists('id', $row) || ! array_key_exists('type', $row)) {
                return false;
            }
        }

        return true;
    }

    private function billsFromPayload(array $rows)
    {
        return collect($rows)->map(function ($row) {
            $bill = new Bill;
            $bill->setDateFormat('Y-m-d H:i:s');
            $bill->forceFill([
                'id' => $row['id'] ?? null,
                'number' => $row['number'] ?? null,
                'customer_name' => $row['customer_name'] ?? null,
                'fixed_total' => $row['fixed_total'] ?? null,
                'status' => $row['status'] ?? null,
                'method' => $row['method'] ?? null,
                'created_at' => $row['created_at'] ?? null,
                'user_id' => $row['user_id'] ?? null,
                'type' => $row['type'] ?? null,
            ]);

            return $bill;
        })->values();
    }

    /**
     * @param  int|string  $ownerId
     * @return array{total_paid: mixed, total_bills: mixed, total_paid_bills: mixed}
     */
    protected function displayTotals($ownerId): array
    {
        if (! $this->cache->enabled('merchant_home')) {
            return $this->queryDisplayTotals($ownerId);
        }

        try {
            $payload = $this->cache->remember(
                CacheKeys::homeTotals($ownerId),
                (int) config('surebills_cache.ttl.home_totals'),
                function () use ($ownerId) {
                    return $this->queryDisplayTotals($ownerId);
                },
                'merchant_home'
            );
        } catch (Throwable $e) {
            return $this->queryDisplayTotals($ownerId);
        }

        if (! is_array($payload)
            || ! array_key_exists('total_paid', $payload)
            || ! array_key_exists('total_bills', $payload)
            || ! array_key_exists('total_paid_bills', $payload)
        ) {
            return $this->queryDisplayTotals($ownerId);
        }

        return [
            'total_paid' => $payload['total_paid'],
            'total_bills' => $payload['total_bills'],
            'total_paid_bills' => $payload['total_paid_bills'],
        ];
    }

    /**
     * @param  int|string  $ownerId
     * @return array{total_paid: mixed, total_bills: mixed, total_paid_bills: mixed}
     */
    protected function queryDisplayTotals($ownerId): array
    {
        $bills = Bill::userId($ownerId);

        return [
            'total_paid' => (clone $bills)->where('status', 'paid')->sum('total'),
            'total_bills' => (clone $bills)->count(),
            'total_paid_bills' => (clone $bills)->where('status', 'paid')->count(),
        ];
    }

    /**
     * Show contact page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function contact()
    {
        return view('landing/contact');
    }

    public function contactSendForm(ContactRequest $request){
        $data['source'] = $request->source;
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['company'] = $request->company;
        $data['mobile'] = $request->mobile;
        $data['message'] = $request->message;
        //fire event to send email for message
        event(new ContactSendEmail($data));

        return response()->json(['success' => __('message will sent to support team')], 200);
    }

    /**
     * Show privacy page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function privacy()
    {
        return view('landing/privacy_policy');
    }

    /**
     * Show terms page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function terms()
    {
        return view('landing/terms');
    }
}
