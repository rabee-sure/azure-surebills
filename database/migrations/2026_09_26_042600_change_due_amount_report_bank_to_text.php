<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeDueAmountReportBankToText extends Migration
{
    /**
     * The report bank column stores a Spatie translation snapshot.
     * banks.name is already text. varchar(191) can truncate both locales.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE due_amount_auto_transfer_report MODIFY bank TEXT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE due_amount_auto_transfer_report MODIFY bank VARCHAR(191) NULL');
    }
}
