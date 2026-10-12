<?php

namespace App\Services\Cache;

use Closure;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Thin Laravel Cache helper for SureBills Redis domains.
 *
 * Contract only — no domain business logic.
 * PR-04: sampled hit/miss/error structured logging (P0-05).
 *
 * Hit/miss for remember* is observed via Laravel CacheHit/CacheMissed events
 * so Cache::remember / rememberForever semantics are unchanged.
 */
class SureBillsCache
{
    protected const APP = 'merchant';

    /** @var bool */
    protected static $cacheEventListenersRegistered = false;

    /**
     * Stack of in-flight remember* observations: [['key' => string, 'result' => ?string], ...]
     *
     * @var array<int, array{key: string, result: ?string}>
     */
    protected static $watchStack = [];

    /**
     * Domain is usable only when the master switch and the domain flag are both true.
     */
    public function enabled(string $domain): bool
    {
        if (! config('surebills_cache.enabled', false)) {
            return false;
        }

        return (bool) config('surebills_cache.domains.' . $domain, false);
    }

    /**
     * Cache-aside remember. When caching is disabled for the domain (or master),
     * runs the callback without touching the cache store.
     *
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return mixed
     */
    public function remember(string $key, $ttl, Closure $callback, ?string $domain = null)
    {
        if (! $this->shouldUseCache($domain)) {
            return $callback();
        }

        $started = hrtime(true);

        try {
            if ($this->loggingEnabled()) {
                [$value, $outcome] = $this->withCacheObservation($key, function () use ($key, $ttl, $callback) {
                    return Cache::remember($key, $ttl, $callback);
                });
                $this->logCacheEvent($domain, 'get', $key, $outcome ?? 'miss', $started, true);

                return $value;
            }

            return Cache::remember($key, $ttl, $callback);
        } catch (Throwable $e) {
            $this->logCacheEvent($domain, 'get', $key, 'error', $started, false);

            if ($this->failOpen()) {
                return $callback();
            }

            throw $e;
        }
    }

    /**
     * @return mixed
     */
    public function rememberForever(string $key, Closure $callback, ?string $domain = null)
    {
        if (! $this->shouldUseCache($domain)) {
            return $callback();
        }

        $started = hrtime(true);

        try {
            if ($this->loggingEnabled()) {
                [$value, $outcome] = $this->withCacheObservation($key, function () use ($key, $callback) {
                    return Cache::rememberForever($key, $callback);
                });
                $this->logCacheEvent($domain, 'get', $key, $outcome ?? 'miss', $started, true);

                return $value;
            }

            return Cache::rememberForever($key, $callback);
        } catch (Throwable $e) {
            $this->logCacheEvent($domain, 'get', $key, 'error', $started, false);

            if ($this->failOpen()) {
                return $callback();
            }

            throw $e;
        }
    }

    /**
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @param  mixed  $value
     */
    public function put(string $key, $value, $ttl = null): bool
    {
        $started = hrtime(true);

        try {
            if ($ttl === null) {
                return (bool) Cache::forever($key, $value);
            }

            return (bool) Cache::put($key, $value, $ttl);
        } catch (Throwable $e) {
            $this->logCacheEvent(null, 'put', $key, 'error', $started, false);

            if ($this->failOpen()) {
                return false;
            }

            throw $e;
        }
    }

    public function forget(string $key): bool
    {
        $started = hrtime(true);

        try {
            return (bool) Cache::forget($key);
        } catch (Throwable $e) {
            $this->logCacheEvent(null, 'forget', $key, 'error', $started, false);

            if ($this->failOpen()) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * Increment a version key. Returns 0 on fail-open errors (callers treat as miss).
     */
    public function bumpVersion(string $versionKey): int
    {
        $started = hrtime(true);

        try {
            return (int) Cache::increment($versionKey);
        } catch (Throwable $e) {
            $this->logCacheEvent(null, 'bump', $versionKey, 'error', $started, false);

            if ($this->failOpen()) {
                return 0;
            }

            throw $e;
        }
    }

    /**
     * Build "{base}:v{n}". Missing version key is treated as version 1.
     */
    public function versionedKey(string $baseKey, string $versionKey): string
    {
        $started = hrtime(true);

        try {
            if ($this->loggingEnabled()) {
                [$version, $outcome] = $this->withCacheObservation($versionKey, function () use ($versionKey) {
                    return Cache::get($versionKey);
                });
                $this->logCacheEvent(null, 'get', $versionKey, $outcome ?? 'miss', $started, true);
            } else {
                $version = Cache::get($versionKey);
            }

            if ($version === null || $version === false) {
                $version = 1;
            }

            return $baseKey . ':v' . (int) $version;
        } catch (Throwable $e) {
            $this->logCacheEvent(null, 'get', $versionKey, 'error', $started, false);

            if ($this->failOpen()) {
                return $baseKey . ':v1';
            }

            throw $e;
        }
    }

    /**
     * Whether remember* should use the cache store.
     */
    protected function shouldUseCache(?string $domain): bool
    {
        if ($domain === null) {
            return (bool) config('surebills_cache.enabled', false);
        }

        return $this->enabled($domain);
    }

    protected function failOpen(): bool
    {
        return (bool) config('surebills_cache.fail_open', true);
    }

    /**
     * Logging master switch: log_sample_rate <= 0 means OFF (including errors).
     */
    protected function loggingEnabled(): bool
    {
        return (float) config('surebills_cache.log_sample_rate', 0.05) > 0;
    }

    /**
     * @return bool
     */
    protected function shouldSample(): bool
    {
        $rate = (float) config('surebills_cache.log_sample_rate', 0.05);

        if ($rate <= 0) {
            return false;
        }

        if ($rate >= 1) {
            return true;
        }

        return (mt_rand() / mt_getrandmax()) <= $rate;
    }

    /**
     * @param  mixed  $startedHrtime  hrtime(true) start mark
     */
    protected function logCacheEvent(
        ?string $domain,
        string $op,
        string $key,
        string $result,
        $startedHrtime,
        bool $sampleSuccess
    ): void {
        try {
            if (! $this->loggingEnabled()) {
                return;
            }

            if ($result !== 'error' && $sampleSuccess && ! $this->shouldSample()) {
                return;
            }

            $ms = (hrtime(true) - $startedHrtime) / 1e6;

            Log::info('surebills.cache', [
                'domain' => $domain ?? 'none',
                'op' => $op,
                'key' => hash('sha256', $key),
                'result' => $result,
                'ms' => round($ms, 3),
                'app' => self::APP,
            ]);
        } catch (Throwable $e) {
            // Logging must never affect cache/business behavior.
        }
    }

    /**
     * Observe CacheHit/CacheMissed for a single logical key without altering Cache APIs.
     *
     * @template T
     * @param  Closure(): T  $callback
     * @return array{0: T, 1: ?string}
     */
    protected function withCacheObservation(string $key, Closure $callback): array
    {
        $this->registerCacheEventListeners();

        self::$watchStack[] = [
            'key' => $key,
            'result' => null,
        ];

        try {
            $value = $callback();
            $index = array_key_last(self::$watchStack);
            $outcome = $index !== null ? self::$watchStack[$index]['result'] : null;

            return [$value, $outcome];
        } finally {
            array_pop(self::$watchStack);
        }
    }

    protected function registerCacheEventListeners(): void
    {
        if (self::$cacheEventListenersRegistered) {
            return;
        }

        Event::listen(CacheHit::class, static function (CacheHit $event) {
            self::noteCacheObservation($event->key, 'hit');
        });

        Event::listen(CacheMissed::class, static function (CacheMissed $event) {
            self::noteCacheObservation($event->key, 'miss');
        });

        self::$cacheEventListenersRegistered = true;
    }

    protected static function noteCacheObservation(string $key, string $result): void
    {
        if (self::$watchStack === []) {
            return;
        }

        $index = array_key_last(self::$watchStack);

        if ($index === null) {
            return;
        }

        if (self::$watchStack[$index]['key'] === $key && self::$watchStack[$index]['result'] === null) {
            self::$watchStack[$index]['result'] = $result;
        }
    }
}
