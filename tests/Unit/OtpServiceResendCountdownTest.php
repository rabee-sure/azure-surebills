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

    public function test_seconds_until_resend_is_integer_and_does_not_accumulate(): void
    {
        $now = Carbon::parse('2026-09-01 12:00:00.250000');
        $sentAt = $now->copy()->subSeconds(12);

        $remaining = OtpService::secondsUntilResend($sentAt, $now);

        $this->assertSame(48, $remaining);
        $this->assertIsInt($remaining);

        // Carbon 3: now->diffInSeconds(sentAt) is signed/float (e.g. -12.25).
        // The old 60 - thatDiff formula grew past 60 (e.g. 72.25, then 231).
        $signedFloatDiff = $now->diffInSeconds($sentAt);
        $this->assertLessThan(0, $signedFloatDiff);
        $this->assertNotSame(60 - $signedFloatDiff, $remaining);
    }
}
