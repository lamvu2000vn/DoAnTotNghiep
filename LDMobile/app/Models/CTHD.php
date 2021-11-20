<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CTHD extends Model
{
    use HasFactory;

    protected $table = 'cthd';

    protected $fillable = [
        'id_hd',
        'id_sp',
        'sl'
    ];

    public $timestamps = false;
}
