<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HINHANH extends Model
{
    use HasFactory;

    protected $table = 'hinhanh';

    protected $fillable = [
        'id_msp',
        'hinhanh',
    ];

    public $timestamps = false;

    // mausp
    public function mausp()
    {
        return $this->belongsTo(MAUSP::class, 'id_msp');
    }
}
