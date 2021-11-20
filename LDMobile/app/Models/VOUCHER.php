<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VOUCHER extends Model
{
    use HasFactory;

    protected $table = 'voucher';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'noidung',
        'chietkhau',
        'dieukien',
        'ngaybatdau',
        'ngayketthuc',
        'sl',
    ];

    public $timestamps = false;

    // taikhoan_voucher
    public function taikhoan_voucher()
    {
        return $this->belongsToMany(TAIKHOAN::class, 'taikhoan_voucher', 'id_vc', 'id_tk')->withPivot('id', 'sl');
    }

    // donhang
    public function donhang()
    {
        return $this->hasMany(DONHANG::class, 'id_vc');
    }
}
