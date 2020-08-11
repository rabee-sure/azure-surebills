<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExpiryMinutesAndExpiryHoursToBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function($table)
        {
            $table->integer('expiry_minutes')->default(0);  
            $table->integer('expiry_hours')->default(0);  
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
            $table->dropColumn('expiry_hours');
            $table->dropColumn('expiry_minutes');
        });
    }
}
