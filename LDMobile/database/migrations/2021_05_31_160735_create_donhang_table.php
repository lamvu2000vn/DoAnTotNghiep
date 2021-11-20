<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonhangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donhang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('thoigian', 20);
            $table->unsignedInteger('id_tk');
            $table->unsignedInteger('id_dh_dc')->nullable();
            $table->unsignedInteger('id_cn')->nullable();
            $table->string('pttt', 100);
            $table->unsignedInteger('id_vc')->nullable();
            $table->string('hinhthuc', 50);
            $table->integer('tongtien');
            $table->string('trangthaidonhang', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donhang');
    }
}
