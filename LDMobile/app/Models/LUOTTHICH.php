<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LUOTTHICH extends Model
{
    use HasFactory;

    protected $table = 'luotthich';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'id_tk',
        'id_dg',
    ];

    public $timestamps = false;

    public function danhgiasp()
    {
        return $this->belongsToMany(DANHGIASP::class, 'id_dg');
    }
}
