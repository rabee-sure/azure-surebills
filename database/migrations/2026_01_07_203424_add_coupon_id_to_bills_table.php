<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCouponIdToBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->nullable()->after('customer_id');
            $table->foreign('coupon_id')->on('coupons')->references('id')->onDelete('set null');
            $table->index('coupon_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropIndex(['coupon_id']);
            $table->dropColumn('coupon_id');
        });
    }
}
