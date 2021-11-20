<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\user\IndexController;

use App\Models\MAUSP;
use App\Models\NHACUNGCAP;
use App\Models\SANPHAM;

class MauSanPhamController extends Controller
{
    public function __construct()
    {
        $this->admin='admin/content/';
        $this->IndexController = new IndexController;
    }
    public function index()
    {
        $lst_model = MAUSP::limit(10)->get();

        foreach($lst_model as $idx => $key){
            $supplierName = NHACUNGCAP::find($key->id_ncc)->tenncc;
            $lst_model[$idx]->nhacungcap = $supplierName;
        }

        $data = [
            'lst_supplier' => NHACUNGCAP::all(),
            'lst_model' => $lst_model,
        ];

        return view($this->admin."mau-san-pham")->with($data);
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            // kiểm tra trùng tên
            if(MAUSP::where('tenmau', $request->tenmau)->first()){
                return 'invalid name';
            }
            
            $data = [
                'tenmau' => $request->tenmau,
                'id_youtube' => $request->id_youtube,
                'id_ncc' => $request->id_ncc,
                'baohanh' => $request->baohanh,
                'diachibaohanh' => $request->diachibaohanh,
                'trangthai' => 1,
            ];

            $create = MAUSP::create($data);
            $create->supplierName = NHACUNGCAP::find($create->id_ncc)->tenncc;

            return [
                'id' => $create->id,
                'data' => [$create]
            ];
        }
    }

    public function update(Request $request, $id)
    {
        if($request->ajax()){
            $data = [
                'tenmau' => $request->tenmau,
                'id_youtube' => $request->id_youtube,
                'id_ncc' => $request->id_ncc,
                'baohanh' => $request->baohanh,
                'diachibaohanh' => $request->diachibaohanh,
                'trangthai' => $request->trangthai,
            ];

            MAUSP::where('id', $id)->update($data);
            SANPHAM::where('id_msp', $id)->update(['trangthai' => $data['trangthai']]);

            $newRow = MAUSP::find($id);
            $newRow->supplierName = NHACUNGCAP::find($newRow->id_ncc)->tenncc;
            
            return [$newRow];
        }
    }

    public function destroy($id)
    {
        MAUSP::where('id', $id)->update(['trangthai' => 0]);
        SANPHAM::where('id_msp', $id)->update(['trangthai' => 0]);
    }

    public function AjaxRestore(Request $request)
    {
        if($request->ajax()){
            MAUSP::find($request->id)->update(['trangthai' => 1]);
            SANPHAM::where('id_msp', $request->id)->update(['trangthai' => 1]);
        }
    }

    public function AjaxGetMausp(Request $request)
    {
        if($request->ajax()){
            return MAUSP::find($request->id);
        }
    }

    public function AjaxSearch(Request $request)
    {
        if($request->ajax()){
            $keyword = $this->IndexController->unaccent($request->keyword);
            $lst_result = [];

            if($keyword == ''){
                $data = MAUSP::limit(10)->get();
                foreach($data as $key){
                    $key->supplierName = NHACUNGCAP::find($key->id_ncc)->tenncc;
                }

                return $data;
            }

            foreach(MAUSP::all() as $key){
                $supplierName = NHACUNGCAP::find($key->id_ncc)->tenncc;
                $string = strtolower($this->IndexController->unaccent($key->id.$key->tenmau.$supplierName.($key->baohanh ? $key->baohanh : 'Không có').$key->diachibaohanh.($key->trangthai == 1 ? 'Kinh doanh' : 'Ngừng kinh doanh')));
                if(str_contains($string, $keyword)){
                    $key->supplierName = $supplierName;
                    array_push($lst_result, $key);
                }
            }

            return $lst_result;
        }
    }

    public function AjaxFilter(Request $request)
    {
        if($request->ajax()){
            $arrFilter = $request->arrFilter;
            $lst_search = [];
            $lst_temp = [];
            $lst_result = [];
            $keyword = $this->IndexController->unaccent($request->keyword);

            // có danh sách tìm kiếm
            if($keyword){
                $lst_search = $this->search($keyword);
            }

            // không có lọc
            if(empty($arrFilter)){
                // không có tìm kiếm
                if(empty($lst_search)){
                    $data = MAUSP::limit(10)->get();
                    foreach($data as $key){
                        $supplierName = NHACUNGCAP::find($key->id_ncc)->tenncc;
                        $key->supplierName = $supplierName;
                    }

                    return $data;
                } else {
                    foreach($lst_search as $key){
                        $supplierName = NHACUNGCAP::find($key->id_ncc)->tenncc;
                        $key->supplierName = $supplierName;
                    }

                    return $lst_search;
                }
            }

            // tiêu chí lọc đầu tiên trên danh sách tìm kiếm
            if(!empty($lst_search)){
                if(array_key_first($arrFilter) == 'supplier'){
                    foreach($arrFilter['supplier'] as $supplier){
                        foreach($lst_search as $key){
                            if($key->id_ncc == $supplier){
                                array_push($lst_temp, $key);
                            }
                        }
                    }
                } else {
                    foreach($arrFilter['status'] as $status){
                        foreach($lst_search as $key){
                            if($key->trangthai == $status){
                                array_push($lst_temp, $key);
                            }
                        }
                    }   
                }
            } else {
                if(array_key_first($arrFilter) == 'supplier'){
                    foreach($arrFilter['supplier'] as $key){
                        $lst_model = MAUSP::where('id_ncc', $key)->get();
                        foreach($lst_model as $model){
                            array_push($lst_temp, $model);
                        }
                    }
                } else {
                    foreach($arrFilter['status'] as $key){
                        $lst_model = MAUSP::where('trangthai', $key)->get();
                        foreach($lst_model as $model){
                            array_push($lst_temp, $model);
                        }
                    }   
                }
            }

            // chỉ có 1 tiêu chí
            if(count($arrFilter) == 1){
                foreach($lst_temp as $key){
                    $supplierName = NHACUNGCAP::find($key->id_ncc)->tenncc;
                    $key->supplierName = $supplierName;
                }

                return $lst_temp;
            }

            // tiếp tục lọc các tiêu chí còn lại
            // tiêu chí tiếp theo là nhà cung cấp
            if(array_keys($arrFilter)[1] == 'supplier'){
                foreach($arrFilter['supplier'] as $supplier){
                    foreach($lst_temp as $temp){
                        if($temp->id_ncc == $supplier){
                            array_push($lst_result, $temp);
                        }
                    }
                }
            } 
            // tiêu chí tiếp theo là trạng thái
            else {
                foreach($arrFilter['status'] as $status){
                    foreach($lst_temp as $temp){
                        if($temp->trangthai == $status){
                            array_push($lst_result, $temp);
                        }
                    }
                }
            }

            // render danh sách kết quả
            foreach($lst_result as $key){
                $supplierName = NHACUNGCAP::find($key->id_ncc)->tenncc;
                $key->supplierName = $supplierName;
            }

            return $lst_result;
        }
    }
    
    public function search($keyword)
    {
        $lst_result = [];

        foreach(MAUSP::all() as $key){
            $supplierName = NHACUNGCAP::find($key->id_ncc)->tenncc;
            $data = strtolower($this->IndexController->unaccent($key->id.$key->tenmau.$supplierName.($key->baohanh ? $key->baohanh : 'Không có').$key->diachibaohanh.($key->trangthai == 1 ? 'Kinh doanh' : 'Ngừng kinh doanh')));
            if(str_contains($data, $keyword)){
                array_push($lst_result, $key);
            }
        }

        return $lst_result;
    }
}
