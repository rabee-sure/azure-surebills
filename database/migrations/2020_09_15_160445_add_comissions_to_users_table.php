<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComissionsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pay_fees');
            $table->dropColumn('price_fixed');
            $table->dropColumn('price_percentage');

            $table->double('mada_fixed', 10, 2)->default(1);
            $table->double('mada_percentage', 10, 2)->default(1.7);
            $table->double('credit_cards_fixed', 10, 2)->default(1);
            $table->double('credit_cards_percentage', 10, 2)->default(2.7);
            $table->double('apple_pay_fixed', 10, 2)->default(1);
            $table->double('apple_pay_percentage', 10, 2)->default(2.7);
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
            $table->dropColumn('mada_cards_fixed');
            $table->dropColumn('mada_cards_percentage');
            $table->dropColumn('credit_cards_fixed');
            $table->dropColumn('credit_cards_percentage');
            $table->dropColumn('apple_pay_fixed');
            $table->dropColumn('apple_pay_percentage');
        });
    }
}
