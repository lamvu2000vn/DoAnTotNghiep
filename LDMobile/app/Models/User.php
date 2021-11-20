<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'taikhoan';
    public $timestamps = false;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'sdt',
        'password',
        'email',
        'hoten',
        'anhdaidien',
        'loaitk',
        'htdn',
        'remember_token',
        'user_social_token',
        'login_status',
        'device_token',
        'thoigian',
        'trangthai',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function AauthAcessToken(){
        return $this->hasMany('\App\Models\OauthAccessToken');
    }
}
