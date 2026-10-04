<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\BillController as ApiBillController;
use App\Http\Controllers\Api\CouponController as ApiCouponController;
use App\Http\Controllers\BillController;
use App\Http\Requests\BillApiRequest;
use App\Http\Requests\BillRequest;
use App\Models\Application;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Coupon;
use App\Models\CouponCode;
use App\Models\CouponUsage;
use App\Models\Customer;
use App\Models\Settings;
use App\Models\User;
use App\Services\Coupon\CouponService;
use App\Services\Coupon\CouponValidator;
use App\Services\Coupon\RedemptionClaim;
use App\Repositories\CouponRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CouponConcurrencyTest extends TestCase
{
    private string $marker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->marker = 'coupon-concurrency-'.uniqid('', true);

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $exception) {
            $this->markTestSkipped('MySQL is not reachable from the PHPUnit environment.');
        }
    }

    protected function tearDown(): void
    {
        $this->cleanupMarkedData();
        parent::tearDown();
    }

    public function test_validate_coupon_preview_does_not_reserve_usage(): void
    {
        $merchant = $this->makeMerchant();
        $customer = $this->makeCustomer($merchant, $this->mobile());
        $coupon = $this->makeReusableCoupon($merchant, 'max_usage', $this->code('PREVIEW'), 1);

        $result = (new CouponService())->validateCoupon($coupon->code_pattern, $customer->id, $merchant->id);

        $this->assertTrue($result['success']);
        $this->assertSame(0, $this->usageCount($coupon));
    }

    public function test_record_usage_without_the_locked_claim_throws(): void
    {
        $merchant = $this->makeMerchant();
        $coupon = $this->makeReusableCoupon($merchant, 'max_usage', $this->code('CLAIM'), 1);
        $service = new CouponService();

        $this->expectException(\RuntimeException::class);

        DB::transaction(function () use ($service, $coupon, $merchant) {
            $service->recordUsage(new RedemptionClaim($coupon->code_pattern, $merchant->id, $coupon, null));
        });
    }

    public function test_validator_apply_cannot_redeem_without_the_claim(): void
    {
        $merchant = $this->makeMerchant();
        $customer = $this->makeCustomer($merchant, $this->mobile());
        $coupon = $this->makeReusableCoupon($merchant, 'max_usage', $this->code('APPLY'), 1);

        try {
            (new CouponValidator(new CouponRepository()))->apply($coupon, $customer, null);
            $this->fail('Unlocked apply() must not redeem a coupon.');
        } catch (\RuntimeException $exception) {
            $this->assertSame(0, $this->usageCount($coupon));
        }
    }

    public function test_sequential_web_redemption_allows_only_the_configured_uses(): void
    {
        $merchant = $this->makeMerchant();
        $mobile = $this->mobile();
        $this->makeCustomer($merchant, $mobile);
        $coupon = $this->makeReusableCoupon($merchant, 'max_usage', $this->code('SEQ'), 1);

        $first = $this->redeemWeb($merchant, $coupon->code_pattern, $mobile);
        $second = $this->redeemWeb($merchant, $coupon->code_pattern, $mobile);

        $this->assertTrue($first['ok'], $first['error'] ?? '');
        $this->assertFalse($second['ok']);
        $this->assertSame(1, $this->usageCount($coupon));
        $this->assertSame(1, $this->discountedBillCount($merchant, $coupon));
    }

    public function test_web_max_usage_one_allows_exactly_one_concurrent_redemption(): void
    {
        $this->requirePcntl();

        $merchant = $this->makeMerchant();
        $mobile = $this->mobile();
        $this->makeCustomer($merchant, $mobile);
        $coupon = $this->makeReusableCoupon($merchant, 'max_usage', $this->code('WEB1'), 1);

        [$first, $second] = $this->runConcurrently(
            fn () => $this->redeemWeb($merchant, $coupon->code_pattern, $mobile),
            fn () => $this->redeemWeb($merchant, $coupon->code_pattern, $mobile)
        );

        $this->assertEqualsCanonicalizing([true, false], [$first['ok'], $second['ok']]);
        $this->assertSame(1, $this->usageCount($coupon));
        $this->assertSame(1, $this->discountedBillCount($merchant, $coupon));
    }

    public function test_web_near_limit_allows_only_one_of_two_concurrent_redemptions(): void
    {
        $this->requirePcntl();

        $merchant = $this->makeMerchant();
        $mobile = $this->mobile();
        $customer = $this->makeCustomer($merchant, $mobile);
        $coupon = $this->makeReusableCoupon($merchant, 'max_usage', $this->code('WEBN'), 3);
        $this->seedUsages($coupon, $customer, 2);

        [$first, $second] = $this->runConcurrently(
            fn () => $this->redeemWeb($merchant, $coupon->code_pattern, $mobile),
            fn () => $this->redeemWeb($merchant, $coupon->code_pattern, $mobile)
        );

        $this->assertEqualsCanonicalizing([true, false], [$first['ok'], $second['ok']]);
        $this->assertSame(3, $this->usageCount($coupon));
    }

    public function test_same_customer_max_customer_usage_allows_one_concurrent_redemption(): void
    {
        $this->requirePcntl();

        $merchant = $this->makeMerchant();
        $mobile = $this->mobile();
        $this->makeCustomer($merchant, $mobile);
        $coupon = $this->makeReusableCoupon($merchant, 'max_customer_usage', $this->code('SAME'), null, 1);

        [$first, $second] = $this->runConcurrently(
            fn () => $this->redeemWeb($merchant, $coupon->code_pattern, $mobile),
            fn () => $this->redeemWeb($merchant, $coupon->code_pattern, $mobile)
        );

        $this->assertEqualsCanonicalizing([true, false], [$first['ok'], $second['ok']]);
        $this->assertSame(1, $this->usageCount($coupon));
    }

    public function test_different_customers_can_both_redeem_max_customer_usage(): void
    {
        $this->requirePcntl();

        $merchant = $this->makeMerchant();
        $mobileA = $this->mobile();
        $mobileB = $this->mobile();
        $this->makeCustomer($merchant, $mobileA);
        $this->makeCustomer($merchant, $mobileB);
        $coupon = $this->makeReusableCoupon($merchant, 'max_customer_usage', $this->code('DIFF'), null, 1);

        [$first, $second] = $this->runConcurrently(
            fn () => $this->redeemWeb($merchant, $coupon->code_pattern, $mobileA),
            fn () => $this->redeemWeb($merchant, $coupon->code_pattern, $mobileB)
        );

        $this->assertTrue($first['ok'], $first['error'] ?? '');
        $this->assertTrue($second['ok'], $second['error'] ?? '');
        $this->assertSame(2, $this->usageCount($coupon));
    }

    public function test_one_time_code_allows_exactly_one_concurrent_web_redemption(): void
    {
        $this->requirePcntl();

        $merchant = $this->makeMerchant();
        $mobile = $this->mobile();
        $this->makeCustomer($merchant, $mobile);
        $code = $this->code('ONCE');
        $coupon = $this->makeOneTimeCoupon($merchant, $code);

        [$first, $second] = $this->runConcurrently(
            fn () => $this->redeemWeb($merchant, $code, $mobile),
            fn () => $this->redeemWeb($merchant, $code, $mobile)
        );

        $this->assertEqualsCanonicalizing([true, false], [$first['ok'], $second['ok']]);
        $this->assertSame(1, $this->usageCount($coupon));
        $this->assertTrue((bool) CouponCode::where('code', $code)->first()->is_used);
        $this->assertSame(1, $this->discountedBillCount($merchant, $coupon));
    }

    public function test_web_total_failure_rolls_back_bill_usage_and_one_time_code(): void
    {
        $merchant = $this->makeMerchant();
        $mobile = $this->mobile();
        $this->makeCustomer($merchant, $mobile);
        $code = $this->code('ROLL');
        $coupon = $this->makeOneTimeCoupon($merchant, $code, 10);

        $result = $this->redeemWeb($merchant, $code, $mobile, 10);

        $this->assertFalse($result['ok']);
        $this->assertSame(0, $this->usageCount($coupon));
        $this->assertSame(0, $this->discountedBillCount($merchant, $coupon));
        $this->assertFalse((bool) CouponCode::where('code', $code)->first()->is_used);
    }

    public function test_api_max_usage_one_allows_exactly_one_concurrent_redemption_and_keeps_reference_cancel(): void
    {
        $this->requirePcntl();

        $merchant = $this->makeMerchant();
        $application = $this->makeApplication($merchant);
        $mobile = $this->mobile();
        $customer = $this->makeCustomer($merchant, $mobile);
        $coupon = $this->makeReusableCoupon($merchant, 'max_usage', $this->code('API1'), 1);
        $reference = $this->marker.'-ref';
        $pending = $this->makePendingBill($merchant, $customer, $reference);

        [$first, $second] = $this->runConcurrently(
            fn () => $this->redeemApi($merchant, $application, $coupon->code_pattern, $reference, $mobile),
            fn () => $this->redeemApi($merchant, $application, $coupon->code_pattern, $reference, $mobile)
        );

        $this->assertEqualsCanonicalizing(
            [true, false],
            [$first['ok'], $second['ok']],
            substr(($first['error'] ?? '').' | '.($second['error'] ?? ''), 0, 500)
        );
        $this->assertSame(1, $this->usageCount($coupon));
        $this->assertSame(1, $this->discountedBillCount($merchant, $coupon));
        $this->assertSame('canceled', $pending->fresh()->status);
    }

    public function test_validate_and_apply_allows_exactly_one_concurrent_redemption(): void
    {
        $this->requirePcntl();

        $merchant = $this->makeMerchant();
        $customer = $this->makeCustomer($merchant, $this->mobile());
        $coupon = $this->makeReusableCoupon($merchant, 'max_usage', $this->code('APIAPPLY'), 1);

        [$first, $second] = $this->runConcurrently(
            fn () => $this->redeemValidateEndpoint($merchant, $customer, $coupon->code_pattern),
            fn () => $this->redeemValidateEndpoint($merchant, $customer, $coupon->code_pattern)
        );

        $this->assertEqualsCanonicalizing([true, false], [$first['ok'], $second['ok']]);
        $this->assertSame(1, $this->usageCount($coupon));
        $this->assertSame(0, $this->discountedBillCount($merchant, $coupon));
    }

    public function test_bill_create_after_validate_and_apply_cannot_redeem_the_same_one_time_code_again(): void
    {
        $merchant = $this->makeMerchant();
        $mobile = $this->mobile();
        $customer = $this->makeCustomer($merchant, $mobile);
        $code = $this->code('BOTH');
        $coupon = $this->makeOneTimeCoupon($merchant, $code);

        $applied = $this->redeemValidateEndpoint($merchant, $customer, $code);
        $bill = $this->redeemWeb($merchant, $code, $mobile);

        $this->assertTrue($applied['ok'], $applied['error'] ?? '');
        $this->assertFalse($bill['ok']);
        $this->assertSame(1, $this->usageCount($coupon));
        $this->assertSame(0, $this->discountedBillCount($merchant, $coupon));
        $this->assertTrue((bool) CouponCode::where('code', $code)->first()->is_used);
    }

    public function test_second_request_cannot_pass_while_the_coupon_row_lock_is_held(): void
    {
        $merchant = $this->makeMerchant();
        $mobile = $this->mobile();
        $this->makeCustomer($merchant, $mobile);
        $coupon = $this->makeReusableCoupon($merchant, 'max_usage', $this->code('HOLD'), 1);

        $default = config('database.default');
        $probe = $default.'_coupon_probe';
        config(["database.connections.$probe" => config("database.connections.$default")]);
        DB::purge($probe);

        DB::connection($default)->beginTransaction();
        (new CouponService())->beginRedemption($coupon->code_pattern, $merchant->id);

        config(['database.default' => $probe]);
        DB::purge($probe);
        DB::connection($probe)->statement('SET SESSION innodb_lock_wait_timeout = 1');

        try {
            $blocked = $this->redeemWeb($merchant, $coupon->code_pattern, $mobile);
        } finally {
            config(['database.default' => $default]);
            DB::purge($probe);
        }

        $this->assertFalse($blocked['ok'], $blocked['error'] ?? '');
        $this->assertSame(0, $this->usageCount($coupon));
        $this->assertSame(0, $this->discountedBillCount($merchant, $coupon));

        DB::connection($default)->rollBack();

        $allowed = $this->redeemWeb($merchant, $coupon->code_pattern, $mobile);
        $this->assertTrue($allowed['ok'], $allowed['error'] ?? '');
        $this->assertSame(1, $this->usageCount($coupon));
    }

    private function redeemWeb(User $merchant, string $code, string $mobile, float $price = 100): array
    {
        auth()->login($merchant);

        $request = BillRequest::create('/bills', 'POST', [
            'customer_name' => 'Test Customer',
            'customer_mobile' => $mobile,
            'customer_email' => $mobile.'@example.test',
            'due_date' => '2026-10-10',
            'coupon_code' => $code,
            'items' => [
                ['name' => 'Item', 'price' => $price, 'quantity' => 1],
            ],
            'send_sms' => false,
            'send_email' => false,
            'add_tax' => false,
        ]);
        $request->setContainer(app())->setRedirector(app('redirect'));
        $request->setUserResolver(function () use ($merchant) {
            return $merchant;
        });

        try {
            app(BillController::class)->store($request);

            return ['ok' => true];
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'error' => $exception->getMessage(),
                'errors' => $exception instanceof ValidationException ? $exception->errors() : [],
            ];
        }
    }

    private function redeemApi(User $merchant, Application $application, string $code, string $reference, string $mobile): array
    {
        $request = BillApiRequest::create('/api/bills/create', 'POST', [
            'reference_id' => $reference,
            'customer_name' => 'Test Customer',
            'customer_email' => $mobile.'@example.test',
            'customer_mobile' => $mobile,
            'due_date' => '2026-10-10',
            'coupon_code' => $code,
            'items' => [
                ['name' => 'Item', 'price' => 100, 'quantity' => 1],
            ],
            'send_sms' => 0,
            'send_email' => 0,
            'add_tax' => 0,
            'is_redirect' => true,
        ]);
        $request->setContainer(app())->setRedirector(app('redirect'));
        $request->merge([
            'application' => $application,
            'user' => $merchant,
        ]);

        try {
            $response = app(ApiBillController::class)->store($request);
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'error' => $exception->getMessage(),
            ];
        }

        if ($response instanceof JsonResponse) {
            return [
                'ok' => $response->getStatusCode() < 400,
                'status' => $response->getStatusCode(),
                'error' => (string) $response->getContent(),
            ];
        }

        return ['ok' => true];
    }

    private function redeemValidateEndpoint(User $merchant, Customer $customer, string $code): array
    {
        auth()->login($merchant);

        $request = Request::create('/api/coupons/validate', 'POST', [
            'code' => $code,
            'customer_id' => $customer->id,
        ]);
        $request->setUserResolver(function () use ($merchant) {
            return $merchant;
        });

        try {
            $response = app(ApiCouponController::class)->validateCoupon($request);
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'error' => $exception->getMessage(),
            ];
        }

        $payload = $response->getData(true);

        return [
            'ok' => (bool) ($payload['success'] ?? false),
            'error' => $payload['message'] ?? '',
        ];
    }

    private function makeMerchant(): User
    {
        $user = User::withoutEvents(function () {
            return User::create([
                'name' => $this->marker,
                'email' => $this->marker.'-'.uniqid('', true).'@example.test',
                'password' => bcrypt('password'),
                'mobile' => substr(str_replace('.', '', uniqid('5', true)), 0, 14),
                'business_name_en' => 'Coupon Test',
            ]);
        });

        Settings::create([
            'user_id' => $user->id,
            'add_tax' => false,
            'default_lang' => 'en',
            'active_lang' => 'en',
            'create_send_sms' => false,
            'create_send_email' => false,
            'paid_send_sms' => false,
            'paid_send_email' => false,
        ]);

        return $user;
    }

    private function makeApplication(User $merchant): Application
    {
        return Application::withoutEvents(function () use ($merchant) {
            return Application::create([
                'user_id' => $merchant->id,
                'name' => $this->marker,
                'secret' => $this->marker,
            ]);
        });
    }

    private function makeCustomer(User $merchant, string $mobile): Customer
    {
        return Customer::create([
            'name' => 'Test Customer',
            'mobile' => $mobile,
            'email' => $mobile.'@example.test',
            'user_id' => $merchant->id,
        ]);
    }

    private function makeReusableCoupon(User $merchant, string $mechanism, string $code, ?int $maxUsage, ?int $maxCustomerUsage = null): Coupon
    {
        return Coupon::create([
            'user_id' => $merchant->id,
            'name' => $this->marker,
            'mechanism' => $mechanism,
            'discount_type' => 'fixed',
            'discount_value' => 10,
            'max_usage' => $maxUsage,
            'max_customer_usage' => $maxCustomerUsage,
            'code_pattern' => $code,
            'is_active' => true,
        ]);
    }

    private function makeOneTimeCoupon(User $merchant, string $code, float $discount = 10): Coupon
    {
        $coupon = Coupon::create([
            'user_id' => $merchant->id,
            'name' => $this->marker,
            'mechanism' => 'one_time_usage',
            'discount_type' => 'fixed',
            'discount_value' => $discount,
            'code_pattern' => '{TEST}',
            'is_active' => true,
        ]);

        CouponCode::create([
            'coupon_id' => $coupon->id,
            'code' => $code,
            'is_used' => false,
        ]);

        return $coupon;
    }

    private function makePendingBill(User $merchant, Customer $customer, string $reference): Bill
    {
        return Bill::withoutEvents(function () use ($merchant, $customer, $reference) {
            $bill = new Bill();
            $bill->id = (string) \Ramsey\Uuid\Uuid::uuid4();
            $bill->user_id = $merchant->id;
            $bill->status = 'pending';
            $bill->customer_id = $customer->id;
            $bill->reference_id = $reference;
            $bill->business_name = 'Coupon Test';
            $bill->customer_name = 'Test Customer';
            $bill->customer_mobile = $customer->mobile;
            $bill->source = 'api';
            $bill->due_date = '2026-10-10';
            $bill->number = '0';
            $bill->save();

            return $bill;
        });
    }

    private function seedUsages(Coupon $coupon, Customer $customer, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            CouponUsage::create([
                'coupon_id' => $coupon->id,
                'customer_id' => $customer->id,
                'used_at' => now(),
            ]);
        }
    }

    private function usageCount(Coupon $coupon): int
    {
        return CouponUsage::where('coupon_id', $coupon->id)->count();
    }

    private function discountedBillCount(User $merchant, Coupon $coupon): int
    {
        return Bill::where('user_id', $merchant->id)->where('coupon_id', $coupon->id)->count();
    }

    private function mobile(): string
    {
        return '5'.str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
    }

    private function code(string $suffix): string
    {
        return strtoupper(substr(str_replace('.', '', $this->marker), -12)).$suffix;
    }

    private function requirePcntl(): void
    {
        if (!function_exists('pcntl_fork')) {
            $this->markTestSkipped('pcntl is required to exercise concurrent redemption.');
        }
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function runConcurrently(callable $left, callable $right): array
    {
        $leftFile = tempnam(sys_get_temp_dir(), 'coupon-l-');

        DB::disconnect();
        $pid = pcntl_fork();

        if ($pid === -1) {
            $this->fail('Failed to fork concurrent process');
        }

        if ($pid === 0) {
            $this->reconnectAfterFork();
            file_put_contents($leftFile, serialize($left()));
            exit(0);
        }

        $this->reconnectAfterFork();
        $rightResult = $right();
        pcntl_waitpid($pid, $status);
        $leftResult = unserialize((string) file_get_contents($leftFile));
        @unlink($leftFile);

        return [$leftResult, $rightResult];
    }

    private function reconnectAfterFork(): void
    {
        DB::purge();
        DB::reconnect();
    }

    private function cleanupMarkedData(): void
    {
        if (!isset($this->marker)) {
            return;
        }

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $exception) {
            return;
        }

        $userIds = User::withTrashed()->where('name', $this->marker)->pluck('id');
        if ($userIds->isEmpty()) {
            return;
        }

        $couponIds = Coupon::withTrashed()->whereIn('user_id', $userIds)->pluck('id');
        if ($couponIds->isNotEmpty()) {
            CouponUsage::whereIn('coupon_id', $couponIds)->delete();
            CouponCode::whereIn('coupon_id', $couponIds)->delete();
        }

        $billIds = Bill::whereIn('user_id', $userIds)->pluck('id');
        if ($billIds->isNotEmpty()) {
            BillItem::whereIn('bill_id', $billIds)->delete();
            Bill::whereIn('id', $billIds)->delete();
        }

        if ($couponIds->isNotEmpty()) {
            Coupon::withTrashed()->whereIn('id', $couponIds)->forceDelete();
        }

        Customer::whereIn('user_id', $userIds)->delete();
        Application::whereIn('user_id', $userIds)->delete();
        Settings::whereIn('user_id', $userIds)->delete();
        User::withTrashed()->whereIn('id', $userIds)->forceDelete();
    }
}
