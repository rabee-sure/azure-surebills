<?php

namespace Tests\Unit;

use App\Services\OtpService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class OtpServiceResendCountdownTest extends TestCase
{
    public function test_seconds_until_resend(): void
    {
        $now = Carbon::parse('2026-09-01 12:00:00');

        $this->assertSame(0, OtpService::secondsUntilResend(null, $now));
        $this->assertSame(60, OtpService::secondsUntilResend($now->copy(), $now));
        $this->assertSame(59, OtpService::secondsUntilResend($now->copy()->subSecond(), $now));
        $this->assertSame(0, OtpService::secondsUntilResend($now->copy()->subSeconds(60), $now));
        $this->assertSame(0, OtpService::secondsUntilResend($now->copy()->subSeconds(90), $now));
    }
}
