<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBusinessToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sector')->nullable();
            $table->string('website')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();

            $table->string('license_type')->nullable();
            $table->string('bank')->nullable();
            $table->string('iban_number')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('beneficiary_name')->nullable();
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
            $table->dropColumn('sector');
            $table->dropColumn('website');
            $table->dropColumn('twitter');
            $table->dropColumn('facebook');
            $table->dropColumn('instagram');
            $table->dropColumn('logo');
            $table->dropColumn('description');
            $table->dropColumn('license_type');
            $table->dropColumn('bank');
            $table->dropColumn('iban_number');
            $table->dropColumn('organization_name');
            $table->dropColumn('beneficiary_name');
        });
    }
}
