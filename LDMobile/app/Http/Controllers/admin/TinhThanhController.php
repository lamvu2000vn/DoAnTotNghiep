<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;

use App\Models\TINHTHANH;

class TinhThanhController extends Controller
{
    public function __construct()
    {
        $this->admin = 'admin/content/';
        $this->IndexController = new IndexController;
    }
    public function index()
    {
        $data = [
            'lst_province' => TINHTHANH::limit(10)->get(),
        ];

        return view($this->admin.'tinh-thanh')->with($data);
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            $data = [
                'tentt' => $request->tentt,
            ];

            // đã có tỉnh thành
            if(TINHTHANH::where('tentt', 'like', $data['tentt'])->first()){
                return 'exists';
            }

            $create = TINHTHANH::create($data);
            
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
                'tentt' => $request->tentt,
            ];

            // đã có tỉnh thành
            if(TINHTHANH::where('tentt', 'like', $data['tentt'])->first()){
                return 'exists';
            }

            TINHTHANH::where('id', $id)->update($data);

            $newRow = TINHTHANH::find($id);

            return [$newRow];
        }
    }

    public function destroy($id)
    {
        TINHTHANH::destroy($id);
    }

    public function AjaxGetTinhThanh(Request $request)
    {
        if($request->ajax()){
            return TINHTHANH::find($request->id);
        }
    }

    public function AjaxSearch(Request $request)
    {
        if($request->ajax()){
            $keyword = $this->IndexController->unaccent($request->keyword);
            $lst_result = [];

            if($keyword == ''){
                $lst_city = TINHTHANH::limit(10)->get();

                return $lst_city;
            }

            foreach(TINHTHANH::all() as $key){
                $data = strtolower($this->IndexController->unaccent($key->id.$key->tentt));
                if(str_contains($data, $keyword)){
                    array_push($lst_result, $key);
                }
            }

            return $lst_result;
        }
    }
}
