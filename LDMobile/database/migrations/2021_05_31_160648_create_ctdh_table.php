<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtdhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ctdh', function (Blueprint $table) {
            $table->unsignedInteger('id_dh');
            $table->unsignedInteger('id_sp');
            $table->integer('gia')->unsigned();
            $table->integer('sl')->unsigned();
            $table->float('giamgia')->nullable();
            $table->integer('thanhtien');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ctdh');
    }
}
