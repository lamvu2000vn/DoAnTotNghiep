<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PHANHOI extends Model
{
    use HasFactory;

    protected $table = 'phanhoi';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_tk',
        'id_dg',
        'noidung',
        'thoigian'
    ];

    public $timestamps = false;

    // danhgiasp
    public function danhgiasp()
    {
        return $this->belongsTo(DANHGIASP::class, 'id_dg');
    }
}
