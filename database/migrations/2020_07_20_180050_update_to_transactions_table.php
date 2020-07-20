<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('bill_no');
            $table->dropColumn('creation_date');
            $table->dropColumn('payment_date');

            $table->unsignedBigInteger('receipt')->change();
            $table->string('auth_id')->nullable()->change();
            $table->string('card')->nullable()->change();
            $table->string('card_brand')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('balance', 15, 8)->change();
            $table->decimal('amount', 15, 8);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('bill_no');
            $table->datetime('creation_date');
            $table->datetime('payment_date');

            $table->dropColumn('card_brand');
            $table->dropForeign('transactions_user_id_foreign');
            $table->dropColumn('user_id');
            $table->dropColumn('amount');
        });
    }
}
