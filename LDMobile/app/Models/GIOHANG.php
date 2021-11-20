<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GIOHANG extends Model
{
    use HasFactory;

    protected $table = 'giohang';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id_tk',
        'id_sp',
        'sl',
    ];

    public $timestamps = false;
}
