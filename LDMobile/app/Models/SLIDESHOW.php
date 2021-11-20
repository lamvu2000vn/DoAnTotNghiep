<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SLIDESHOW extends Model
{
    use HasFactory;

    protected $table = 'slideshow';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'link',
        'hinhanh',
    ];

    public $timestamps = false;
}
