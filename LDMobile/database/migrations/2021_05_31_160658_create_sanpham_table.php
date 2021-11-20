<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSanphamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sanpham', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tensp', 100);
            $table->unsignedInteger('id_msp');
            $table->string('hinhanh', 100);
            $table->string('mausac', 50);
            $table->string('ram', 10);
            $table->string('dungluong', 10);
            $table->integer('gia');
            $table->unsignedInteger('id_km')->nullable();
            $table->string('cauhinh', 100);
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
        Schema::dropIfExists('sanpham');
    }
}
