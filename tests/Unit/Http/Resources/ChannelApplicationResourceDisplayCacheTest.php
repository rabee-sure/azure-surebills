<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\ChannelApplicationResource;
use App\Models\Application;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ChannelApplicationResourceDisplayCacheTest extends TestCase
{
    public function test_resource_uses_cached_fee_configuration_without_changing_non_cache_fields(): void
    {
        $databaseChannel = (new Channel())->forceFill([
            'id' => 7,
            'name' => 'Database Channel',
            'mada_percentage' => '7.00',
            'sub_account_status_webhook' => 'https://example.test/status',
        ]);
        $cachedChannel = (new Channel())->forceFill([
            'id' => 7,
            'name' => 'Cached Channel',
            'mada_fixed' => '98.00',
            'mada_percentage' => '99.00',
            'credit_cards_fixed' => '97.00',
            'credit_cards_percentage' => '96.00',
        ]);
        $application = (new Application())->forceFill([
            'id' => 21,
            'name' => 'Application',
            'secret' => 'response-contract-secret',
            'mada_fixed' => '0.50000',
            'mada_percentage' => '5.00000',
            'credit_cards_fixed' => '0.60000',
            'credit_cards_percentage' => '6.00000',
        ]);
        $cachedApplication = (new Application())->forceFill([
            'id' => 21,
            'mada_fixed' => '98.00000',
            'mada_percentage' => '99.00000',
            'credit_cards_fixed' => '97.00000',
            'credit_cards_percentage' => '96.00000',
        ]);
        $application->setRelation('user', (new User())->forceFill(['email' => 'display@example.test']));
        $application->setRelation('channel', $databaseChannel);
        $application->setRelation('displayFeeConfig', $cachedApplication);
        $application->setRelation('displayChannelFeeConfig', $cachedChannel);

        $result = (new ChannelApplicationResource($application))->toArray(Request::create('/'));

        $this->assertSame('99.00000', $result['mada_percentage']);
        $this->assertSame('98.00000', $result['mada_fixed']);
        $this->assertSame('Cached Channel', $result['channel']->name);
        $this->assertSame('99.00', $result['channel']->mada_percentage);
        $this->assertSame('https://example.test/status', $result['channel']->sub_account_status_webhook);
        $this->assertSame('response-contract-secret', $result['secret']);
        $this->assertSame('5.00000', $application->mada_percentage);
        $this->assertSame('7.00', $databaseChannel->mada_percentage);
    }

    public function test_edit_source_without_display_cache_uses_database_fee_fields(): void
    {
        $channel = (new Channel())->forceFill([
            'id' => 9,
            'mada_percentage' => '7.50',
        ]);
        $application = (new Application())->forceFill([
            'id' => 23,
            'mada_fixed' => '0.50000',
            'mada_percentage' => '5.50000',
            'credit_cards_fixed' => '0.60000',
            'credit_cards_percentage' => '6.25000',
        ]);
        $application->setRelation('user', (new User())->forceFill(['email' => 'edit@example.test']));
        $application->setRelation('channel', $channel);

        $result = (new ChannelApplicationResource($application))->toArray(Request::create('/'));

        $this->assertSame('5.50000', $result['mada_percentage']);
        $this->assertSame('0.50000', $result['mada_fixed']);
        $this->assertSame('6.25000', $result['credit_cards_percentage']);
    }

    public function test_resource_falls_back_to_database_models_when_display_cache_is_not_attached(): void
    {
        $channel = (new Channel())->forceFill([
            'id' => 8,
            'name' => 'Database Channel',
            'mada_percentage' => '7.00',
        ]);
        $application = (new Application())->forceFill([
            'id' => 22,
            'mada_percentage' => '5.00000',
        ]);
        $application->setRelation('user', (new User())->forceFill(['email' => 'display@example.test']));
        $application->setRelation('channel', $channel);

        $result = (new ChannelApplicationResource($application))->toArray(Request::create('/'));

        $this->assertSame('5.00000', $result['mada_percentage']);
        $this->assertSame('7.00', $result['channel']->mada_percentage);
    }
}
