<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KHO extends Model
{
    use HasFactory;

    protected $table = 'kho';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_cn',
        'id_sp',
        'slton',
    ];

    public $timestamps = false;
}
