<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDoubleApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->decimal('mada_fixed', 10, 5)->nullable()->change();
            $table->decimal('mada_percentage', 10, 5)->nullable()->change();
            $table->decimal('credit_cards_fixed', 10, 5)->nullable()->change();
            $table->decimal('credit_cards_percentage', 10, 5)->nullable()->change();          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {

        });
    }
}
