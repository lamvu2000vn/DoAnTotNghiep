<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KHUYENMAI extends Model
{
    use HasFactory;

    protected $table = 'khuyenmai';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'tenkm',
        'noidung',
        'chietkhau',
        'ngaybatdau',
        'ngayketthuc',
    ];

    public $timestamps = false;

    // sanpham
    public function sanpham()
    {
        return $this->hasMany(SANPHAM::class, 'id_km');
    }
}
