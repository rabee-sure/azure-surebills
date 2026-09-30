<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Models\TransferLog;
use App\Models\UserBalance;
use App\Services\TransferOperations;
use App\Services\TransferService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TransferConcurrencyTest extends TestCase
{
    private string $marker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->marker = 'xfer-concurrency-'.uniqid('', true);
        Queue::fake();

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped('MySQL is not reachable from the PHPUnit environment (typically .env.testing DB_HOST=database).');
        }
    }

    protected function tearDown(): void
    {
        $this->cleanupMarkedData();
        parent::tearDown();
    }

    public function test_sequential_duplicate_create_is_rejected(): void
    {
        $merchant = $this->makeMerchant();
        $this->makeUnsettledCredit($merchant, 600);

        TransferService::makeTransfer('completed', 600, $this->transferData($merchant));

        try {
            TransferService::makeTransfer('completed', 600, $this->transferData($merchant));
            $this->fail('Second create should have been rejected');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('amount', $e->errors());
        }

        $this->assertSame(1, $this->settlementCount($merchant));
        $this->assertSame(1, $this->transferDebitCount($merchant));
    }

    public function test_stale_amount_is_rejected_after_first_settlement(): void
    {
        $merchant = $this->makeMerchant();
        $this->makeUnsettledCredit($merchant, 600);

        TransferService::makeTransfer('completed', 600, $this->transferData($merchant));

        try {
            TransferService::makeTransfer('completed', 9999, $this->transferData($merchant));
            $this->fail('Stale amount create should have been rejected');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('amount', $e->errors());
        }

        $this->assertSame(1, $this->settlementCount($merchant));
        $this->assertSame(1, $this->transferDebitCount($merchant));
    }

    public function test_concurrent_create_allows_only_one_settlement(): void
    {
        $this->requirePcntl();

        $merchant = $this->makeMerchant();
        $this->makeUnsettledCredit($merchant, 600);
        $data = $this->transferData($merchant);

        [$first, $second] = $this->runConcurrently(
            fn () => $this->makeTransferOutcome('completed', $data),
            fn () => $this->makeTransferOutcome('completed', $data)
        );

        $this->assertEqualsCanonicalizing([true, false], [$first['ok'], $second['ok']]);
        $this->assertSame(1, $this->settlementCount($merchant));
        $this->assertSame(1, $this->transferDebitCount($merchant));
    }

    public function test_concurrent_confirm_produces_one_debit(): void
    {
        $this->requirePcntl();

        $merchant = $this->makeMerchant();
        $this->makeUnsettledCredit($merchant, 600);
        $transfer = TransferService::makeTransfer('pending', 600, $this->transferData($merchant));

        $this->runConcurrently(
            fn () => $this->confirmOutcome($transfer->id),
            fn () => $this->confirmOutcome($transfer->id)
        );

        $this->assertSame('completed', $transfer->fresh()->status);
        $this->assertSame(1, $this->transferDebitCount($merchant));
    }

    public function test_different_merchants_can_create_in_parallel(): void
    {
        $this->requirePcntl();

        $merchantA = $this->makeMerchant();
        $merchantB = $this->makeMerchant();
        $this->makeUnsettledCredit($merchantA, 600);
        $this->makeUnsettledCredit($merchantB, 600);

        [$first, $second] = $this->runConcurrently(
            fn () => $this->makeTransferOutcome('completed', $this->transferData($merchantA)),
            fn () => $this->makeTransferOutcome('completed', $this->transferData($merchantB))
        );

        $this->assertTrue($first['ok']);
        $this->assertTrue($second['ok']);
        $this->assertSame(1, $this->settlementCount($merchantA));
        $this->assertSame(1, $this->settlementCount($merchantB));
    }

    public function test_amount_below_minimum_after_lock_creates_nothing(): void
    {
        $merchant = $this->makeMerchant();
        $this->makeUnsettledCredit($merchant, 10);

        try {
            TransferService::makeTransfer('completed', 10, $this->transferData($merchant));
            $this->fail('Below-minimum create should have been rejected');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('amount', $e->errors());
        }

        $this->assertSame(0, $this->settlementCount($merchant));
        $this->assertSame(0, $this->transferDebitCount($merchant));
    }

    public function test_cancel_then_retry_is_allowed(): void
    {
        $merchant = $this->makeMerchant();
        $this->makeUnsettledCredit($merchant, 600);

        $first = TransferService::makeTransfer('pending', 600, $this->transferData($merchant));
        (new TransferOperations())->cancel([$first], 'canceled', $merchant->id);

        $second = TransferService::makeTransfer('completed', 600, $this->transferData($merchant));

        $this->assertSame('canceled', $first->fresh()->status);
        $this->assertSame('completed', $second->fresh()->status);
        $this->assertSame(2, $this->settlementCount($merchant));
        $this->assertSame(1, $this->transferDebitCount($merchant));
    }

    public function test_merchant_race_allows_only_one_claim(): void
    {
        $this->requirePcntl();

        $merchant = $this->makeMerchant();
        $this->makeUnsettledCredit($merchant, 600);
        $data = $this->transferData($merchant);

        [$first, $second] = $this->runConcurrently(
            fn () => $this->makeTransferOutcome('pending', $data),
            fn () => $this->makeTransferOutcome('completed', $data)
        );

        $this->assertEqualsCanonicalizing([true, false], [$first['ok'], $second['ok']]);
        $this->assertSame(1, $this->settlementCount($merchant));
    }

    private function transferData(User $merchant): array
    {
        return [
            'cycle_date' => Carbon::now(),
            'transfer_fees' => 0,
            'note' => $this->marker,
            'created_by_id' => $merchant->id,
            'bank_id' => null,
            'user_id' => $merchant->id,
            'iban_number' => $merchant->iban_number,
            'beneficiary_name' => $merchant->name,
        ];
    }

    private function makeTransferOutcome(string $status, array $data): array
    {
        Queue::fake();

        try {
            $transfer = TransferService::makeTransfer($status, 600, $data);

            return ['ok' => true, 'id' => $transfer->id];
        } catch (ValidationException $e) {
            return ['ok' => false, 'errors' => $e->errors()];
        }
    }

    private function confirmOutcome(int $transferId): array
    {
        Queue::fake();
        $transfer = Transfer::findOrFail($transferId);
        TransferService::changeTranfersStatus([$transfer], 'completed');

        return ['ok' => true, 'status' => $transfer->fresh()->status];
    }

    private function makeMerchant(): User
    {
        $user = User::withoutEvents(function () {
            return User::create([
                'name' => $this->marker,
                'email' => $this->marker.'-'.uniqid().'@example.test',
                'password' => bcrypt('password'),
                'mobile' => substr(str_replace('.', '', uniqid('5', true)), 0, 14),
            ]);
        });

        UserBalance::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        return $user;
    }

    private function makeUnsettledCredit(User $merchant, float $amount): Transaction
    {
        $transaction = new Transaction();
        $transaction->user_id = $merchant->id;
        $transaction->type = 'credit';
        $transaction->amount = $amount;
        $transaction->description = $this->marker;
        $transaction->reference = $this->marker;
        $transaction->transaction_source = 'bill';
        $transaction->pending_settled = false;
        $transaction->settled = false;
        $transaction->save();

        return $transaction;
    }

    private function settlementCount(User $merchant): int
    {
        return Transfer::where('user_id', $merchant->id)->where('note', $this->marker)->count();
    }

    private function transferDebitCount(User $merchant): int
    {
        return Transaction::where('user_id', $merchant->id)
            ->where('transaction_source', 'transfer')
            ->count();
    }

    private function requirePcntl(): void
    {
        if (! function_exists('pcntl_fork')) {
            $this->markTestSkipped('pcntl is required to exercise concurrent locking');
        }
    }

    /**
     * @return array{0: mixed, 1: mixed}
     */
    private function runConcurrently(callable $left, callable $right): array
    {
        $leftFile = tempnam(sys_get_temp_dir(), 'xfer-l-');

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
        $users = User::withTrashed()->where('name', $this->marker)->get();

        foreach ($users as $user) {
            $transferIds = Transfer::where('user_id', $user->id)->pluck('id');
            if ($transferIds->isNotEmpty()) {
                DB::table('transaction_transfer')->whereIn('transfer_id', $transferIds)->delete();
                TransferLog::whereIn('transfer_id', $transferIds)->delete();
                Transfer::whereIn('id', $transferIds)->delete();
            }
            Transaction::where('user_id', $user->id)->delete();
            UserBalance::where('user_id', $user->id)->delete();
            $user->forceDelete();
        }
    }
}
