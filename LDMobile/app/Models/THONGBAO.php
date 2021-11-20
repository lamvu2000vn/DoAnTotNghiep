<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class THONGBAO extends Model
{
    use HasFactory;

    protected $table = 'thongbao';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_tk',
        'tieude',
        'noidung',
        'thoigian',
        'trangthaithongbao',
    ];

    public $timestamps = false;

    // taikhoan
    public function taikhoan()
    {
        return $this->belongsTo(TAIKHOAN::class, 'id_tk');
    }
}
