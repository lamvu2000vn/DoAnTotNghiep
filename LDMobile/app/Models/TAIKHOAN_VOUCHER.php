<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TAIKHOAN_VOUCHER extends Model
{
    use HasFactory;

    protected $table = 'taikhoan_voucher';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_vc',
        'id_tk',
        'sl'
    ];

    public $timestamps = false;
}
