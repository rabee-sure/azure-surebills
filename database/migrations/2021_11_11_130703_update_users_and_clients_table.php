<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersAndClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bullding_no')->nullable();
            $table->string('street_name')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('additional_no')->nullable();
            $table->string('other_buyer_id')->nullable();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('bullding_no')->nullable();
            $table->string('street_name')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('additional_no')->nullable();
            $table->string('other_buyer_id')->nullable();
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
            $table->dropColumn('bullding_no');
            $table->dropColumn('street_name');
            $table->dropColumn('district');
            $table->dropColumn('city');
            $table->dropColumn('postal_code');
            $table->dropColumn('additional_no');
            $table->dropColumn('other_buyer_id');
        });        
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('bullding_no');
            $table->dropColumn('street_name');
            $table->dropColumn('district');
            $table->dropColumn('city');
            $table->dropColumn('postal_code');
            $table->dropColumn('additional_no');
            $table->dropColumn('other_buyer_id');
        });
    }
}
