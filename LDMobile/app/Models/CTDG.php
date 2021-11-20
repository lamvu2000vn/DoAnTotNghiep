<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CTDG extends Model
{
    use HasFactory;

    protected $table = 'ctdg';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'id_dg',
        'hinhanh',
    ];

    public $timestamps = false;

    // danhgiasp
    public function danhgiasp()
    {
        return $this->belongsTo(DANHGIASP::class, 'id_dg');
    }
}
