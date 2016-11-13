<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function($table)
        {
            /** @var $table Blueprint */
            $table->increments('id');
            $table->timestamp('start');
            $table->timestamp('end');
            $table->string('room');
            $table->string('title');
            $table->text('abstract');
            $table->text('speakers');
            $table->text('tracks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }

}
