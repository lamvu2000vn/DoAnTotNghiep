<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DANHGIASP extends Model
{
    use HasFactory;

    protected $table = 'danhgiasp';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_tk',
        'id_sp',
        'noidung',
        'thoigian',
        'soluotthich',
        'danhgia',
        'chinhsua'
    ];

    public $timestamps = false;

    // ctdg
    public function ctdg()
    {
        return $this->hasMany(CTDG::class, 'id_dg');
    }

    // phanhoi
    public function phanhoi()
    {
        return $this->hasMany(PHANHOI::class, 'id_dg');
    }

    public function luotthich()
    {
        return $this->hasMany(LUOTTHICH::class, 'id_dg');
    }
}
