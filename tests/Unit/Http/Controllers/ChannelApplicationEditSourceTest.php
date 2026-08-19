<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ChannelApplicationController;
use App\Http\Resources\ChannelApplicationResource;
use App\Models\Application;
use App\Models\Channel;
use App\Models\User;
use App\Services\ApplicationFeeConfigService;
use App\Services\ChannelFeeConfigService;
use Illuminate\Http\Request;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ChannelApplicationEditSourceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_show_returns_database_fees_and_never_reads_cache(): void
    {
        $application = Mockery::mock(Application::class)->makePartial();
        $application->forceFill([
            'id' => 21,
            'mada_fixed' => '0.50000',
            'mada_percentage' => '5.50000',
            'credit_cards_fixed' => '0.60000',
            'credit_cards_percentage' => '6.25000',
            'redirect' => 'https://example.test/ok',
            'webhook_url' => 'https://example.test/hook',
        ]);
        $application->shouldReceive('load')->once()->andReturnSelf();
        $application->setRelation('user', (new User())->forceFill(['email' => 'edit@example.test']));
        $application->setRelation('channel', (new Channel())->forceFill(['id' => 7]));

        $applicationCache = Mockery::mock(ApplicationFeeConfigService::class);
        $channelCache = Mockery::mock(ChannelFeeConfigService::class);
        $applicationCache->shouldReceive('get')->never();
        $channelCache->shouldReceive('get')->never();

        $resource = (new ChannelApplicationController($applicationCache, $channelCache))
            ->show(new Channel(), $application);

        $this->assertInstanceOf(ChannelApplicationResource::class, $resource);
        $payload = $resource->toArray(Request::create('/'));
        $this->assertSame('5.50000', $payload['mada_percentage']);
        $this->assertSame('0.50000', $payload['mada_fixed']);
        $this->assertFalse($application->relationLoaded('displayFeeConfig'));
    }

    public function test_vue_edit_fetches_show_endpoint_instead_of_list_fee_fields(): void
    {
        $vueFiles = [
            '/var/www/sure-bills-dev/resources/assets/v2/js/components/ChannelApplications.vue',
            '/var/www/sure-bills-dev/resources/js/components/ChannelApplications.vue',
            '/var/www/sure-bills-dev/resources/new/js/components/ChannelApplications.vue',
        ];

        foreach ($vueFiles as $path) {
            $source = file_get_contents($path);
            $this->assertStringContainsString(
                "/applications/' + application.id",
                $source,
                $path
            );
            $this->assertStringNotContainsString(
                'this.editForm.mada_percentage = application.mada_percentage',
                $source,
                $path
            );
        }
    }
}
