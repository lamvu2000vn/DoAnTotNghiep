<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IMEI extends Model
{
    use HasFactory;

    protected $table = 'imei';

    protected $fillable = [
        'id_sp',
        'imei',
        'trangthai',
    ];

    public $timestamps = false;
}
