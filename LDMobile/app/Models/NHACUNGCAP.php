<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NHACUNGCAP extends Model
{
    use HasFactory;

    protected $table = 'nhacungcap';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'tenncc',
        'anhdaidien',
        'diachi',
        'sdt',
        'email',
        'trangthai',
    ];

    public $timestamps = false;

    // mausp
    public function mausp()
    {
        return $this->hasMany(MAUSP::class, 'id_ncc');
    }
}
