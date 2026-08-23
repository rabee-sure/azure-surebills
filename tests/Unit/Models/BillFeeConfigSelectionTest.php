<?php

namespace Tests\Unit\Models;

use App\Models\Application;
use App\Models\Bill;
use App\Models\Channel;
use App\Models\PaymentLog;
use App\Models\User;
use App\Services\ApplicationFeeConfigService;
use App\Services\ChannelFeeConfigService;
use Illuminate\Container\Container;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class BillFeeConfigSelectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private ?Container $previousContainer = null;
    private Container $container;
    private $applicationCache;
    private $channelCache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = Container::getInstance();
        $this->container = new Container;
        Container::setInstance($this->container);

        $this->applicationCache = Mockery::mock(ApplicationFeeConfigService::class);
        $this->channelCache = Mockery::mock(ChannelFeeConfigService::class);
        $this->container->instance(ApplicationFeeConfigService::class, $this->applicationCache);
        $this->container->instance(ChannelFeeConfigService::class, $this->channelCache);

        $this->applicationCache->shouldReceive('get')->never();
        $this->channelCache->shouldReceive('get')->never();
    }

    protected function tearDown(): void
    {
        Container::setInstance($this->previousContainer);
        parent::tearDown();
    }

    private function log(string $brand): PaymentLog
    {
        return (new PaymentLog())->forceFill(['brand' => $brand]);
    }

    private function user(): User
    {
        return (new User())->forceFill([
            'mada_fixed' => '7.00000',
            'mada_percentage' => '8.00000',
            'credit_cards_fixed' => '9.00000',
            'credit_cards_percentage' => '10.00000',
        ]);
    }

    public function test_case_a_uses_live_application_and_channel_rates(): void
    {
        $channel = (new Channel())->forceFill([
            'id' => 51,
            'mada_fixed' => '4.00000',
            'mada_percentage' => '5.00000',
            'credit_cards_fixed' => '6.00000',
            'credit_cards_percentage' => '7.00000',
        ]);
        $application = (new Application())->forceFill([
            'id' => 41,
            'channel_id' => 51,
            'mada_fixed' => '0.12345',
            'mada_percentage' => '1.25000',
            'credit_cards_fixed' => '2.50000',
            'credit_cards_percentage' => '3.75000',
        ]);
        $application->setRelation('channel', $channel);

        $bill = (new Bill())->forceFill(['application_id' => 41]);
        $bill->setRelation('application', $application);
        $bill->setRelation('user', $this->user());

        $this->assertSame('1.25000', $bill->getPercentage($this->log('MADA')));
        $this->assertSame('0.12345', $bill->getFixed($this->log('MADA')));
        $this->assertSame('7.00000', $bill->getPercentage($this->log('VISA'), true));
        $this->assertSame('6.00000', $bill->getFixed($this->log('VISA'), true));
    }

    public function test_case_b_ignores_application_rates_and_uses_user_rates(): void
    {
        $standaloneApplication = (new Application())->forceFill([
            'id' => 42,
            'channel_id' => null,
            'mada_fixed' => '0.01000',
            'mada_percentage' => '0.02000',
            'credit_cards_fixed' => '0.03000',
            'credit_cards_percentage' => '0.04000',
        ]);
        $bill = (new Bill())->forceFill(['application_id' => 42]);
        $bill->setRelation('application', $standaloneApplication);
        $standaloneApplication->setRelation('channel', null);
        $bill->setRelation('user', $this->user());

        $this->assertSame('8.00000', $bill->getPercentage($this->log('MADA')));
        $this->assertSame('7.00000', $bill->getFixed($this->log('MADA')));
        $this->assertSame('10.00000', $bill->getPercentage($this->log('VISA')));
        $this->assertSame('9.00000', $bill->getFixed($this->log('VISA')));
    }

    public function test_stale_redis_application_and_channel_rates_do_not_affect_financial_selection(): void
    {
        $channel = (new Channel())->forceFill([
            'id' => 71,
            'mada_percentage' => '7.00000',
            'mada_fixed' => '0.70000',
            'credit_cards_percentage' => '1.00000',
            'credit_cards_fixed' => '1.00000',
        ]);
        $application = (new Application())->forceFill([
            'id' => 61,
            'channel_id' => 71,
            'mada_percentage' => '5.00000',
            'mada_fixed' => '0.50000',
            'credit_cards_percentage' => '1.00000',
            'credit_cards_fixed' => '1.00000',
        ]);
        $application->setRelation('channel', $channel);

        $bill = (new Bill())->forceFill(['application_id' => 61]);
        $bill->setRelation('application', $application);
        $bill->setRelation('user', $this->user());

        $this->assertSame('5.00000', $bill->getPercentage($this->log('MADA')));
        $this->assertSame('7.00000', $bill->getPercentage($this->log('MADA'), true));
    }
}
