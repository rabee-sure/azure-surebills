<?php

namespace App\Services\Cache;

/**
 * Logical cache key builders for SureBills Redis domains.
 *
 * Keys must NOT include the Laravel CACHE_PREFIX (e.g. sb_).
 * Final Redis key = CACHE_PREFIX + logical key (Spec §10.6.1).
 *
 * Shared key strings must stay identical to Admin CacheKeys (PR-02).
 */
class CacheKeys
{
    public static function basicSettings(): string
    {
        return 'basic_settings';
    }

    public static function bank($id): string
    {
        return 'bank:' . $id;
    }

    public static function banksActiveVersion(): string
    {
        return 'banks:active:version';
    }

    public static function banksActive($version): string
    {
        return 'banks:active:v' . $version;
    }

    public static function merchantSettings($uid): string
    {
        return 'merchant:' . $uid . ':settings';
    }

    public static function channel($id): string
    {
        return 'channel:' . $id;
    }

    public static function application($id): string
    {
        return 'application:' . $id;
    }

    public static function merchantProfile($uid): string
    {
        return 'merchant:' . $uid . ':profile';
    }

    public static function merchantsDropdownVersion(): string
    {
        return 'merchants:dropdown:version';
    }

    public static function merchantsDropdown($version): string
    {
        return 'merchants:dropdown:v' . $version;
    }

    public static function customer($id): string
    {
        return 'customer:' . $id;
    }

    public static function couponsAvailable($uid, string $ymd): string
    {
        return 'merchant:' . $uid . ':coupons:available:' . $ymd;
    }

    public static function pendingBills($uid): string
    {
        return 'merchant:' . $uid . ':bills:pending';
    }

    public static function uiBalance($uid): string
    {
        return 'merchant:' . $uid . ':ui:balance';
    }

    public static function homeLastBills($uid): string
    {
        return 'merchant:' . $uid . ':home:last_bills';
    }

    public static function homeTotals($uid): string
    {
        return 'merchant:' . $uid . ':home:totals';
    }

    public static function charts($uid, string $metric, string $period, string $from, string $to): string
    {
        return 'merchant:' . $uid . ':charts:' . $metric . ':' . $period . ':' . $from . ':' . $to;
    }

    public static function adminHomeVersion(): string
    {
        return 'admin:home:version';
    }

    public static function adminHome($version, string $from, string $to): string
    {
        return 'admin:home:v' . $version . ':' . $from . ':' . $to;
    }

    public static function adminHomeYearly(string $series): string
    {
        return 'admin:home:yearly:' . $series;
    }

    public static function adminBadge(string $name): string
    {
        return 'admin:badges:' . $name;
    }

    public static function txVersion($uid): string
    {
        return 'merchant:' . $uid . ':tx:version';
    }

    public static function txPage($uid, string $ym, $page): string
    {
        return 'merchant:' . $uid . ':tx:' . $ym . ':p' . $page;
    }

    public static function transfersMonth($uid, string $ym): string
    {
        return 'merchant:' . $uid . ':transfers:' . $ym;
    }

    public static function transfers($uid): string
    {
        return 'merchant:' . $uid . ':transfers';
    }
}
