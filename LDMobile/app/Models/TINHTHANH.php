<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TINHTHANH extends Model
{
    use HasFactory;

    protected $table = 'tinhthanh';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'tentt',
    ];

    public $timestamps = false;

    // chinhanh
    public function chinhanh()
    {
        return $this->hasMany(CHINHANH::class, 'id_tt');
    }
}
