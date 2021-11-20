<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SLIDESHOW_CTMSP extends Model
{
    use HasFactory;

    protected $table = 'slideshow_ctmsp';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
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
