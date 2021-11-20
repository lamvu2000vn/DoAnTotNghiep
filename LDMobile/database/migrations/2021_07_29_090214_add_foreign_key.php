<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // tài khoản địa chỉ
        Schema::table('taikhoan_diachi', function (Blueprint $table) {
            $table->foreign('id_tk')->references('id')->on('taikhoan');
        });

        // thông báo
        Schema::table('thongbao', function (Blueprint $table) {
            $table->foreign('id_tk')->references('id')->on('taikhoan');
        });

        // tài khoản voucher
        Schema::table('taikhoan_voucher', function (Blueprint $table) {
            $table->foreign('id_tk')->references('id')->on('taikhoan');
            $table->foreign('id_vc')->references('id')->on('voucher');
        });

        // đánh giá sp
        Schema::table('danhgiasp', function (Blueprint $table) {
            $table->foreign('id_tk')->references('id')->on('taikhoan');
            $table->foreign('id_sp')->references('id')->on('sanpham');
        });

        // phản hồi
        Schema::table('phanhoi', function (Blueprint $table) {
            $table->foreign('id_tk')->references('id')->on('taikhoan');
            $table->foreign('id_dg')->references('id')->on('danhgiasp');
        });

        // ctdg
        Schema::table('ctdg', function (Blueprint $table) {
            $table->foreign('id_dg')->references('id')->on('danhgiasp');
        });

        // lượt thích
        Schema::table('luotthich', function (Blueprint $table) {
            $table->foreign('id_tk')->references('id')->on('taikhoan');
            $table->foreign('id_dg')->references('id')->on('danhgiasp');
        });

        // giỏ hàng
        Schema::table('giohang', function (Blueprint $table) {
            $table->foreign('id_tk')->references('id')->on('taikhoan');
            $table->foreign('id_sp')->references('id')->on('sanpham');
        });

        // hình ảnh
        Schema::table('hinhanh', function (Blueprint $table) {
            $table->foreign('id_msp')->references('id')->on('mausp');
        });

        // slideshow ctmsp
        Schema::table('slideshow_ctmsp', function (Blueprint $table) {
            $table->foreign('id_msp')->references('id')->on('mausp');
        });

        // mẫu sp
        Schema::table('mausp', function (Blueprint $table) {
            $table->foreign('id_ncc')->references('id')->on('nhacungcap');
        });

        // ctdh
        Schema::table('ctdh', function (Blueprint $table) {
            $table->foreign('id_dh')->references('id')->on('donhang');
            $table->foreign('id_sp')->references('id')->on('sanpham');
        });

        // sản phẩm
        Schema::table('sanpham', function (Blueprint $table) {
            $table->foreign('id_msp')->references('id')->on('mausp');
            $table->foreign('id_km')->references('id')->on('khuyenmai');
        });

        // sp yêu thích
        Schema::table('sp_yeuthich', function (Blueprint $table) {
            $table->foreign('id_tk')->references('id')->on('taikhoan');
            $table->foreign('id_sp')->references('id')->on('sanpham');
        });

        // imei
        Schema::table('imei', function (Blueprint $table) {
            $table->foreign('id_sp')->references('id')->on('sanpham');
        });

        // bảo hành
        Schema::table('baohanh', function (Blueprint $table) {
            $table->foreign('id_imei')->references('id')->on('imei');
        });

        // đơn hàng
        Schema::table('donhang', function (Blueprint $table) {
            $table->foreign('id_tk')->references('id')->on('taikhoan');
            $table->foreign('id_vc')->references('id')->on('voucher');
            $table->foreign('id_tk_dc')->references('id')->on('taikhoan_diachi');
            $table->foreign('id_cn')->references('id')->on('chinhanh');
        });

        // kho
        Schema::table('kho', function (Blueprint $table) {
            $table->foreign('id_cn')->references('id')->on('chinhanh');
            $table->foreign('id_sp')->references('id')->on('sanpham');
        });

        // chi nhánh
        Schema::table('chinhanh', function (Blueprint $table) {
            $table->foreign('id_tt')->references('id')->on('tinhthanh');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
