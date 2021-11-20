<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHangdoiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hangdoi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_tk');
            $table->string('nentang', 5);
            $table->bigInteger('timestamp')->nullable();
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
        Schema::dropIfExists('hangdoi');
    }
}
