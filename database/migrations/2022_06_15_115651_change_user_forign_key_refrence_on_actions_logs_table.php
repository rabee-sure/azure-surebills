<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUserForignKeyRefrenceOnActionsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('actions_logs', function (Blueprint $table) {
            //$table->dropForeign('actions_logs_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('admins');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('actions_logs', function (Blueprint $table) {
            $table->dropForeign('actions_logs_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::enableForeignKeyConstraints();
    }
}
