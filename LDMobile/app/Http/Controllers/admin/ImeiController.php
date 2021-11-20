<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;

use App\Models\IMEI;
use App\Models\SANPHAM;
use App\Models\MAUSP;

class ImeiController extends Controller
{
    public function __construct()
    {
        $this->admin = 'admin/content/';
        $this->IndexController = new IndexController;
    }
    public function index()
    {
        $lst_imei = IMEI::limit(10)->get();
        
        foreach($lst_imei as $key){
            $product = SANPHAM::find($key->id_sp);
            // sản phẩm
            $key->product = $product;
        }

        $data = [
            'lst_imei' => $lst_imei,
        ];

        return view($this->admin.'imei')->with($data);
    }

    public function AjaxSearch(Request $request)
    {
        if($request->ajax()){
            $lst_result = [];

            $keyword = $this->IndexController->unaccent($request->keyword);

            if(!$keyword){
                $lst_imei = IMEI::limit(10)->get();

                foreach($lst_imei as $imei){
                    $imei->product = SANPHAM::find($imei->id_sp);
                }
                
                return $lst_imei;
            }

            $count = 0;

            foreach(IMEI::all() as $imei){
                // lấy 10 bản ghi
                if($count === 10){
                    break;
                }

                $product = SANPHAM::find($imei->id_sp);

                $string = strtolower($this->IndexController->unaccent($imei->id.$product->tensp.$product->mausac
                    .$product->ram.$product->dungluong.$imei->imei.($imei->trangthai == 1 ? 'Đã kích hoạt' : 'Chưa kích hoạt')));

                if(str_contains($string, $keyword)){
                    $imei->product = $product;
                    array_push($lst_result, $imei);
                    $count++;
                }
            }

            return $lst_result;
        }
    }
}
