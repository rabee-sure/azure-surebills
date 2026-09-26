<?php

namespace App\Console\Commands;

use App\Models\Bank;
use App\Models\DueAmountAutoTransferReport;
use Illuminate\Console\Command;
use Throwable;

class BackfillDueAmountBankTranslations extends Command
{
    protected $signature = 'reports:backfill-due-amount-bank-translations {--commit : Write matched bank snapshots}';

    protected $description = 'Replace a due amount report bank string with that bank\'s translation snapshot when exactly one bank matches';

    public function handle(): int
    {
        $commit = (bool) $this->option('commit');
        $banksByName = $this->banksByExactName();

        $total = 0;
        $matched = 0;
        $unmatched = 0;
        $ambiguous = 0;
        $alreadyMigrated = 0;
        $failed = 0;
        $unmatchedValues = [];
        $ambiguousValues = [];

        DueAmountAutoTransferReport::query()->orderBy('id')->chunkById(100, function ($reports) use (
            $commit,
            $banksByName,
            &$total,
            &$matched,
            &$unmatched,
            &$ambiguous,
            &$alreadyMigrated,
            &$failed,
            &$unmatchedValues,
            &$ambiguousValues
        ) {
            foreach ($reports as $report) {
                $total++;
                $raw = $report->getRawOriginal('bank');

                if (DueAmountAutoTransferReport::bankValueIsTranslationSnapshot($raw)) {
                    $alreadyMigrated++;
                    continue;
                }

                $storedName = is_string($raw) ? $raw : '';
                $matches = $banksByName[$storedName] ?? [];

                if (count($matches) === 1) {
                    $matched++;
                    if ($commit) {
                        try {
                            $bank = array_values($matches)[0];
                            $report->bank = $bank->getTranslations('name');
                            $report->save();
                        } catch (Throwable $exception) {
                            $failed++;
                            $matched--;
                            $this->error('Failed report '.$report->id.': '.$exception->getMessage());
                        }
                    }
                    continue;
                }

                if (count($matches) > 1) {
                    $ambiguous++;
                    $ambiguousValues[$storedName] = true;
                    continue;
                }

                $unmatched++;
                $unmatchedValues[$storedName] = true;
            }
        });

        if ($commit) {
            $this->info('Committed matched bank snapshots.');
        } else {
            $this->info('Dry run. No rows were written.');
        }

        $this->line('Total reports: '.$total);
        $this->line('Successfully matched: '.$matched);
        $this->line('Unmatched: '.$unmatched);
        $this->line('Ambiguous: '.$ambiguous);
        $this->line('Already migrated: '.$alreadyMigrated);
        $this->line('Failed: '.$failed);

        if ($unmatchedValues !== []) {
            $this->warn('Unmatched values: '.implode(', ', array_keys($unmatchedValues)));
        }

        if ($ambiguousValues !== []) {
            $this->warn('Ambiguous values: '.implode(', ', array_keys($ambiguousValues)));
        }

        return $failed > 0 ? 1 : 0;
    }

    /**
     * @return array<string, array<int, Bank>>
     */
    private function banksByExactName(): array
    {
        $banksByName = [];

        foreach (Bank::query()->get() as $bank) {
            foreach ($bank->getTranslations('name') as $translation) {
                if (! is_string($translation) || $translation === '') {
                    continue;
                }

                $banksByName[$translation][$bank->id] = $bank;
            }
        }

        return $banksByName;
    }
}
