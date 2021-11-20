<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DONHANG_DIACHI extends Model
{
    use HasFactory;

    protected $table = 'donhang_diachi';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'hoten',
        'diachi',
        'phuongxa',
        'quanhuyen',
        'tinhthanh',
        'sdt',
    ];

    public $timestamps = false;
}
