<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;

use App\Models\BAOHANH;
use App\Models\SANPHAM;
use App\Models\MAUSP;
use App\Models\IMEI;

class BaoHanhController extends Controller
{
    public function __construct()
    {
        $this->admin='admin/content/';
        $this->IndexController = new IndexController;
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    public function index()
    {
        $lst_warranty = BAOHANH::limit(10)->get();
        foreach($lst_warranty as $i => $key){
            $lst_warranty[$i]->sanpham = SANPHAM::find(IMEI::find($key->id_imei)->id_sp);
        }
        $data = [
            'lst_warranty' => $lst_warranty,
        ];

        return view($this->admin."bao-hanh")->with($data);
    }

    public function AjaxGetBaoHanh(Request $request)
    {
        if($request->ajax()){
            $warranty = BAOHANH::find($request->id);
            $id_sp = IMEI::find($warranty->id_imei)->id_sp;

            $warranty->sanpham = SANPHAM::find($id_sp);
            $warranty->baohanh = MAUSP::find(SANPHAM::find($id_sp)->id_msp)->baohanh;
            // có bảo hành
            if($warranty->baohanh){
                $warranty->trangthai = $this->warrantyStatus($warranty->ngayketthuc);
            }
            // không có bảo hành
            else {
                $warranty->trangthai = 'no';
            }

            return $warranty;
        }
    }

    // kiểm tra còn bảo hành không
    public function warrantyStatus($dateEnd){
        $status = 0;

        // ngày kết thúc
        $end = strtotime(str_replace('/', '-', $dateEnd));
        // ngày hiện tại
        $current = strtotime(date('d-m-Y'));
        // kiểm tra còn bảo hành không
        return $end >= $current ? 1 : 0;
    }

    public function AjaxSearch(Request $request)
    {
        if($request->ajax()){
            $keyword = $this->IndexController->unaccent($request->keyword);
            $lst_result = [];

            if($keyword == ''){
                $lst_warranty = BAOHANH::limit(10)->get();
                return $lst_warranty;
            }

            foreach(BAOHANH::all() as $key){
                $data = strtolower($this->IndexController->unaccent($key->id.$key->imei.$key->ngaymua.$key->ngayketthuc));
                if(str_contains($data, $keyword)){
                    array_push($lst_result, $key);
                }
            }
            return $lst_result;
        }
    }
}
