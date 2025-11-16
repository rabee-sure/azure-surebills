<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SanitizeSandboxData extends Command
{
    protected $signature = 'sandbox:sanitize {--dry-run} {--table=}';
    protected $description = 'Mask sensitive data in sandbox database based on config/sandbox_sensitive.php';

    public function handle()
    {
        $faker = Faker::create();
        $config = config('sandbox_sensitive');
        $dryRun = $this->option('dry-run');
        $specificTable = $this->option('table');

        $this->info('🚀 Starting sandbox data sanitization...');
        $this->newLine();

        foreach ($config as $table => $columns) {
            if ($specificTable && $specificTable !== $table) {
                continue;
            }

            if (!DB::getSchemaBuilder()->hasTable($table)) {
                $this->warn("⚠️  Table {$table} does not exist, skipped.");
                continue;
            }

            $this->info("Processing table: {$table}");

            $totalRows = DB::table($table)->count();
            if ($totalRows === 0) {
                $this->warn("  ⚠️  Table {$table} is empty, skipped.");
                $this->newLine();
                continue;
            }

            $bar = $this->output->createProgressBar($totalRows);
            $bar->start();

            $maskedTotal = 0;
            $skippedTotal = 0;

            DB::table($table)->orderBy('id')->chunk(100, function ($rows) use (
                $faker, $columns, $table, $dryRun, &$maskedTotal, &$skippedTotal, $bar
            ) {
                foreach ($rows as $record) {
                    $updates = [];

                    foreach ($columns as $col) {
                        $column = $col['column'];
                        $example = $col['example'] ?? null;

                        // Skip if column not exists
                        if (!DB::getSchemaBuilder()->hasColumn($table, $column)) {
                            continue;
                        }

                        $original = $record->{$column};

                        // keep empty/null as is
                        if (empty($original)) {
                            $skippedTotal++;
                            continue;
                        }

                        $fakeValue = $this->generateFakeValue($faker, $column, $example);
                        $updates[$column] = $fakeValue;
                        $maskedTotal++;
                    }

                    if (!$dryRun && !empty($updates)) {
                        DB::table($table)->where('id', $record->id)->update($updates);
                    }

                    $bar->advance();
                }
            });

            $bar->finish();
            $this->newLine();
            $this->info("  ✅ Total masked: {$maskedTotal} | skipped empty: {$skippedTotal}");
            $this->newLine();
        }

        $this->info('🎯 Sandbox data sanitization completed successfully.');
    }

    private function generateFakeValue($faker, string $column, ?string $example)
    {
        $col = strtolower($column);

        // === Saudi Format Rules ===
        if (str_contains($col, 'mobile')) {
            return '5' . $faker->numerify(str_repeat('#', 8));
        }

        if (str_contains($col, 'crn')) {
            return '1' . $faker->numerify(str_repeat('#', 9));
        }

        if (str_contains($col, 'postal_code')) {
            return $faker->numerify('#####');
        }

        if (str_contains($col, 'tin')) {
            return '1' . $faker->numerify(str_repeat('#', 9));
        }

        if (str_contains($col, 'national_id')) {
            $start = $faker->randomElement(['1', '7']);
            return $start . $faker->numerify(str_repeat('#', 9));
        }

        if (str_contains($col, 'iban')) {
            return 'SA' . $faker->numerify(str_repeat('#', 22));
        }

        if (str_contains($col, 'card_number') || $col === 'card') {
            return 'xxxxxxxxxxxx1111';
        }

        if (str_contains($col, 'vat_registration_number')) {
            return '3' . $faker->numerify(str_repeat('#', 13)) . '3';
        }

        if (str_contains($col, 'additional_no')) {
            return $faker->numerify($faker->randomElement(['#####', '######']));
        }

        if (str_contains($col, 'email')) {
            return $faker->unique()->safeEmail();
        }

        if (str_contains($col, 'name')) {
            return $faker->name();
        }

        if (str_contains($col, 'beneficiary_name')) {
            return $faker->company();
        }

        if (str_contains($col, 'bank')) {
            return $faker->randomElement(['ANB - TEST', 'Riyad Bank -TEST', 'Alinma -TEST', 'Al Rajhi - TEST']);
        }

        return $faker->word();
    }
}
