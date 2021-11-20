<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BAOHANH extends Model
{
    use HasFactory;

    protected $table = 'baohanh';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_imei',
        'imei',
        'ngaymua',
        'ngayketthuc'
    ];

    public $timestamps = false;
}
