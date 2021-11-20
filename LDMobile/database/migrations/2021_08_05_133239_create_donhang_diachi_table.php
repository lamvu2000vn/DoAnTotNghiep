<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonhangDiachiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donhang_diachi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hoten', 100);
            $table->string('diachi', 100);
            $table->string('phuongxa', 100);
            $table->string('quanhuyen', 100);
            $table->string('tinhthanh', 100);
            $table->string('sdt', 10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donhang_diachi');
    }
}
