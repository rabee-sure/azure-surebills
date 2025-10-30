<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExpandZatcaColumnsForEncryption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Expand zatca_merchants encrypted columns
        if (Schema::hasTable('zatca_merchants')) {
            Schema::table('zatca_merchants', function (Blueprint $table) {
                $table->text('crn')->nullable()->change();
            });
        }

        // Expand zatca_invoices encrypted columns
        if (Schema::hasTable('zatca_invoices')) {
            Schema::table('zatca_invoices', function (Blueprint $table) {
                $table->text('merchant_crn')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to varchar
        if (Schema::hasTable('zatca_merchants')) {
            Schema::table('zatca_merchants', function (Blueprint $table) {
                $table->string('crn')->change();
            });
        }

        if (Schema::hasTable('zatca_invoices')) {
            Schema::table('zatca_invoices', function (Blueprint $table) {
                $table->string('merchant_crn')->change();
            });
        }
    }
}
