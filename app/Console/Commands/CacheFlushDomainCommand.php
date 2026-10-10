<?php

namespace App\Console\Commands;

use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Console\Command;
use Illuminate\Support\ConfigurationUrlParser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Bounded domain flush. Dry-run is the default. --execute forgets known keys
 * or bumps known version keys through SureBillsCache. Never scans or flushes
 * a Redis database.
 */
class CacheFlushDomainCommand extends Command
{
    protected $signature = 'cache:flush-domain {domain} {--id=} {--user=} {--execute}';

    protected $description = 'Dry-run or invalidate one approved SureBills cache domain';

    private const APP = 'merchant';

    /** @var array<string, string> */
    private const UNSAFE_DOMAINS = [
        'coupons' => 'Coupon keys include a merchant and a date. Safe domain-wide invalidation is not supported.',
        'merchant_charts' => 'Chart keys vary by metric, period, and date range. Safe domain-wide invalidation is not supported.',
        'merchant_transactions' => 'Transaction page keys vary by month and page. Safe domain-wide invalidation is not supported.',
    ];

    /**
     * id/user: forbid, optional, or required.
     *
     * @var array<string, array{id: string, user: string}>
     */
    private const RULES = [
        'basic_settings' => ['id' => 'forbid', 'user' => 'forbid'],
        'banks' => ['id' => 'optional', 'user' => 'forbid'],
        'merchant_profile' => ['id' => 'forbid', 'user' => 'optional'],
        'admin_badges' => ['id' => 'forbid', 'user' => 'forbid'],
        'admin_home' => ['id' => 'forbid', 'user' => 'forbid'],
        'channels' => ['id' => 'required', 'user' => 'forbid'],
        'applications' => ['id' => 'required', 'user' => 'forbid'],
        'merchant_settings' => ['id' => 'forbid', 'user' => 'required'],
        'customers' => ['id' => 'required', 'user' => 'forbid'],
        'pending_bills' => ['id' => 'forbid', 'user' => 'required'],
        'merchant_home' => ['id' => 'forbid', 'user' => 'required'],
        'transfers' => ['id' => 'forbid', 'user' => 'required'],
    ];

    /** @var list<string> */
    private const ADMIN_BADGES = [
        'unverified-merchants',
        'pending-tax-invoices',
        'pending-transfers',
    ];

    /** @var list<string> */
    private const ADMIN_HOME_YEARLY = [
        'sum_total',
        'sum_commission',
        'sum_commission_vat',
        'sum_amount_transfers',
    ];

    public function __construct(private readonly SureBillsCache $cache)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $domain = (string) $this->argument('domain');
        $id = $this->presentOption('id');
        $user = $this->presentOption('user');

        if (isset(self::UNSAFE_DOMAINS[$domain])) {
            $this->error('Refusing domain ['.$domain.']. '.self::UNSAFE_DOMAINS[$domain]);

            return 1;
        }

        if (! isset(self::RULES[$domain])) {
            $this->error('Unknown domain ['.$domain.']. Safe domain-wide invalidation is not supported.');

            return 1;
        }

        $identifierError = $this->identifierError($domain, $id, $user);
        if ($identifierError !== null) {
            $this->error($identifierError);

            return 1;
        }

        $operations = $this->operations($domain, $id, $user);
        $target = $this->describeTarget();

        $this->info($this->option('execute') ? 'mode: execute' : 'mode: dry-run');
        $this->line('domain: '.$domain);
        foreach ($target['lines'] as $line) {
            $this->line($line);
        }
        $this->line('operations:');
        foreach ($operations as $operation) {
            $this->line($operation['op'].' '.$operation['key']);
        }
        $this->warnIfFlagOff($domain);

        if ($target['refusals'] !== []) {
            foreach ($target['refusals'] as $refusal) {
                $this->error($refusal);
            }

            return 1;
        }

        if (! $this->option('execute')) {
            $this->info('Dry-run only. No cache keys were read or written. Re-run with --execute to apply.');

            return 0;
        }

        $failed = [];
        foreach ($operations as $operation) {
            $ok = $operation['op'] === 'bump'
                ? $this->bump($operation['key'], $domain)
                : $this->forget($operation['key'], $domain);
            if (! $ok) {
                $failed[] = $operation['key'];
            }
        }

        if ($failed !== []) {
            $this->error('Failed logical keys:');
            foreach ($failed as $key) {
                $this->line($key);
            }

            return 2;
        }

        $this->info('Flush completed.');

        return 0;
    }

    private function presentOption(string $name): ?string
    {
        $value = $this->option($name);
        if ($value === null || $value === false || $value === '') {
            return null;
        }

        return (string) $value;
    }

    private function identifierError(string $domain, ?string $id, ?string $user): ?string
    {
        $rule = self::RULES[$domain];

        if ($rule['id'] === 'forbid' && $id !== null) {
            return 'Domain ['.$domain.'] does not accept --id.';
        }
        if ($rule['user'] === 'forbid' && $user !== null) {
            return 'Domain ['.$domain.'] does not accept --user.';
        }
        if ($rule['id'] === 'required' && $id === null) {
            return 'Domain ['.$domain.'] requires --id.';
        }
        if ($rule['user'] === 'required' && $user === null) {
            return 'Domain ['.$domain.'] requires --user.';
        }
        if ($id !== null && preg_match('/^[1-9][0-9]*$/', $id) !== 1) {
            return 'Option --id must be a positive integer.';
        }
        if ($user !== null && preg_match('/^[1-9][0-9]*$/', $user) !== 1) {
            return 'Option --user must be a positive integer.';
        }

        return null;
    }

    /**
     * @return list<array{op: string, key: string}>
     */
    private function operations(string $domain, ?string $id, ?string $user): array
    {
        $operations = [];

        switch ($domain) {
            case 'basic_settings':
                $operations[] = $this->forgetOp(CacheKeys::basicSettings());
                break;
            case 'banks':
                $operations[] = $this->bumpOp(CacheKeys::banksActiveVersion());
                if ($id !== null) {
                    $operations[] = $this->forgetOp(CacheKeys::bank($id));
                }
                break;
            case 'merchant_profile':
                $operations[] = $this->bumpOp(CacheKeys::merchantsDropdownVersion());
                if ($user !== null) {
                    $operations[] = $this->forgetOp(CacheKeys::merchantProfile($user));
                }
                break;
            case 'admin_badges':
                foreach (self::ADMIN_BADGES as $badge) {
                    $operations[] = $this->forgetOp(CacheKeys::adminBadge($badge));
                }
                break;
            case 'admin_home':
                $operations[] = $this->bumpOp(CacheKeys::adminHomeVersion());
                foreach (self::ADMIN_HOME_YEARLY as $series) {
                    $operations[] = $this->forgetOp(CacheKeys::adminHomeYearly($series));
                }
                break;
            case 'channels':
                $operations[] = $this->forgetOp(CacheKeys::channel($id));
                break;
            case 'applications':
                $operations[] = $this->forgetOp(CacheKeys::application($id));
                break;
            case 'merchant_settings':
                $operations[] = $this->forgetOp(CacheKeys::merchantSettings($user));
                break;
            case 'customers':
                $operations[] = $this->forgetOp(CacheKeys::customer($id));
                break;
            case 'pending_bills':
                $operations[] = $this->forgetOp(CacheKeys::pendingBills($user));
                break;
            case 'merchant_home':
                $operations[] = $this->forgetOp(CacheKeys::uiBalance($user));
                $operations[] = $this->forgetOp(CacheKeys::homeLastBills($user));
                $operations[] = $this->forgetOp(CacheKeys::homeTotals($user));
                break;
            case 'transfers':
                $operations[] = $this->forgetOp($this->transfersKey($user));
                break;
        }

        return $operations;
    }

    /**
     * Admin CacheKeys has no transfers() helper. Merchant does.
     * Both must produce merchant:{user}:transfers.
     */
    private function transfersKey(string $user): string
    {
        return CacheKeys::transfers($user);
    }

    /**
     * @return array{op: string, key: string}
     */
    private function forgetOp(string $key): array
    {
        return ['op' => 'forget', 'key' => $key];
    }

    /**
     * @return array{op: string, key: string}
     */
    private function bumpOp(string $key): array
    {
        return ['op' => 'bump', 'key' => $key];
    }

    /**
     * Effective target from application config. Does not connect to Redis.
     * Laravel applies a Redis URL over the discrete host/port/database when
     * resolving the cache connection, so the same parser is used here.
     *
     * @return array{lines: list<string>, refusals: list<string>}
     */
    private function describeTarget(): array
    {
        $storeName = config('cache.default');
        $store = config('cache.stores.'.$storeName);
        $store = is_array($store) ? $store : [];
        $driver = $store['driver'] ?? null;
        $connection = $store['connection'] ?? null;
        $cachePrefix = $store['prefix'] ?? config('cache.prefix');
        $redisPrefix = config('database.redis.options.prefix');

        $parsed = [];
        $parseError = false;
        if (is_string($connection) && $connection !== '') {
            $raw = config('database.redis.'.$connection);
            if (is_array($raw)) {
                try {
                    $parsed = (new ConfigurationUrlParser)->parseConfiguration($raw);
                } catch (Throwable $e) {
                    $parseError = true;
                }
            } else {
                $parseError = true;
            }
        }

        $database = $parsed['database'] ?? null;
        $lines = [
            'cache store: '.$this->display($storeName),
            'cache driver: '.$this->display($driver),
            'cache connection: '.$this->display($connection),
            'redis host: '.$this->display($parsed['host'] ?? null),
            'redis port: '.$this->display($parsed['port'] ?? null),
            'redis database: '.$this->display($database),
            'cache prefix: '.$this->display($cachePrefix),
            'redis prefix: '.$this->display($redisPrefix),
        ];

        $refusals = [];
        if ($driver !== 'redis') {
            $refusals[] = 'Refusing to write: cache driver is ['.$this->display($driver).'], expected [redis].';
        }
        if ($connection !== 'cache') {
            $refusals[] = 'Refusing to write: cache connection is ['.$this->display($connection).'], expected [cache].';
        }
        if ($parseError) {
            $refusals[] = 'Refusing to write: Redis cache connection configuration could not be read.';
        }
        if (! $parseError && $database !== 1 && $database !== '1') {
            $refusals[] = 'Refusing to write: Redis cache database is ['.$this->display($database).'], expected [1].';
        }
        if ($cachePrefix !== 'sb_') {
            $refusals[] = 'Refusing to write: CACHE_PREFIX is ['.$this->display($cachePrefix).'], expected [sb_].';
        }
        if ($redisPrefix !== 'sb_database_') {
            $refusals[] = 'Refusing to write: REDIS_PREFIX is ['.$this->display($redisPrefix).'], expected [sb_database_].';
        }

        return ['lines' => $lines, 'refusals' => $refusals];
    }

    /**
     * @param  mixed  $value
     */
    private function display($value): string
    {
        if ($value === null || $value === '') {
            return '(not set)';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_int($value) || is_float($value) || is_string($value)) {
            return (string) $value;
        }

        return '(unreadable)';
    }

    private function warnIfFlagOff(string $domain): void
    {
        $flag = $domain === 'applications' ? 'channels' : $domain;
        if ($domain === 'applications') {
            $this->line('Application keys use the channels flag.');
        }
        if (! config('surebills_cache.enabled')) {
            $this->warn('Master cache switch is off.');
            $this->warn('Web reads bypass Redis until it is enabled.');
        }
        if (! config('surebills_cache.domains.'.$flag)) {
            $this->warn('Domain flag ['.$flag.'] is off.');
            $this->warn('Web reads bypass Redis until that flag is enabled.');
        }
    }

    /**
     * A missing version is already treated as 1, and INCR of a missing key
     * returns 1. Only a final value of at least 2 changes the key readers use.
     */
    private function bump(string $key, string $domain): bool
    {
        try {
            $first = $this->cache->bumpVersion($key);
        } catch (Throwable $e) {
            $this->reraiseTestDouble($e);

            return $this->failBump($key, $domain);
        }

        if (! is_int($first) || $first < 1) {
            return $this->failBump($key, $domain);
        }

        if ($first >= 2) {
            $this->line('ok bump '.$key.' final='.$first);
            $this->logResult($domain, 'bump', $key, 'ok');

            return true;
        }

        try {
            $second = $this->cache->bumpVersion($key);
        } catch (Throwable $e) {
            $this->reraiseTestDouble($e);

            return $this->failBump($key, $domain);
        }

        if (is_int($second) && $second >= 2) {
            $this->line('ok bump '.$key.' final='.$second);
            $this->logResult($domain, 'bump', $key, 'ok');

            return true;
        }

        return $this->failBump($key, $domain);
    }

    /**
     * A final version below 2 does not move readers off the current payload.
     */
    private function failBump(string $key, string $domain): bool
    {
        $this->line('failed bump '.$key);
        $this->line('Bump did not reach 2.');
        $this->line('Old cached payload may remain in use');
        $this->line('until a later successful version bump.');
        $this->logResult($domain, 'bump', $key, 'error');

        return false;
    }

    /**
     * forget() false means either DEL removed nothing or fail-open swallowed
     * an error. A follow-up Cache::get of this same key distinguishes them.
     */
    private function forget(string $key, string $domain): bool
    {
        try {
            $forgotten = $this->cache->forget($key);
        } catch (Throwable $e) {
            $this->reraiseTestDouble($e);
            $this->line('failed forget '.$key);
            $this->logResult($domain, 'forget', $key, 'error');

            return false;
        }

        if ($forgotten === true) {
            $this->line('ok forget '.$key);
            $this->logResult($domain, 'forget', $key, 'ok');

            return true;
        }

        if ($forgotten !== false) {
            $this->line('failed forget '.$key);
            $this->logResult($domain, 'forget', $key, 'error');

            return false;
        }

        try {
            $value = Cache::get($key);
        } catch (Throwable $e) {
            $this->line('failed forget '.$key);
            $this->logResult($domain, 'forget', $key, 'error');

            return false;
        }

        if ($value === null) {
            $this->line('ok forget '.$key.' already-absent');
            $this->logResult($domain, 'forget', $key, 'absent');

            return true;
        }

        $this->line('failed forget '.$key);
        $this->logResult($domain, 'forget', $key, 'error');

        return false;
    }

    /**
     * Mock expectation failures must fail the test. They are not cache errors.
     */
    private function reraiseTestDouble(Throwable $e): void
    {
        if ($e instanceof \Mockery\Exception) {
            throw $e;
        }
    }

    private function logResult(string $domain, string $op, string $key, string $result): void
    {
        try {
            Log::info('surebills.cache', [
                'domain' => $domain,
                'op' => $op,
                'key' => hash('sha256', $key),
                'result' => $result,
                'app' => self::APP,
            ]);
        } catch (Throwable $e) {
            // Logging must not change the flush result.
        }
    }
}
