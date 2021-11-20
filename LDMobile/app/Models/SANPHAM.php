<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SANPHAM extends Model
{
    use HasFactory;

    protected $table = 'sanpham';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'tensp',
        'id_msp',
        'hinhanh',
        'mausac',
        'ram',
        'dungluong',
        'gia',
        'id_km',
        'cauhinh',
        'trangthai',
    ];

    public $timestamps = false;

    // mausp
    public function mausp()
    {
        return $this->belongsTo(MAUSP::class, 'id_msp');
    }

    // danhgiasp
    public function danhgiasp()
    {
        return $this->belongsToMany(TAIKHOAN::class, 'danhgiasp', 'id_sp', 'id_tk')->withPivot('id', 'noidung', 'thoigian', 'soluotthich', 'danhgia', 'trangthai');
    }

    // luotthich
    public function luotthich()
    {
        return $this->belongsToMany(TAIKHOAN::class, 'luotthich', 'id_sp', 'id_tk');
    }

    // giohang
    public function giohang()
    {
        return $this->belongsToMany(TAIKHOAN::class, 'giohang', 'id_sp', 'id_tk')->withPivot('id', 'sl');
    }

    // sp_yeuthich
    public function sp_yeuthich()
    {
        return $this->belongsToMany(TAIKHOAN::class, 'sp_yeuthich', 'id_sp', 'id_tk')->withPivot('id');
    }

    // ctdh
    public function ctdh()
    {
        return $this->belongsToMany(DONHANG::class, 'ctdh', 'id_sp', 'id_dh')->withPivot('gia', 'sl', 'giamgia', 'thanhtien');
    }

    // khuyenmai
    public function khuyenmai()
    {
        return $this->belongsTo(KHUYENMAI::class, 'id_km');
    }

    // kho
    public function kho()
    {
        return $this->belongsToMany(CHINHANH::class, 'kho', 'id_sp', 'id_cn')->withPivot('id', 'slton');
    }

    // baohanh
    public function baohanh()
    {
        return $this->hasOneThrough(
            BAOHANH::class,
            IMEI::class,
            'id_sp',
            'id_imei',
            'id',
            'id'
        );
    }
}
