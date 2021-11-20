<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CTDH extends Model
{
    use HasFactory;

    protected $table = 'ctdh';

    protected $fillable = [
        'id_dh',
        'id_sp',
        'gia',
        'sl',
        'giamgia',
        'thanhtien',
    ];

    public $timestamps = false;
}
