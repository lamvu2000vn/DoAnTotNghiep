<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaikhoanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taikhoan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sdt', 10)->unique()->nullable();
            $table->string('password', 500)->nullable();
            $table->string('email', 100)->unique()->nullable();
            $table->string('hoten', 100);
            $table->string('anhdaidien', 500)->nullable();
            $table->boolean('loaitk');
            $table->string('htdn', 10);
            $table->string('remember_token', 500)->nullable();
            $table->string('user_social_token', 500)->nullable();
            $table->boolean('login_status')->nullable();
            $table->string('device_token', 500)->nullable();
            $table->string('thoigian', 20);
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
        Schema::dropIfExists('taikhoan');
    }
}
