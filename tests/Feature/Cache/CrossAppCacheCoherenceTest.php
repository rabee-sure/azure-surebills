<?php

namespace Tests\Feature\Cache;

use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Tests\Feature\Cache\Support\GuardedBankActiveListService;
use Tests\Feature\Cache\Support\GuardedBasicSettingsService;
use Tests\Feature\Cache\Support\GuardedChannelFeeConfigService;
use Tests\Feature\Cache\Support\GuardedMerchantProfileService;
use Tests\TestCase;
use Throwable;
use function Tests\Feature\Cache\Support\forbidSql;

/**
 * PR-25 cross-app cache coherence.
 *
 * Merchant PHPUnit starts one Admin PHP process. Both use a confirmed Redis
 * database other than DB 0 and DB 1. Direct cache writes and observer
 * dispatches are separate cases. Observer dispatch does not prove a MySQL
 * mutation. Bill status and Redis-down coverage are not part of this suite.
 *
 * Without PR25_REDIS_DB and PR25_REDIS_DB_CONFIRM=dedicated-unused this
 * class skips before the application boots and before any Redis socket.
 */
class CrossAppCacheCoherenceTest extends TestCase
{
    private const REDIS_PREFIX = 'sb_database_';

    private const CONFIRM = 'dedicated-unused';

    private static ?array $plan = null;

    private static bool $shutdownRegistered = false;

    private string $runId;

    private int $entityId;

    private int $redisDb;

    private string $writerPrefix;

    protected function setUp(): void
    {
        if (! self::optInConfirmed()) {
            $this->markTestSkipped('PR-25 cross-app skipped: dedicated Redis DB not confirmed');
        }

        if (! extension_loaded('redis')) {
            $this->fail('PR-25 opt-in requires the phpredis extension.');
        }

        require_once __DIR__.'/Support/GuardedCacheReaders.php';

        parent::setUp();

        ini_set('default_socket_timeout', '2');
        $this->redisDb = (int) self::envValue('PR25_REDIS_DB');
        $this->runId = bin2hex(random_bytes(8));
        $this->entityId = 900000000 + (hexdec(substr($this->runId, 0, 5)) % 1000000);
        $this->writerPrefix = 'sb_pr25_'.$this->runId.'_';
        forbidSql();
        $this->armCleanupPlan();
    }

    protected function tearDown(): void
    {
        if (is_array(self::$plan) && (self::$plan['items'] ?? []) !== [] && ! (self::$plan['done'] ?? false)) {
            self::deleteOwnedKeys();
        }

        parent::tearDown();
    }

    public function test_settings_put_is_visible_to_merchant(): void
    {
        $flags = $this->allOn();
        $sentinel = 'pr25-settings-'.$this->runId;
        $this->applyFlags($flags);
        $this->useCachePrefix($this->writerPrefix);
        $this->launch('settings-put', $flags, ['sentinel' => $sentinel], $this->writerPrefix, [
            CacheKeys::basicSettings(),
        ]);

        $reader = $this->settingsReader();
        $this->assertSame(['pr25_marker' => $sentinel], $reader->getSettings());
        $this->assertSame(0, $reader->loaderCalls);
    }

    public function test_channel_service_put_and_forget_are_visible_to_merchant(): void
    {
        $flags = $this->allOn();
        $this->applyFlags($flags);
        $this->useCachePrefix($this->writerPrefix);
        $payload = ['channel' => $this->channelSpec()];
        $keys = [CacheKeys::channel($this->entityId)];

        $this->launch('channels-put', $flags, $payload, $this->writerPrefix, $keys);
        $hit = $this->channelReader();
        $channel = $hit->get($this->entityId);
        $this->assertSame('pr25-channel', $channel->name);
        $this->assertSame('1.25', (string) $channel->mada_fixed);
        $this->assertSame(0, $hit->loaderCalls);

        $this->launch('channels-forget', $flags, $payload, $this->writerPrefix, $keys);
        $this->assertNull(Cache::get(CacheKeys::channel($this->entityId)));
        $miss = $this->channelReader();
        $this->assertSame('pr25-loader', $miss->get($this->entityId)->name);
        $this->assertSame(1, $miss->loaderCalls);
    }

    public function test_profile_service_put_forget_and_bump_are_visible_to_merchant(): void
    {
        $flags = $this->allOn();
        $this->applyFlags($flags);
        $this->useCachePrefix($this->writerPrefix);
        $payload = ['user' => $this->userSpec('pr25-profile')];
        $profileKey = CacheKeys::merchantProfile($this->entityId);
        $versionKey = CacheKeys::merchantsDropdownVersion();

        $this->launch('profile-put', $flags, $payload, $this->writerPrefix, [$profileKey]);
        $hit = $this->profileReader();
        $this->assertSame('pr25-profile', $hit->get($this->entityId)->name);
        $this->assertSame(0, $hit->loaderCalls);

        $this->launch('profile-forget', $flags, $payload, $this->writerPrefix, [$profileKey]);
        $this->assertNull(Cache::get($profileKey));
        $miss = $this->profileReader();
        $this->assertSame('pr25-loader', $miss->get($this->entityId)->name);
        $this->assertSame(1, $miss->loaderCalls);

        $this->launch('profile-bump', $flags, [], $this->writerPrefix, [$versionKey]);
        $this->assertSame(
            'merchants:dropdown:v5',
            $this->app->make(SureBillsCache::class)->versionedKey('merchants:dropdown', $versionKey)
        );
    }

    public function test_bank_list_put_is_visible_to_merchant(): void
    {
        $flags = $this->allOn();
        $this->applyFlags($flags);
        $this->useCachePrefix($this->writerPrefix);
        $this->launch('banks-put-list', $flags, ['rows' => $this->bankRows()], $this->writerPrefix, [
            CacheKeys::banksActive(1),
        ]);

        $reader = $this->bankReader();
        $this->assertSame('PR25CACHE', $reader->getActiveBanks()->first()->code);
        $this->assertSame(0, $reader->loaderCalls);
    }

    public function test_bank_observer_update_then_delete_changes_shared_version(): void
    {
        $flags = $this->allOn();
        $this->applyFlags($flags);
        $this->useCachePrefix($this->writerPrefix);
        $bank = $this->bankSpec();
        $payload = ['bank' => $bank, 'rows' => $this->bankRows()];
        $recordKey = CacheKeys::bank($this->entityId);
        $versionKey = CacheKeys::banksActiveVersion();
        $owned = [
            CacheKeys::banksActive(1),
            CacheKeys::banksActive(2),
            CacheKeys::banksActive(3),
            $versionKey,
            $recordKey,
        ];

        $this->launch('banks-observer-updated', $flags, $payload, $this->writerPrefix, $owned);
        $cache = $this->app->make(SureBillsCache::class);
        $this->assertSame('banks:active:v2', $cache->versionedKey('banks:active', $versionKey));
        $this->assertSame('PR25CACHE', Cache::get(CacheKeys::banksActive(1))[0]['code']);

        $reader = $this->bankReader();
        $this->assertSame('PR25LOADER', $reader->getActiveBanks()->first()->code);
        $this->assertSame(1, $reader->loaderCalls);
        $this->assertSame($this->expectedBankRecord($bank), Cache::get($recordKey));

        $this->launch('banks-observer-deleted', $flags, $payload, $this->writerPrefix, $owned);
        $this->assertNull(Cache::get($recordKey));
        $this->assertSame('banks:active:v3', $cache->versionedKey('banks:active', $versionKey));
        $afterDelete = $this->bankReader();
        $this->assertSame('PR25LOADER', $afterDelete->getActiveBanks()->first()->code);
        $this->assertSame(1, $afterDelete->loaderCalls);
    }

    public function test_channel_observer_create_then_delete_is_visible_to_merchant(): void
    {
        $flags = $this->allOn();
        $this->applyFlags($flags);
        $this->useCachePrefix($this->writerPrefix);
        $payload = ['channel' => $this->channelSpec()];
        $key = CacheKeys::channel($this->entityId);

        $this->launch('channels-observer-created', $flags, $payload, $this->writerPrefix, [$key]);
        $hit = $this->channelReader();
        $this->assertSame('pr25-channel', $hit->get($this->entityId)->name);
        $this->assertSame(0, $hit->loaderCalls);

        $this->launch('channels-observer-deleted', $flags, $payload, $this->writerPrefix, [$key]);
        $this->assertNull(Cache::get($key));
        $miss = $this->channelReader();
        $this->assertSame('pr25-loader', $miss->get($this->entityId)->name);
        $this->assertSame(1, $miss->loaderCalls);
    }

    public function test_profile_observer_update_then_delete_is_visible_to_merchant(): void
    {
        $flags = $this->allOn();
        $this->applyFlags($flags);
        $this->useCachePrefix($this->writerPrefix);
        $payload = ['user' => $this->userSpec('pr25-before')];
        $profileKey = CacheKeys::merchantProfile($this->entityId);
        $versionKey = CacheKeys::merchantsDropdownVersion();
        $owned = [$profileKey, $versionKey];

        $this->launch('profile-observer-updated', $flags, $payload, $this->writerPrefix, $owned);
        $hit = $this->profileReader();
        $this->assertSame('pr25-renamed', $hit->get($this->entityId)->name);
        $this->assertSame(0, $hit->loaderCalls);
        $cache = $this->app->make(SureBillsCache::class);
        $this->assertSame('merchants:dropdown:v8', $cache->versionedKey('merchants:dropdown', $versionKey));

        $this->launch('profile-observer-deleted', $flags, $payload, $this->writerPrefix, $owned);
        $this->assertNull(Cache::get($profileKey));
        $this->assertSame('merchants:dropdown:v9', $cache->versionedKey('merchants:dropdown', $versionKey));
        $miss = $this->profileReader();
        $this->assertSame('pr25-loader', $miss->get($this->entityId)->name);
        $this->assertSame(1, $miss->loaderCalls);
    }

    public function test_settings_flag_off_uses_loader_and_writes_nothing(): void
    {
        $this->assertDomainDisabled('settings', true);
    }

    public function test_banks_flag_off_uses_loader_and_writes_nothing(): void
    {
        $this->assertDomainDisabled('banks', true);
    }

    public function test_channels_flag_off_uses_loader_and_writes_nothing(): void
    {
        $this->assertDomainDisabled('channels', true);
    }

    public function test_profile_flag_off_uses_loader_and_writes_nothing(): void
    {
        $this->assertDomainDisabled('profile', true);
    }

    public function test_settings_master_off_uses_loader_and_writes_nothing(): void
    {
        $this->assertDomainDisabled('settings', false);
    }

    public function test_banks_master_off_uses_loader_and_writes_nothing(): void
    {
        $this->assertDomainDisabled('banks', false);
    }

    public function test_channels_master_off_uses_loader_and_writes_nothing(): void
    {
        $this->assertDomainDisabled('channels', false);
    }

    public function test_profile_master_off_uses_loader_and_writes_nothing(): void
    {
        $this->assertDomainDisabled('profile', false);
    }

    public function test_matching_run_scoped_prefixes_share_the_probe_key(): void
    {
        $flags = $this->flags(true, []);
        $key = $this->probeKey();
        $sentinel = 'pr25-prefix-'.$this->runId;
        $this->applyFlags($flags);
        $this->useCachePrefix($this->writerPrefix);
        $this->launch('prefix-probe-put', $flags, [
            'key' => $key,
            'sentinel' => $sentinel,
        ], $this->writerPrefix, [$key]);

        $this->assertSame($sentinel, Cache::get($key));
    }

    public function test_mismatched_cache_prefixes_do_not_share_settings(): void
    {
        $flags = $this->allOn();
        $sentinel = 'pr25-settings-'.$this->runId;
        $mismatch = $this->writerPrefix.'mismatch_';
        $key = CacheKeys::basicSettings();
        $this->applyFlags($flags);
        $this->own($mismatch, [$key]);
        $this->launch('settings-put', $flags, ['sentinel' => $sentinel], $this->writerPrefix, [$key]);

        $this->useCachePrefix($mismatch);
        $reader = $this->settingsReader();
        $this->assertSame(['pr25_loader' => 'loader'], $reader->getSettings());
        $this->assertSame(1, $reader->loaderCalls);

        $this->useCachePrefix($this->writerPrefix);
        $this->assertSame(['pr25_marker' => $sentinel], Cache::get($key));
    }

    public function test_production_prefix_formula_uses_a_run_specific_logical_key(): void
    {
        $flags = $this->flags(true, []);
        $logical = $this->probeKey();
        $sentinel = 'pr25-prefix-'.$this->runId;
        $physical = self::REDIS_PREFIX.'sb_'.$logical;
        $this->applyFlags($flags);
        $this->useCachePrefix('sb_');
        $this->launch('prefix-probe-put', $flags, [
            'key' => $logical,
            'sentinel' => $sentinel,
        ], 'sb_', [$logical]);

        $this->assertSame('sb_database_sb_'.$logical, $physical);
        $this->assertSame($sentinel, Cache::get($logical));
        $this->assertSame($sentinel, unserialize($this->rawGet($physical)));
    }

    private function assertDomainDisabled(string $domain, bool $masterOn): void
    {
        $domainFlag = $this->domainFlag($domain);
        $flags = $masterOn
            ? $this->flags(true, [])
            : $this->flags(false, ['basic_settings', 'banks', 'channels', 'merchant_profile']);
        $this->applyFlags($flags);
        $this->useCachePrefix($this->writerPrefix);

        [$command, $payload, $keys] = $this->disabledCommand($domain);
        foreach ($keys as $key) {
            $this->assertNull(Cache::get($key));
        }
        $this->launch($command, $flags, $payload, $this->writerPrefix, $keys);
        foreach ($keys as $key) {
            $this->assertNull(Cache::get($key));
        }

        if ($domain === 'settings') {
            $reader = $this->settingsReader();
            $this->assertSame(['pr25_loader' => 'loader'], $reader->getSettings());
            $this->assertSame(1, $reader->loaderCalls);
        } elseif ($domain === 'banks') {
            $reader = $this->bankReader();
            $this->assertSame('PR25LOADER', $reader->getActiveBanks()->first()->code);
            $this->assertSame(1, $reader->loaderCalls);
        } elseif ($domain === 'channels') {
            $reader = $this->channelReader();
            $this->assertSame('pr25-loader', $reader->get($this->entityId)->name);
            $this->assertSame(1, $reader->loaderCalls);
        } else {
            $reader = $this->profileReader();
            $this->assertSame('pr25-loader', $reader->get($this->entityId)->name);
            $this->assertSame(1, $reader->loaderCalls);
        }

        foreach ($keys as $key) {
            $this->assertNull(Cache::get($key), $domainFlag.' stayed absent');
        }
    }

    /**
     * @return array{0: string, 1: array<string, mixed>, 2: list<string>}
     */
    private function disabledCommand(string $domain): array
    {
        return match ($domain) {
            'settings' => ['settings-put', ['sentinel' => 'pr25-off-'.$this->runId], [CacheKeys::basicSettings()]],
            'banks' => ['banks-observer-updated', [
                'bank' => $this->bankSpec(),
                'rows' => $this->bankRows(),
            ], [
                CacheKeys::bank($this->entityId),
                CacheKeys::banksActiveVersion(),
                CacheKeys::banksActive(1),
            ]],
            'channels' => ['channels-observer-created', ['channel' => $this->channelSpec()], [CacheKeys::channel($this->entityId)]],
            'profile' => ['profile-observer-updated', ['user' => $this->userSpec('pr25-before')], [
                CacheKeys::merchantProfile($this->entityId),
                CacheKeys::merchantsDropdownVersion(),
            ]],
            default => throw new RuntimeException('Unknown domain '.$domain),
        };
    }

    private function domainFlag(string $domain): string
    {
        return match ($domain) {
            'settings' => 'basic_settings',
            'banks' => 'banks',
            'channels' => 'channels',
            'profile' => 'merchant_profile',
            default => $domain,
        };
    }

    /**
     * @param list<string> $domainsOn
     * @return array<string, bool>
     */
    private function flags(bool $master, array $domainsOn): array
    {
        $flags = [
            'master' => $master,
            'basic_settings' => false,
            'banks' => false,
            'channels' => false,
            'merchant_profile' => false,
        ];
        foreach ($domainsOn as $domain) {
            $flags[$domain] = true;
        }

        return $flags;
    }

    /**
     * @return array<string, bool>
     */
    private function allOn(): array
    {
        return $this->flags(true, ['basic_settings', 'banks', 'channels', 'merchant_profile']);
    }

    /**
     * @param array<string, bool> $flags
     */
    private function applyFlags(array $flags): void
    {
        config([
            'surebills_cache.enabled' => $flags['master'],
            'surebills_cache.domains.basic_settings' => $flags['basic_settings'],
            'surebills_cache.domains.banks' => $flags['banks'],
            'surebills_cache.domains.channels' => $flags['channels'],
            'surebills_cache.domains.merchant_profile' => $flags['merchant_profile'],
            'surebills_cache.log_sample_rate' => 0,
        ]);
    }

    private function useCachePrefix(string $cachePrefix): void
    {
        if ($this->redisDb === 0 || $this->redisDb === 1) {
            $this->fail('PR-25 refused Redis DB '.$this->redisDb);
        }

        config([
            'cache.default' => 'redis',
            'cache.prefix' => $cachePrefix,
            'cache.stores.redis.connection' => 'cache',
            'database.redis.client' => 'phpredis',
            'database.redis.options.prefix' => self::REDIS_PREFIX,
            'database.redis.cache.url' => null,
            'database.redis.default.url' => null,
            'database.redis.cache.database' => (string) $this->redisDb,
            'database.redis.default.database' => (string) $this->redisDb,
            'database.redis.cache.timeout' => 2,
            'database.redis.cache.read_timeout' => 2,
            'database.redis.default.timeout' => 2,
            'database.redis.default.read_timeout' => 2,
        ]);

        $this->app->make('redis')->purge('cache');
        $this->app->make('redis')->purge('default');
        Cache::forgetDriver('redis');
        Cache::forgetDriver();
    }

    /**
     * @param list<string> $logicalKeys
     */
    private function own(string $cachePrefix, array $logicalKeys): void
    {
        foreach ($logicalKeys as $key) {
            self::$plan['items'][] = [
                'cache_prefix' => $cachePrefix,
                'redis_prefix' => self::REDIS_PREFIX,
                'key' => $key,
            ];
        }
        self::$plan['done'] = false;
    }

    /**
     * @param array<string, bool> $flags
     * @param array<string, mixed> $payload
     * @param list<string> $allowlist
     */
    private function launch(string $command, array $flags, array $payload, string $cachePrefix, array $allowlist): void
    {
        $this->own($cachePrefix, $allowlist);
        $reported = $this->startProbe($command, $flags, $payload, $cachePrefix);
        $extra = array_values(array_diff($reported, $allowlist));
        $this->assertSame([], $extra, 'Admin probe reported a key outside the allowlist.');
    }

    /**
     * @param array<string, bool> $flags
     * @param array<string, mixed> $payload
     * @return list<string>
     */
    private function startProbe(string $command, array $flags, array $payload, string $cachePrefix): array
    {
        $script = '/var/www/surebills-admin-dashboard/tests/CrossApp/write_probe.php';
        $meta = [
            'db' => $this->redisDb,
            'cache_prefix' => $cachePrefix,
            'redis_prefix' => self::REDIS_PREFIX,
        ];
        $commandLine = [
            PHP_BINARY,
            $script,
            $command,
            json_encode($meta, JSON_THROW_ON_ERROR),
            json_encode($flags, JSON_THROW_ON_ERROR),
            json_encode($payload, JSON_THROW_ON_ERROR),
        ];

        $pipes = [];
        $process = proc_open($commandLine, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes, '/var/www/surebills-admin-dashboard', $this->probeEnvironment($cachePrefix));

        if (! is_resource($process)) {
            throw new RuntimeException('PR-25 could not start the Admin probe.');
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        $decoded = json_decode((string) $stdout, true);
        if ($exitCode !== 0 || ! is_array($decoded) || ($decoded['ok'] ?? false) !== true) {
            throw new RuntimeException(
                'Admin probe '.$command.' failed ('.$exitCode.'): '.trim((string) $stderr)
            );
        }

        return array_values($decoded['keys'] ?? []);
    }

    /**
     * @return array<string, string>
     */
    private function probeEnvironment(string $cachePrefix): array
    {
        $env = [
            'APP_ENV' => 'testing',
            'APP_DEBUG' => 'false',
            'DEBUGBAR_ENABLED' => 'false',
            'TELESCOPE_ENABLED' => 'false',
            'SESSION_DRIVER' => 'array',
            'QUEUE_CONNECTION' => 'sync',
            'MAIL_MAILER' => 'array',
            'CACHE_DRIVER' => 'redis',
            'REDIS_CLIENT' => 'phpredis',
            'REDIS_URL' => '',
            'REDIS_HOST' => (string) config('database.redis.cache.host'),
            'REDIS_PORT' => (string) config('database.redis.cache.port'),
            'REDIS_CACHE_DB' => (string) $this->redisDb,
            'REDIS_DB' => (string) $this->redisDb,
            'REDIS_PREFIX' => self::REDIS_PREFIX,
            'CACHE_PREFIX' => $cachePrefix,
            'PR25_REDIS_DB' => (string) $this->redisDb,
            'PR25_REDIS_DB_CONFIRM' => self::CONFIRM,
        ];

        foreach (['PATH', 'HOME', 'LANG', 'LC_ALL', 'LD_LIBRARY_PATH'] as $key) {
            $value = getenv($key);
            if (is_string($value) && $value !== '') {
                $env[$key] = $value;
            }
        }

        $password = config('database.redis.cache.password');
        if (is_string($password) && $password !== '') {
            $env['REDIS_PASSWORD'] = $password;
        }

        $username = config('database.redis.cache.username');
        if (is_string($username) && $username !== '') {
            $env['REDIS_USERNAME'] = $username;
        }

        return $env;
    }

    private function rawGet(string $physicalKey): string
    {
        if ($this->redisDb === 0 || $this->redisDb === 1) {
            $this->fail('PR-25 refused Redis DB '.$this->redisDb);
        }

        $redis = new \Redis();
        $redis->connect((string) self::$plan['host'], (int) self::$plan['port'], 2.0);
        if (self::$plan['password'] !== '') {
            $redis->auth(self::$plan['password']);
        }
        if (! $redis->select($this->redisDb)) {
            $redis->close();
            $this->fail('PR-25 could not select the dedicated Redis DB.');
        }

        $value = $redis->get($physicalKey);
        $redis->close();
        if (! is_string($value)) {
            $this->fail('PR-25 physical key was missing: '.$physicalKey);
        }

        return $value;
    }

    private function armCleanupPlan(): void
    {
        self::$plan = [
            'done' => false,
            'host' => (string) config('database.redis.cache.host', '127.0.0.1'),
            'port' => (int) config('database.redis.cache.port', 6379),
            'password' => (string) (config('database.redis.cache.password') ?? ''),
            'db' => $this->redisDb,
            'items' => [],
        ];

        if (self::$shutdownRegistered) {
            return;
        }

        self::$shutdownRegistered = true;
        register_shutdown_function(static function (): void {
            try {
                self::deleteOwnedKeys();
            } catch (Throwable $e) {
                fwrite(STDERR, 'PR-25 shutdown cleanup failed: '.$e->getMessage()."\n");
            }
        });
    }

    public static function deleteOwnedKeys(): void
    {
        $plan = self::$plan;
        if (! is_array($plan) || ($plan['done'] ?? false)) {
            return;
        }

        $items = $plan['items'] ?? [];
        if ($items === []) {
            self::$plan['done'] = true;

            return;
        }

        $db = (int) $plan['db'];
        if ($db === 0 || $db === 1) {
            throw new RuntimeException('PR-25 refused cleanup on Redis DB '.$db);
        }

        $physical = [];
        foreach ($items as $item) {
            $physical[] = $item['redis_prefix'].$item['cache_prefix'].$item['key'];
        }
        $physical = array_values(array_unique($physical));

        try {
            $redis = new \Redis();
            $redis->connect((string) $plan['host'], (int) $plan['port'], 2.0);
            if ($plan['password'] !== '') {
                $redis->auth($plan['password']);
            }
            if (! $redis->select($db)) {
                throw new RuntimeException('PR-25 could not select Redis DB '.$db.' for cleanup.');
            }
            foreach ($physical as $key) {
                $redis->del($key);
            }
            $redis->close();
            self::$plan['items'] = [];
            self::$plan['done'] = true;
        } catch (Throwable $e) {
            fwrite(STDERR, 'PR-25 cleanup failed on Redis DB '.$db.' for '.count($physical)." exact keys.\n");
            throw $e;
        }
    }

    private static function optInConfirmed(): bool
    {
        $db = self::envValue('PR25_REDIS_DB');
        $confirm = self::envValue('PR25_REDIS_DB_CONFIRM');
        if ($db === null || $confirm !== self::CONFIRM || ! preg_match('/^[0-9]+$/', $db)) {
            return false;
        }

        $index = (int) $db;

        return $index !== 0 && $index !== 1;
    }

    private static function envValue(string $name): ?string
    {
        $candidates = [
            getenv($name),
            $_ENV[$name] ?? null,
            $_SERVER[$name] ?? null,
        ];
        foreach ($candidates as $value) {
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function settingsReader(): GuardedBasicSettingsService
    {
        return new GuardedBasicSettingsService($this->app->make(SureBillsCache::class));
    }

    private function bankReader(): GuardedBankActiveListService
    {
        return new GuardedBankActiveListService($this->app->make(SureBillsCache::class));
    }

    private function channelReader(): GuardedChannelFeeConfigService
    {
        return new GuardedChannelFeeConfigService($this->app->make(SureBillsCache::class));
    }

    private function profileReader(): GuardedMerchantProfileService
    {
        return new GuardedMerchantProfileService($this->app->make(SureBillsCache::class));
    }

    private function probeKey(): string
    {
        return 'pr25:'.$this->runId.':prefix-probe';
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function bankRows(): array
    {
        return [[
            'id' => $this->entityId,
            'code' => 'PR25CACHE',
            'name' => ['en' => 'PR25', 'ar' => 'PR25'],
            'sort_number' => 1,
        ]];
    }

    /**
     * @return array<string, mixed>
     */
    private function bankSpec(): array
    {
        return [
            'id' => $this->entityId,
            'code' => 'PR25BANK',
            'name' => ['en' => 'PR25', 'ar' => 'PR25'],
            'active' => 1,
            'sort_number' => 4,
            'fees' => 0,
        ];
    }

    /**
     * @param array<string, mixed> $bank
     * @return array<string, mixed>
     */
    private function expectedBankRecord(array $bank): array
    {
        return [
            'id' => $bank['id'],
            'code' => $bank['code'],
            'name' => $bank['name'],
            'active' => $bank['active'],
            'sort_number' => $bank['sort_number'],
            'fees' => $bank['fees'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function channelSpec(): array
    {
        return [
            'id' => $this->entityId,
            'user_id' => $this->entityId,
            'name' => 'pr25-channel',
            'mada_fixed' => '1.25',
            'mada_percentage' => '2.50',
            'credit_cards_fixed' => '3.00',
            'credit_cards_percentage' => '4.75',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function userSpec(string $name): array
    {
        return [
            'id' => $this->entityId,
            'name' => $name,
            'email' => 'pr25-'.$this->entityId.'@example.test',
            'mobile' => '0500000000',
            'mobile_verified' => 1,
            'business_name_en' => 'PR25',
            'business_name_ar' => 'PR25',
            'logo' => null,
            'verified' => 1,
            'store_main_user_id' => null,
            'source' => 'pr25',
        ];
    }
}
