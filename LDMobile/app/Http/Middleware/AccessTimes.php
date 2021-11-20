<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

use App\Models\LUOTTRUYCAP;
use App\Models\TAIKHOAN;

class AccessTimes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!Session::get('visitor')){
            Session::put('visitor', '1');
            LUOTTRUYCAP::create([
                'nentang' => 'web',
                'thoigian' => date('d/m/Y H:i:s')
            ]);
        }

        if(Auth::check() && !session('user')){
            $user = TAIKHOAN::find(Auth::user()->id);
            session(['user' => $user]);
        }

        return $next($request);
    }
}
