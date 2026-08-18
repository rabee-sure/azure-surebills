<?php

namespace App\Services;

use App\Models\Settings;
use App\Models\User;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;

/**
 * Merchant bill Settings cache (PR-09).
 *
 * Display/form-defaults only. Never use this service as the source of
 * truth for API VAT / financial calculations.
 */
class MerchantSettingsService
{
    public function __construct(
        private readonly SureBillsCache $cache,
    ) {}

    /**
     * Resolve cache/DB owner id: main merchant when sub-user, else self.
     */
    public function resolveOwnerId(?User $user): ?int
    {
        if (! $user) {
            return null;
        }

        return (int) ($user->store_main_user_id ?? $user->id);
    }

    /**
     * Load settings for an authenticated (or bill-owning) user with owner scoping.
     */
    public function getForUser(?User $user): ?Settings
    {
        $ownerId = $this->resolveOwnerId($user);

        if ($ownerId === null) {
            return null;
        }

        return $this->getForOwner($ownerId);
    }

    /**
     * @return Settings|null
     */
    public function getForOwner(int $ownerId): ?Settings
    {
        if (! $this->cache->enabled('merchant_settings')) {
            return $this->loadFromDatabase($ownerId);
        }

        $payload = $this->cache->rememberForever(
            CacheKeys::merchantSettings($ownerId),
            fn () => $this->toPayloadOrNull($this->loadFromDatabase($ownerId)),
            'merchant_settings'
        );

        if ($payload === null) {
            return null;
        }

        if (! is_array($payload)) {
            return $this->loadFromDatabase($ownerId);
        }

        return $this->hydrate($payload);
    }

    /**
     * Observer write-through: forever put of serialized settings.
     */
    public function putSettings(Settings $settings): bool
    {
        if (! $this->cache->enabled('merchant_settings')) {
            return false;
        }

        $ownerId = (int) $settings->user_id;

        return $this->cache->put(
            CacheKeys::merchantSettings($ownerId),
            self::toPayload($settings)
        );
    }

    /**
     * Observer invalidation on delete.
     */
    public function forgetSettings(Settings $settings): bool
    {
        if (! $this->cache->enabled('merchant_settings')) {
            return false;
        }

        return $this->cache->forget(
            CacheKeys::merchantSettings((int) $settings->user_id)
        );
    }

    protected function loadFromDatabase(int $ownerId): ?Settings
    {
        return Settings::userId($ownerId)->first();
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function toPayloadOrNull(?Settings $settings): ?array
    {
        return $settings ? self::toPayload($settings) : null;
    }

    /**
     * Redis-safe payload (no relations).
     *
     * @return array<string, mixed>
     */
    public static function toPayload(Settings $settings): array
    {
        return [
            'user_id' => $settings->user_id,
            'add_tax' => $settings->add_tax,
            'tax_value' => $settings->tax_value,
            'add_tax_invoice' => $settings->add_tax_invoice,
            'default_lang' => $settings->default_lang,
            'active_lang' => $settings->active_lang,
            'create_send_sms' => $settings->create_send_sms,
            'create_send_email' => $settings->create_send_email,
            'paid_send_sms' => $settings->paid_send_sms,
            'paid_send_email' => $settings->paid_send_email,
            'header_bill' => $settings->getTranslations('header_bill'),
            'footer_bill' => $settings->getTranslations('footer_bill'),
            'display_customer_details' => $settings->display_customer_details,
            'api_bill_style' => $settings->api_bill_style,
            'background_color_body' => $settings->background_color_body,
            'background_image_file' => $settings->background_image_file,
            'text_color_body' => $settings->text_color_body,
            'background_color_payment_button' => $settings->background_color_payment_button,
            'text_color_payment_button' => $settings->text_color_payment_button,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrate(array $payload): Settings
    {
        $attrs = $payload;

        foreach (['header_bill', 'footer_bill'] as $field) {
            if (isset($attrs[$field]) && is_array($attrs[$field])) {
                $attrs[$field] = json_encode($attrs[$field], JSON_UNESCAPED_UNICODE);
            }
        }

        return Settings::hydrate([$attrs])->first();
    }
}
