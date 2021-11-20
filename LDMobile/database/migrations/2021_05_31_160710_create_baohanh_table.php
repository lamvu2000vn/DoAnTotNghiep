<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaohanhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baohanh', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_imei')->unique();
            $table->string('imei', 15)->unique();
            $table->string('ngaymua', 20);
            $table->string('ngayketthuc', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('baohanh');
    }
}
