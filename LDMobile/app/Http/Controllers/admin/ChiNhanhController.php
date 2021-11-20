<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;

use App\Models\CHINHANH;
use App\Models\TINHTHANH;

class ChiNhanhController extends Controller
{
    public function __construct()
    {
        $this->admin='admin/content/';
        $this->IndexController = new IndexController;
    }
    public function index()
    {
        // danh sách chi nhánh
        $lst_branch = CHINHANH::limit(10)->get();
        foreach($lst_branch as $i => $key){
            $lst_branch[$i]->tinhthanh = TINHTHANH::find($key->id_tt)->tentt;
        }


        $data = [
            'lst_branch' => $lst_branch,
            'lst_province' => TINHTHANH::all(),
        ];

        return view($this->admin.'chi-nhanh')->with($data);
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            $data = [
                'diachi' => $request->diachi,
                'sdt' => $request->sdt,
                'id_tt' => $request->id_tt,
                'trangthai' => $request->trangthai,
            ];

            $create = CHINHANH::create($data);
            $cityName = TINHTHANH::find($create->id_tt)->tentt;
            $create->cityName = $cityName;

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
                'diachi' => $request->diachi,
                'sdt' => $request->sdt,
                'id_tt' => $request->id_tt,
                'trangthai' => $request->trangthai,
            ];

            CHINHANH::where('id', $id)->update($data);

            $newRow = CHINHANH::find($id);
            $cityName = TINHTHANH::find($newRow->id_tt)->tentt;
            $newRow->cityName = $cityName;

            return [$newRow];
        }
    }

    public function destroy($id)
    {
        CHINHANH::where('id', $id)->update(['trangthai' => 0]);
    }

    public function AjaxRestore(Request $request)
    {
        if($request->ajax()){
            CHINHANH::where('id', $request->id)->update(['trangthai' => 1]);
        }
    }

    public function AjaxGetChiNhanh(Request $request)
    {
        if($request->ajax()){
            return CHINHANH::find($request->id);
        }
    }

    public function AjaxSearch(Request $request)
    {
        if($request->ajax()){
            $keyword = $this->IndexController->unaccent($request->keyword);
            $lst_result = [];

            if($keyword == ''){
                $lst_branch = CHINHANH::limit(10)->get();
                foreach($lst_branch as $key){
                    $key->cityName = TINHTHANH::find($key->id_tt)->tentt;
                }
                return $lst_branch;
            }

            foreach(CHINHANH::all() as $key){
                $province = TINHTHANH::find($key->id_tt)->tentt;
                $string = strtolower($this->IndexController->unaccent($key->diachi.$key->sdt.$province.($key->trangthai == 1 ? 'Hoạt động' : 'Ngừng hoạt động')));
                if(str_contains($string, $keyword)){
                    $key->cityName = TINHTHANH::find($key->id_tt)->tentt;

                    array_push($lst_result, $key);
                }
            }
            return $lst_result;
        }
    }
}
