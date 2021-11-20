<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DONHANG extends Model
{
    use HasFactory;

    protected $table = 'donhang';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'thoigian',
        'id_tk',
        'id_dh_dc',
        'id_cn',
        'ic_cn',
        'id_dh',
        'pttt',
        'id_vc',
        'hinhthuc',
        'tongtien',
        'trangthaidonhang',
    ];

    public $timestamps = false;

    // taikhoan
    public function taikhoan()
    {
        return $this->belongsTo(TAIKHOAN::class, 'id_tk');
    }

    // voucher
    public function voucher()
    {
        return $this->belongsTo(VOUCHER::class, 'id_vc');
    }

    // ctdh
    public function ctdh()
    {
        return $this->belongsToMany(SANPHAM::class, 'ctdh', 'id_dh', 'id_sp')->withPivot('gia', 'sl', 'giamgia', 'thanhtien');
    }
}
