<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;

use App\Models\NHACUNGCAP;
use App\Models\MAUSP;
use App\Models\SANPHAM;

class NhaCungCapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->admin='admin/content/';
        $this->IndexController = new IndexController;
    }
    public function index()
    {
        // danh sách nhà cung cấp
        $lst_supplier = NHACUNGCAP::limit(10)->get();

        $data = [
            'lst_supplier' => $lst_supplier,
        ];

        return view($this->admin."nha-cung-cap")->with($data);
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            // định dạng hình
            $imageFormat = $this->IndexController->getImageFormat($request->anhdaidien);
            if($imageFormat == 'png'){
                $base64 = str_replace('data:image/png;base64,', '', $request->anhdaidien);
                $imageName = strtolower(str_replace(' ','-', $this->IndexController->unaccent($request->tenncc))).'.png';
            } else {
                $base64 = str_replace('data:image/jpeg;base64,', '', $request->anhdaidien);
                $imageName = strtolower(str_replace(' ','-', $this->IndexController->unaccent($request->tenncc))).'.jpg';
            }
            // lưu hình
            $this->IndexController->saveImage('images/logo/'.$imageName, $base64);

            $data = [
                'tenncc' => $request->tenncc,
                'anhdaidien' => $imageName,
                'diachi' => $request->diachi,
                'sdt' => $request->sdt,
                'email' => $request->email,
                'trangthai' => $request->trangthai,
            ];

            // đã tồn tại
            $exists = NHACUNGCAP::where('tenncc', $data['tenncc'])
                                ->where('anhdaidien', $data['anhdaidien'])
                                ->where('diachi', $data['diachi'])
                                ->where('sdt', $data['sdt'])
                                ->where('email', $data['email'])
                                ->where('trangthai', $data['trangthai'])
                                ->first();
            if($exists){
                return 'exists';
            }

            $create = NHACUNGCAP::create($data);

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
                'tenncc' => $request->tenncc,
                'diachi' => $request->diachi,
                'sdt' => $request->sdt,
                'email' => $request->email,
                'trangthai' => $request->trangthai,
            ];

            $oldData = NHACUNGCAP::find($id);

            // nếu có chỉnh sửa hình
            if($request->anhdaidien){
                // xóa hình cũ
                unlink('images/logo/' . $oldData->anhdaidien);

                // định dạng hình
                $imageFormat = $this->IndexController->getImageFormat($request->anhdaidien);
                if($imageFormat == 'png'){
                    $base64 = str_replace('data:image/png;base64,', '', $request->anhdaidien);
                    $imageName = strtolower(str_replace(' ','-', $this->IndexController->unaccent($request->tenncc))).'.png';
                } else {
                    $base64 = str_replace('data:image/jpeg;base64,', '', $request->anhdaidien);
                    $imageName = strtolower(str_replace(' ','-', $this->IndexController->unaccent($request->tenncc))).'.jpg';
                }
                // lưu hình
                $this->IndexController->saveImage('images/logo/'.$imageName, $base64);

                $data['anhdaidien'] = $imageName;
            } else {
                $data['anhdaidien'] = $oldData->anhdaidien;
            }

            NHACUNGCAP::where('id', $id)->update($data);

            // cập nhật trạng thái mẫu sản phẩm
            $model = MAUSP::where('id_ncc', $id)->get();
            MAUSP::where('id_ncc', $id)->update(['trangthai' => $data['trangthai']]);

            // cập nhật sản phẩm
            foreach($model as $key){
                SANPHAM::where('id_msp', $key['id'])->update(['trangthai' => $data['trangthai']]);
            }

            $newRow = NHACUNGCAP::find($id);

            return [$newRow];
        }
    }

    public function destroy($id)
    {
        // xóa nhà cung cấp
        NHACUNGCAP::where('id', $id)->update(['trangthai' => 0]);

        // xóa mẫu sản phẩm
        $model = MAUSP::where('id_ncc', $id)->get();
        MAUSP::where('id_ncc', $id)->update(['trangthai' => 0]);

        // xóa sản phẩm
        foreach($model as $key){
            SANPHAM::where('id_msp', $key['id'])->update(['trangthai' => 0]);
        }
    }

    public function AjaxRetore(Request $request)
    {
        if($request->ajax()){
            // khôi phục ncc
            NHACUNGCAP::where('id', $request->id)->update(['trangthai' => 1]);

            // khôi phục mẫu sp
            $model = MAUSP::where('id_ncc', $request->id)->get();
            MAUSP::where('id_ncc', $request->id)->update(['trangthai' => 1]);

            // khôi phục sản phẩm
            foreach($model as $key){
                SANPHAM::where('id_msp', $key['id'])->update(['trangthai' => 1]);
            }
        }
    }

    public function AjaxGetNCC(Request $request)
    {
        if($request->ajax()){
            return NHACUNGCAP::find($request->id);
        }
    }

    public function AjaxSearch(Request $request)
    {
        if($request->ajax()){
            $keyword = $this->IndexController->unaccent($request->keyword);
            $lst_result = [];

            if($keyword == ''){
                return NHACUNGCAP::limit(10)->get();
            }

            foreach(NHACUNGCAP::all() as $key){
                $data = strtolower($this->IndexController->unaccent($key->id.$key->tenncc.$key->diachi.$key->sdt.$key->email.($key->trangthai == 1 ? 'Kinh doanh' : 'Ngừng kinh doanh')));
                if(str_contains($data, $keyword)){
                    array_push($lst_result, $key);
                }
            }

            return $lst_result;
        }
    }
}
