<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePricingFixedToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('credit_cards_fixed');
            $table->dropColumn('mada_fixed');
            $table->dropColumn('credit_cards_percentage');
            $table->dropColumn('mada_percentage');
            $table->dropColumn('credit_cards_pay_fees');
            $table->dropColumn('mada_pay_fees');

            $table->string('pay_fees')->default('merchant');
            $table->double('price_fixed', 10, 2)->default(1);
            $table->double('price_percentage', 10, 2)->default(2.9);
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
            $table->dropColumn('pay_fees');
            $table->dropColumn('price_fixed');
            $table->dropColumn('price_percentage');
        });
    }
}
