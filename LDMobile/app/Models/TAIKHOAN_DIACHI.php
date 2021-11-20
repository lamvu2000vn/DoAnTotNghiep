<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TAIKHOAN_DIACHI extends Model
{
    use HasFactory;

    protected $table = 'taikhoan_diachi';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_tk',
        'hoten',
        'diachi',
        'phuongxa',
        'quanhuyen',
        'tinhthanh',
        'sdt',
        'macdinh'
    ];

    public $timestamps = false;

    // taikhoan
    public function taikhoan()
    {
        return $this->belongsTo(TAIKHOAN::class, 'id_tk');
    }
}
