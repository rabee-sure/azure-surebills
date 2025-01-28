<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMorphBillToZatcaLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zatca_logs', function (Blueprint $table) {
            $table->nullableMorphs('parentable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zatca_logs', function (Blueprint $table) {
            $table->dropMorphs('parentable');
        });
    }
}
