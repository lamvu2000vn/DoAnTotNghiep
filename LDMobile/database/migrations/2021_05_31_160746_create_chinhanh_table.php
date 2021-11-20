<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChinhanhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chinhanh', function (Blueprint $table) {
            $table->increments('id');
            $table->string('diachi', 200);
            $table->string('sdt', 10);
            $table->unsignedInteger('id_tt');
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
        Schema::dropIfExists('chinhanh');
    }
}
