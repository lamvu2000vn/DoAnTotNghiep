<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Session;

use App\Models\NHACUNGCAP;
use App\Models\TAIKHOAN;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // supplier
        $lst_brand = [];

        foreach(NHACUNGCAP::all() as $i => $key){
            $lst_brand[$i]['brand'] = explode(' ', $key->tenncc)[0];
            $lst_brand[$i]['image'] = $key->anhdaidien;
        }

        // url user
        View::share('lst_brand', $lst_brand);
        View::share('url_phone', 'images/phone/');
        View::share('url_logo', 'images/logo/');
        View::share('url_slide', 'images/slideshow/');
        View::share('url_banner', 'images/banner/');
        View::share('url_json', 'json/');
        View::share('url_model_slide', 'images/phone/slideshow/');
        View::share('url_user', 'images/user/');
        View::share('url_evaluate', 'images/evaluate/');
    }
}
