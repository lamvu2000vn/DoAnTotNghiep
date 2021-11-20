<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LUOTTRUYCAP extends Model
{
    use HasFactory;

    protected $table = 'luottruycap';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'nentang',
        'thoigian'
    ];

    public $timestamps = false;
}
