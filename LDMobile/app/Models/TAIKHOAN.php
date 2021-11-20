<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TAIKHOAN extends Authenticatable
{
    use HasFactory;

    protected $table = 'taikhoan';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'sdt',
        'password',
        'email',
        'hoten',
        'anhdaidien',
        'loaitk',
        'htdn',
        'remember_token',
        'user_social_token',
        'login_status',
        'device_token',
        'thoigian',
        'trangthai',
    ];

    public $timestamps = false;

    // taikhoan_diachi
    public function taikhoan_diachi()
    {
        return $this->hasMany(TAIKHOAN_DIACHI::class, 'id_tk');
    }

    // thongbao
    public function thongbao()
    {
        return $this->hasMany(THONGBAO::class, 'id_tk');
    }

    // taikhoan_voucher
    public function taikhoan_voucher()
    {
        return $this->belongsToMany(VOUCHER::class, 'taikhoan_voucher', 'id_tk', 'id_vc')->withPivot('id', 'sl');
    }

    // phanhoi
    public function phanhoi()
    {
        return $this->belongsToMany(DANHGIASP::class, 'phanhoi', 'id_tk', 'id_dg')->withPivot('id', 'noidung', 'thoigian', 'trangthai');
    }

    // danhgiasp
    public function danhgiasp()
    {
        return $this->belongsToMany(SANPHAM::class, 'danhgiasp', 'id_tk', 'id_sp')->withPivot('id', 'noidung', 'thoigian', 'soluotthich', 'danhgia', 'trangthai');
    }

    // luotthich
    public function luotthich()
    {
        return $this->belongsToMany(DANHGIASP::class, 'luotthich', 'id_tk', 'id_dg');
    }

    // giohang
    public function giohang()
    {
        return $this->belongsToMany(SANPHAM::class, 'giohang', 'id_tk', 'id_sp')->withPivot('id', 'sl');
    }

    // sp_yeuthich
    public function sp_yeuthich()
    {
        return $this->belongsToMany(SANPHAM::class, 'sp_yeuthich', 'id_tk', 'id_sp')->withPivot('id');;
    }

    // donhang
    public function donhang()
    {
        return $this->hasMany(DONHANG::class, 'id_tk');
    }
}
