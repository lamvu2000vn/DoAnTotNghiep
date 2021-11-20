<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BANNER extends Model
{
    use HasFactory;

    protected $table = 'banner';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'link',
        'hinhanh',
    ];

    public $timestamps = false;
}
