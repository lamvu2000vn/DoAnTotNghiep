<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMauspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mausp', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tenmau', 100);
            $table->string('id_youtube', 50)->nullable();
            $table->unsignedInteger('id_ncc');
            $table->string('baohanh', 50)->nullable();
            $table->string('diachibaohanh', 200)->nullable();
            $table->boolean('trangthai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mausp');
    }
}
