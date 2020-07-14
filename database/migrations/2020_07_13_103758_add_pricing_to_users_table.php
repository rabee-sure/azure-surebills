<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPricingToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->double('credit_cards_percentage', 10, 2)->default(2.9);
            $table->double('mada_percentage', 10, 2)->default(1.9);
            $table->string('credit_cards_pay_fees')->default('customer');
            $table->string('mada_pay_fees')->default('customer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('credit_cards_percentage');
            $table->dropColumn('mada_percentage');
            $table->dropColumn('credit_cards_pay_fees');
            $table->dropColumn('mada_pay_fees');
        });
    }
}
