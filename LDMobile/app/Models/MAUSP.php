<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MAUSP extends Model
{
    use HasFactory;

    protected $table = 'mausp';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'tenmau',
        'id_youtube',
        'id_ncc',
        'baohanh',
        'diachibaohanh',
        'trangthai',
    ];

    public $timestamps = false;

    // hinhanh
    public function hinhanh()
    {
        return $this->hasMany(HINHANH::class, 'id_msp');
    }

    // slideshow_ctmsp
    public function slideshow_ctmsp()
    {
        return $this->hasMany(SLIDESHOW_CTMSP::class, 'id_msp');
    }

    // nhacungcap
    public function nhacungcap()
    {
        return $this->belongsTo(NHACUNGCAP::class, 'id_ncc');
    }

    // sanpham
    public function sanpham()
    {
        return $this->hasMany(SANPHAM::class, 'id_msp');
    }
}
