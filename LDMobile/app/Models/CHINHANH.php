<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CHINHANH extends Model
{
    use HasFactory;

    protected $table = 'chinhanh';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'diachi',
        'sdt',
        'id_tt',
        'trangthai',
    ];

    public $timestamps = false;

    // kho
    public function kho()
    {
        return $this->belongsToMany(SANPHAM::class, 'kho', 'id_cn', 'id_sp')->withPivot('id', 'slton');
    }

    // tinhthanh
    public function tinhthanh()
    {
        return $this->belongsTo(TINHTHANH::class, 'id_tt');
    }
}
