<?php

namespace Tests\Unit\Jobs;

use App\Jobs\MakeReverseTransactionsForChannelExtraFees;
use App\Jobs\MakeTransactionsForChannelExtraFees;
use App\Models\Application;
use App\Models\Bill;
use App\Models\Channel;
use App\Models\PaymentLog;
use App\Services\ChannelFeeConfigService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ChannelExtraFeeJobSourceTest extends TestCase
{
    public function test_forward_job_handle_does_not_depend_on_channel_fee_config_service(): void
    {
        $this->assertHandleDoesNotUseCache(MakeTransactionsForChannelExtraFees::class);
    }

    public function test_reverse_job_handle_does_not_depend_on_channel_fee_config_service(): void
    {
        $this->assertHandleDoesNotUseCache(MakeReverseTransactionsForChannelExtraFees::class);
    }

    public function test_live_eloquent_channel_supplies_extra_fee_rates(): void
    {
        $channel = (new Channel())->forceFill([
            'id' => 81,
            'name' => 'Live Channel',
            'user_id' => 9,
            'mada_percentage' => '7.00000',
            'mada_fixed' => '0.70000',
        ]);
        $application = (new Application())->forceFill([
            'id' => 82,
            'channel_id' => 81,
        ]);
        $application->setRelation('channel', $channel);
        $bill = (new Bill())->forceFill(['id' => 1, 'channel_extra_amount' => 10]);
        $bill->setRelation('application', $application);

        $log = (new PaymentLog())->forceFill([
            'results' => ['response' => ['paymentBrand' => 'MADA']],
        ]);

        $this->assertSame($channel, $bill->application->channel);
        $this->assertSame('7.00000', $channel->getPercentage($log));
        $this->assertSame('0.70000', $channel->getFixed($log));
    }

    private function assertHandleDoesNotUseCache(string $jobClass): void
    {
        $method = new ReflectionMethod($jobClass, 'handle');
        $this->assertFalse($method->getNumberOfParameters() > 0 && $this->handleAcceptsCache($method));
        $this->assertFalse($this->handleAcceptsCache($method));
    }

    private function handleAcceptsCache(ReflectionMethod $method): bool
    {
        foreach ($method->getParameters() as $parameter) {
            $type = $parameter->getType();
            $name = $type && method_exists($type, 'getName') ? $type->getName() : null;
            if ($name === ChannelFeeConfigService::class) {
                return true;
            }
        }

        return false;
    }
}
