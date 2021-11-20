<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaikhoanDiachiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taikhoan_diachi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_tk');
            $table->string('hoten', 100);
            $table->string('diachi', 100);
            $table->string('phuongxa', 100);
            $table->string('quanhuyen', 100);
            $table->string('tinhthanh', 100);
            $table->string('sdt', 10);
            $table->boolean('macdinh');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taikhoan_diachi');
    }
}
