<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankDataToSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settlements', function (Blueprint $table) {
            $table->json('filters')->nullable();
            $table->string('bank_id')->nullable();
            $table->string('iban_number')->nullable();
            $table->string('beneficiary_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settlements', function (Blueprint $table) {
            $table->dropColumn('bank_id');
            $table->dropColumn('iban_number');
            $table->dropColumn('beneficiary_name');
        });
    }
}
