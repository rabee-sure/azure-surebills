<?php

namespace Tests\Feature;

use App\Console\Commands\TransfersSummary;
use App\Exports\DueAmountsExport;
use App\Models\AutoTransfer;
use App\Models\Bank;
use App\Models\DueAmountAutoTransferReport;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DueAmountAutoTransferReportBankTest extends TestCase
{
    use DatabaseTransactions;

    private string $prefix;

    private array $reportBankSnapshots = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix = 'zz-due-bank-'.uniqid('', true);
        $this->reportBankSnapshots = DB::table('due_amount_auto_transfer_report')
            ->get(['id', 'bank', 'updated_at'])
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    protected function tearDown(): void
    {
        foreach ($this->reportBankSnapshots as $row) {
            DB::table('due_amount_auto_transfer_report')->where('id', $row['id'])->update([
                'bank' => $row['bank'],
                'updated_at' => $row['updated_at'],
            ]);
        }

        DB::table('due_amount_auto_transfer_report')->where('merchant_id', 'like', $this->prefix.'%')->delete();
        Bank::query()->where('code', 'like', $this->prefix.'%')->delete();
        AutoTransfer::query()->where('day', 'like', $this->prefix.'%')->delete();

        parent::tearDown();
    }

    public function test_new_report_stores_both_bank_translations_and_excel_keeps_one_name(): void
    {
        app()->setLocale('en');

        $bank = new Bank();
        $bank->setTranslations('name', [
            'en' => 'Saudi National Bank',
            'ar' => 'البنك الأهلي السعودي',
        ]);
        $user = new User();
        $user->id = 1;
        $user->business_name_ar = 'Shop';
        $user->iban_number = 'SA0380000000608010167519';
        $user->setRelation('bank', $bank);
        $transfer = new Transfer();
        $transfer->id = 1;
        $transfer->transfer_fees = 3;
        $transfer->net_amount = 4;
        $transfer->setRelation('user', $user);
        $transfer->setRelation('transactions', collect());

        $item = (new TransfersSummary())->getDueAmountsItem($transfer, $user, collect());

        $this->assertSame('Saudi National Bank', $item['bank']);
        $this->assertSame([
            'en' => 'Saudi National Bank',
            'ar' => 'البنك الأهلي السعودي',
        ], $item['bank_translations']);

        $item['merchant_id'] = $this->prefix.'-created';
        $export = new DueAmountsExport([$item]);
        $html = $export->view()->render();

        $row = DueAmountAutoTransferReport::query()->where('merchant_id', $this->prefix.'-created')->first();
        $stored = json_decode($row->getRawOriginal('bank'), true);

        $this->assertSame('Saudi National Bank', $stored['en']);
        $this->assertSame('البنك الأهلي السعودي', $stored['ar']);
        $this->assertStringContainsString('Saudi National Bank', $html);
        $this->assertStringNotContainsString('البنك الأهلي السعودي', $html);
    }

    public function test_report_keeps_the_bank_names_from_creation_after_the_bank_is_renamed(): void
    {
        $bank = $this->makeBank('snapshot', [
            'en' => 'Old Bank Name',
            'ar' => 'اسم البنك القديم',
        ]);

        $report = new DueAmountAutoTransferReport();
        $report->auto_transfer_id = $this->autoTransferId();
        $report->merchant_id = $this->prefix.'-snapshot';
        $report->bank = $bank->getTranslations('name');
        $report->save();

        $bank->setTranslations('name', [
            'en' => 'New Bank Name',
            'ar' => 'اسم البنك الجديد',
        ]);
        $bank->save();

        $report->refresh();
        app()->setLocale('en');
        $this->assertSame('Old Bank Name', $report->bank);
        app()->setLocale('ar');
        $report->refresh();
        $this->assertSame('اسم البنك القديم', $report->bank);
        $this->assertSame('New Bank Name', $bank->fresh()->getTranslation('name', 'en'));
    }

    public function test_backfill_converts_only_exact_unique_matches_and_can_be_rerun(): void
    {
        $englishName = $this->prefix.' EN';
        $arabicName = $this->prefix.' AR';
        $ambiguousName = $this->prefix.' AMBIG';
        $missingName = $this->prefix.' NONE';

        $this->makeBank('en', ['en' => $englishName, 'ar' => $this->prefix.' EN-AR']);
        $this->makeBank('ar', ['en' => $this->prefix.' AR-EN', 'ar' => $arabicName]);
        $this->makeBank('ambig-a', ['en' => $ambiguousName, 'ar' => $this->prefix.' AMBIG-A']);
        $this->makeBank('ambig-b', ['en' => $ambiguousName, 'ar' => $this->prefix.' AMBIG-B']);

        $this->insertLegacyReport('english', $englishName);
        $this->insertLegacyReport('arabic', $arabicName);
        $this->insertLegacyReport('missing', $missingName);
        $this->insertLegacyReport('ambiguous', $ambiguousName);
        $already = json_encode(['en' => 'Kept EN', 'ar' => 'Kept AR'], JSON_UNESCAPED_UNICODE);
        $this->insertLegacyReport('already', $already);

        $banksBefore = DB::table('banks')->orderBy('id')->pluck('name', 'id')->all();

        $this->artisan('reports:backfill-due-amount-bank-translations')
            ->expectsOutput('Dry run. No rows were written.')
            ->expectsOutputToContain('Successfully matched:')
            ->assertSuccessful();
        $this->assertSame($englishName, $this->rawBank('english'));
        $this->assertSame($arabicName, $this->rawBank('arabic'));

        $this->artisan('reports:backfill-due-amount-bank-translations', ['--commit' => true])
            ->expectsOutput('Committed matched bank snapshots.')
            ->assertSuccessful();

        $englishStored = json_decode($this->rawBank('english'), true);
        $arabicStored = json_decode($this->rawBank('arabic'), true);
        $this->assertSame($englishName, $englishStored['en']);
        $this->assertSame($this->prefix.' EN-AR', $englishStored['ar']);
        $this->assertSame($arabicName, $arabicStored['ar']);
        $this->assertSame($this->prefix.' AR-EN', $arabicStored['en']);
        $this->assertSame($missingName, $this->rawBank('missing'));
        $this->assertSame($ambiguousName, $this->rawBank('ambiguous'));
        $this->assertSame($already, $this->rawBank('already'));

        $converted = $this->rawBank('english');
        $this->artisan('reports:backfill-due-amount-bank-translations', ['--commit' => true])
            ->expectsOutputToContain('Already migrated:')
            ->assertSuccessful();
        $this->assertSame($converted, $this->rawBank('english'));
        $this->assertSame($already, $this->rawBank('already'));

        $this->assertEquals($banksBefore, DB::table('banks')->orderBy('id')->pluck('name', 'id')->all());
    }

    private function autoTransferId(): int
    {
        $existing = AutoTransfer::query()->where('day', $this->prefix)->value('id');
        if ($existing) {
            return (int) $existing;
        }

        $autoTransfer = new AutoTransfer();
        $autoTransfer->day = $this->prefix;
        $autoTransfer->save();

        return (int) $autoTransfer->id;
    }

    private function makeBank(string $suffix, array $names): Bank
    {
        $bank = new Bank();
        $bank->code = $this->prefix.'-'.$suffix;
        $bank->active = 1;
        $bank->sort_number = 0;
        $bank->fees = 0;
        $bank->setTranslations('name', $names);
        $bank->save();

        return $bank;
    }

    private function insertLegacyReport(string $suffix, string $bank): void
    {
        DB::table('due_amount_auto_transfer_report')->insert([
            'auto_transfer_id' => $this->autoTransferId(),
            'merchant_id' => $this->prefix.'-'.$suffix,
            'bank' => $bank,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function rawBank(string $suffix): ?string
    {
        return DB::table('due_amount_auto_transfer_report')
            ->where('merchant_id', $this->prefix.'-'.$suffix)
            ->value('bank');
    }
}
