<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('bill_tag', function (Blueprint $table) {
            $table->id();
            $table->uuid('bill_id');
            $table->foreign('bill_id')->on('bills')->references('id');
            $table->unsignedBigInteger('tag_id');
            $table->foreign('tag_id')->on('tags')->references('id');    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_tag');
        Schema::dropIfExists('tags');
    }
}
