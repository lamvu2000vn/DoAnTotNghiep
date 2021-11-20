<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\user\IndexController;

use App\Models\KHUYENMAI;

class KhuyenMaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->admin='admin/content/';
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->IndexController = new IndexController;
    }
    public function index()
    {
        $lst_promotion = KHUYENMAI::limit(10)->get();

        foreach($lst_promotion as $i => $key){
            $dateEnd = strtotime(str_replace('/', '-', $key->ngayketthuc));
            $lst_promotion[$i]->trangthai = $dateEnd >= strtotime(date('d-m-Y')) ? 1 : 0;
        }

        $data = [
            'lst_promotion' => $lst_promotion,
        ];

        return view($this->admin."khuyen-mai")->with($data);
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            $data = [
                'tenkm' => $request->tenkm,
                'noidung' => $request->noidung,
                'chietkhau' => $request->chietkhau,
                'ngaybatdau' => date('d/m/Y', strtotime($request->ngaybatdau)),
                'ngayketthuc' => date('d/m/Y', strtotime($request->ngayketthuc)),
            ];

            // kiểm tra đã tồn tại
            $exists = KHUYENMAI::where('tenkm', $data['tenkm'])
                                ->where('noidung', $data['noidung'])
                                ->where('chietkhau', $data['chietkhau'])
                                ->where('ngaybatdau', $data['ngaybatdau'])
                                ->where('ngayketthuc', $data['ngayketthuc'])
                                ->first();

            if($exists){
                return 'already exist';
            }

            $create = KHUYENMAI::create($data);
            $status = strtotime(str_replace('/', '-', $create->ngayketthuc)) >= strtotime(date('d-m-Y')) ? 'Hoạt động' : 'Hết hạn';

            $create->status = $status;

            return [
                'id' => $create->id, 
                'data' => [$create],
            ];
        }
    }

    public function update(Request $request, $id)
    {
        if($request->ajax()){
            $data = [
                'tenkm' => $request->tenkm,
                'noidung' => $request->noidung,
                'chietkhau' => $request->chietkhau,
                'ngaybatdau' => date('d/m/Y', strtotime($request->ngaybatdau)),
                'ngayketthuc' => date('d/m/Y', strtotime($request->ngayketthuc)),
            ];

            // kiểm tra đã tồn tại
            $exists = KHUYENMAI::where('tenkm', $data['tenkm'])
                                ->where('noidung', $data['noidung'])
                                ->where('chietkhau', $data['chietkhau'])
                                ->where('ngaybatdau', $data['ngaybatdau'])
                                ->where('ngayketthuc', $data['ngayketthuc'])
                                ->first();

            if($exists){
                return 'already exist';
            }

            KHUYENMAI::where('id', $id)->update($data);

            $newRow = KHUYENMAI::find($id);
            $status = strtotime(str_replace('/', '-', $newRow->ngayketthuc)) >= strtotime(date('d-m-Y')) ? 'Hoạt động' : 'Hết hạn';

            $newRow->status = $status;

            return [$newRow];
        }
    }

    public function destroy($id)
    {
        KHUYENMAI::destroy($id);
    }

    public function AjaxGetKhuyenMai(Request $request)
    {
        if($request->ajax()){
            $result =  KHUYENMAI::find($request->id);
            // format YYY-MM-dd
            $temp = explode('/', $result->ngaybatdau);
            $start = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
            $result->ngaybatdau = $start;

            $temp = explode('/', $result->ngayketthuc);
            $end = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
            $result->ngayketthuc = $end;

            return $result;
        }
    }

    public function AjaxSearch(Request $request)
    {
        if($request->ajax()){
            $keyword = $this->IndexController->unaccent($request->keyword);
            $lst_result = [];

            if($keyword == ''){
                $lst_promotion = KHUYENMAI::limit(10)->get();
                foreach($lst_promotion as $key){
                    $status = strtotime(str_replace('/', '-', $key->ngayketthuc)) >= strtotime(date('d-m-Y')) ? 'Hoạt động' : 'Hết hạn';
                    $key->status = $status;
                }
                return $lst_promotion;
            }

            foreach(KHUYENMAI::all() as $key){
                // trạng thái
                $status = strtotime(str_replace('/', '-', $key->ngayketthuc)) >= strtotime(date('d-m-Y')) ? 'Hoạt động' : 'Hết hạn';
                $string = strtolower($this->IndexController->unaccent($key->id.$key->tenkm.$key->noidung.($key->chietkhau*100).'%'.$key->ngaybatdau.$key->ngayketthuc.$status));
                if(str_contains($string, $keyword)){
                    $key->status = $status;
                    array_push($lst_result, $key);
                }
            }

            return $lst_result;
        }
    }
}
