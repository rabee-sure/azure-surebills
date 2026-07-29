<?php

namespace App\Services\Cache;

use Closure;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Thin Laravel Cache helper for SureBills Redis domains (PR-01).
 *
 * Contract only — no domain business logic.
 * Structured hit/miss logging is deferred to PR-04.
 */
class SureBillsCache
{
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

        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (Throwable $e) {
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

        try {
            return Cache::rememberForever($key, $callback);
        } catch (Throwable $e) {
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
        try {
            if ($ttl === null) {
                return (bool) Cache::forever($key, $value);
            }

            return (bool) Cache::put($key, $value, $ttl);
        } catch (Throwable $e) {
            if ($this->failOpen()) {
                return false;
            }

            throw $e;
        }
    }

    public function forget(string $key): bool
    {
        try {
            return (bool) Cache::forget($key);
        } catch (Throwable $e) {
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
        try {
            return (int) Cache::increment($versionKey);
        } catch (Throwable $e) {
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
        try {
            $version = Cache::get($versionKey);

            if ($version === null || $version === false) {
                $version = 1;
            }

            return $baseKey . ':v' . (int) $version;
        } catch (Throwable $e) {
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
}
