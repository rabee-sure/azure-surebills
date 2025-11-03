<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExpandIbanColumnsForEncryption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Expand users.iban_number column
        Schema::table('users', function (Blueprint $table) {
            $table->text('iban_number')->nullable()->change();
        });

        // Expand settlements.iban_number column
        Schema::table('settlements', function (Blueprint $table) {
            $table->text('iban_number')->nullable()->change();
        });

        // Expand due_amount_auto_transfer_report.merchant_iban column
        if (Schema::hasTable('due_amount_auto_transfer_report')) {
            Schema::table('due_amount_auto_transfer_report', function (Blueprint $table) {
                $table->text('merchant_iban')->nullable()->change();
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
        // Revert to varchar(191)
        Schema::table('users', function (Blueprint $table) {
            $table->string('iban_number', 191)->nullable()->change();
        });

        Schema::table('settlements', function (Blueprint $table) {
            $table->string('iban_number', 191)->nullable()->change();
        });

        if (Schema::hasTable('due_amount_auto_transfer_report')) {
            Schema::table('due_amount_auto_transfer_report', function (Blueprint $table) {
                $table->string('merchant_iban', 191)->nullable()->change();
            });
        }
    }
}
