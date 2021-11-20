<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\View\Composers\UserComposer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Session;
use Cookie;

use App\Models\TAIKHOAN;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function($view){
            if(Auth::check()){
                if(!session('user')){
                    $user = Auth::user();
                    session(['user' => $user]);
                }
            } else {
                if(!session('user')){
                    $account_social_id = Cookie::get('account_social_id');
                    if($account_social_id){
                        $platform = explode('_', $account_social_id)[0];
                        $id_tk = explode('_', $account_social_id)[1];
    
                        $socialAccount = TAIKHOAN::find($id_tk);
        
                        // facebook
                        if($socialAccount && $socialAccount->htdn == 'facebook'){
                            if(!session('login_status')){
                                $token = $socialAccount->user_social_token;
                                $app_id = '3264702020428100';
                                $secrect_id = 'be57a75f0b07f0966f0d224bd2e102b4';
                
                                // kiểm tra token còn hợp lệ không
                                $validToken = Http::get("https://graph.facebook.com/debug_token?input_token={$token}&access_token={$app_id}|{$secrect_id}")->json();
                                // không hợp lệ
                                if(array_key_first($validToken) == 'error'){
                                    Session::flush();
                                    Cookie::forget('account_social_id');
                                    Session::flash('login_status', false);
                                    $socialAccount->login_status = 0;
                                    $socialAccount->save();
                                } 
                                // hợp lệ
                                else {
                                    Auth::login($socialAccount, true);
                                    session(['user' => $socialAccount]);
                                }
                            }
                        }
                    }
                }
            }   
        });

        View::composer([
            'user.header.header',
            'user.content.tai-khoan',
            'user.content.taikhoan.sec-chi-tiet-don-hang',
            'user.content.taikhoan.sec-dia-chi',
            'user.content.taikhoan.sec-don-hang',
            'user.content.taikhoan.sec-tai-khoan',
            'user.content.taikhoan.sec-thanh-chuc-nang',
            'user.content.taikhoan.sec-thong-bao',
            'user.content.taikhoan.voucher',
            'user.content.taikhoan.yeu-thich',
            'user.content.gio-hang',
            'user.content.thanh-toan',
            'user.content.dia-chi-giao-hang'
        ], UserComposer::class);
    }
}
