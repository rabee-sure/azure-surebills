<?php

namespace Tests\Feature\Cache\Support;

use App\Models\Bank;
use App\Models\Channel;
use App\Models\User;
use App\Services\BankActiveListService;
use App\Services\BasicSettingsService;
use App\Services\ChannelFeeConfigService;
use App\Services\MerchantProfileService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Test doubles that replace only the protected database loaders.
 * A hit must leave the counter at 0. A disabled read must increment it.
 * beforeExecuting throws before any SQL is sent.
 */
function forbidSql(): void
{
    DB::beforeExecuting(function (string $query): void {
        throw new RuntimeException('PR-25 blocked SQL before execution: '.$query);
    });
}

class GuardedBasicSettingsService extends BasicSettingsService
{
    public int $loaderCalls = 0;

    protected function loadSettingsFromDatabase(): array
    {
        $this->loaderCalls++;

        return ['pr25_loader' => 'loader'];
    }
}

class GuardedBankActiveListService extends BankActiveListService
{
    public int $loaderCalls = 0;

    protected function loadActiveBanksFromDatabase(): Collection
    {
        $this->loaderCalls++;

        $bank = new Bank();
        $bank->forceFill([
            'id' => 0,
            'code' => 'PR25LOADER',
            'sort_number' => 0,
        ]);

        return new Collection([$bank]);
    }
}

class GuardedChannelFeeConfigService extends ChannelFeeConfigService
{
    public int $loaderCalls = 0;

    protected function loadFromDatabase(int $id): ?Channel
    {
        $this->loaderCalls++;

        $channel = new Channel();
        $channel->forceFill([
            'id' => $id,
            'user_id' => $id,
            'name' => 'pr25-loader',
            'mada_fixed' => '0.00',
            'mada_percentage' => '0.00',
            'credit_cards_fixed' => '0.00',
            'credit_cards_percentage' => '0.00',
        ]);

        return $channel;
    }
}

class GuardedMerchantProfileService extends MerchantProfileService
{
    public int $loaderCalls = 0;

    protected function loadFromDatabase(int $id): ?User
    {
        $this->loaderCalls++;

        $user = new User();
        $user->forceFill([
            'id' => $id,
            'name' => 'pr25-loader',
            'email' => 'pr25-loader@example.test',
            'mobile' => '0500000000',
            'mobile_verified' => 1,
            'business_name_en' => 'PR25',
            'business_name_ar' => 'PR25',
            'logo' => null,
            'verified' => 1,
            'store_main_user_id' => null,
            'source' => 'pr25',
        ]);

        return $user;
    }
}
