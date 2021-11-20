<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;

use App\Models\MAUSP;
use App\Models\HINHANH;
use App\Models\SANPHAM;

class HinhAnhController extends Controller
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

        // chưa có thư mục lưu hình
        if(!is_dir('images/banner')){
            // tạo thư mục lưu hình
            mkdir('images/banner', 0777, true);
        }
    }
    public function index()
    {
        // danh sách hình ảnh theo mẫu
        $lst_image = [];
        foreach(MAUSP::limit(10)->get() as $model){
            $temp = [
                'id_msp' => $model->id,
                'tenmau' => $model->tenmau,
                'hinhanh' => [],
            ];

            $image = HINHANH::where('id_msp', $model->id)->get();
            foreach($image as $key){
                array_push($temp['hinhanh'], $key['hinhanh']);
            }

            array_push($lst_image, $temp);
        }

        $data = [
            'lst_image' => $lst_image,
        ];

        return view($this->admin."hinh-anh")->with($data);
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            $model = MAUSP::find($request->id_msp);

            foreach($request->lst_base64 as $i => $key){
                // định dạng hình
                $format = $this->IndexController->getImageFormat($key);

                $base64 = str_replace('data:image/'.$format.';base64,', '', $key);
                $imageName = strtolower(str_replace(' ', '_', $model->tenmau.' '.time().$i.'.'.$format));

                // lưu hình
                $this->IndexController->saveImage('images/phone/'.$imageName, $base64);

                $data = [
                    'id_msp' => $model->id,
                    'hinhanh' => $imageName,
                ];

                HINHANH::create($data);
            }

            $models = MAUSP::all();
            foreach($models as $model){
                $imageQty = HINHANH::where('id_msp', $model->id)->count();
                $model->imageQty = $imageQty;
            }   

            return [
                'id' => $model->id,
                'data' => $models,
            ];
        }
    }

    public function update(Request $request, $id)
    {
        if($request->ajax()){
            $model = MAUSP::find($id);
            // mảng các tên hình cũ
            $oldNameList = [];

            // xóa hình cũ
            if($request->lst_delete) {
                foreach($request->lst_delete as $nameDelete){
                    // tách chỉ lấy tên hình
                    $dotList = explode('.', $nameDelete);
                    // bỏ đuôi hình
                    array_pop($dotList);
                    // chuyển mảng thành chuỗi
                    $name = implode('', $dotList);
                    
                    unlink('images/phone/' . $nameDelete);
                    HINHANH::where('id_msp', $id)->where('hinhanh', $nameDelete)->delete();
                    array_push($oldNameList, $name);
                }
            }

            // cập nhật hình mới
            $lst_base64 = $request->lst_base64;
            if($lst_base64){
                // số lần lặp dựa theo mảng nào nhiều phần tử hơn
                $length = count($lst_base64);
                $oldNameQty = count($oldNameList);

                if($oldNameQty > $length) {
                    $length = $oldNameQty;
                }

                for($i = 0; $i < $length; $i++) {
                    if(isset($lst_base64[$i])) {
                        // định dạng hình
                        $format = $this->IndexController->getImageFormat($lst_base64[$i]);
    
                        $base64 = str_replace('data:image/'.$format.';base64,', '', $lst_base64[$i]);
                        $imageName = strtolower(str_replace(' ', '_', $model->tenmau.' '.time().$i.'.'.$format));
    
                        // lưu hình mới với tên cũ
                        if(isset($oldNameList[$i])) {
                            $imageName = $oldNameList[$i].'.'.$format;
    
                            // cập nhật tên hình bảng sản phẩm
                            SANPHAM::where('hinhanh', 'like', $oldNameList[$i].'%')->update(['hinhanh' => $imageName]);
                        }
    
                        $this->IndexController->saveImage('images/phone/'.$imageName, $base64);
        
                        $data = [
                            'id_msp' => $model->id,
                            'hinhanh' => $imageName,
                        ];
        
                        HINHANH::create($data);
                    } else {
                        break;
                    }
                }
            }

            $imageQty = count(HINHANH::where('id_msp', $id)->get());
            $model->imageQty = $imageQty;

            return [$model];
        }
    }

    public function destroy($id)
    {
        // xóa hình
        foreach(HINHANH::where('id_msp', $id)->get() as $key){
            unlink('images/phone/' . $key['hinhanh']);
        }
        HINHANH::where('id_msp', $id)->delete();
    }

    public function AjaxGetHinhAnh(Request $request)
    {
        if($request->ajax()){
            $imageList = HINHANH::where('id_msp', $request->id)->get();

            // vd: abc.jpg => abc.jpg?153634135364
            foreach($imageList as $i => $image) {
                $image->hinhanh .= '?'.time().$i;
            }

            return [
                'lst_image' => $imageList,
                'lst_model' => MAUSP::all(),
            ];
        }
    }

    public function AjaxGetModelHaveNotImage(Request $request)
    {
        if($request->ajax()){
            $lst_result = [];

            // danh sách mẫu sp chưa có hình ảnh
            foreach(MAUSP::all() as $model){
                $exists = MAUSP::find($model->id)->hinhanh;
                if(count($exists) === 0){
                    array_push($lst_result, $model);
                }
            }

            return $lst_result;
        }
    }

    public function AjaxSearch(Request $request)
    {
        if($request->ajax()){
            $keyword = $this->IndexController->unaccent($request->keyword);
            $lst_result = [];

            if($keyword == ''){
                $models = MAUSP::limit(10)->get();
                foreach($models as $model){
                    $imageQty = count(HINHANH::where('id_msp', $model->id)->get());
                    $model->imageQty = $imageQty;
                }
                return $models;
            }

            foreach(MAUSP::all() as $key){
                $imageQty = count(HINHANH::where('id_msp', $key->id)->get());
                $string = strtolower($this->IndexController->unaccent($key->tenmau.$imageQty.' Hình'));
                if(str_contains($string, $keyword)){
                    $imageQty = count(HINHANH::where('id_msp', $key->id)->get());
                    $key->imageQty = $imageQty;
                    array_push($lst_result, $key);
                }
            }
            return $lst_result;
        }
    }

    public function AjaxAddSingleFile(Request $request)
    {
        if($request->ajax()) {
            $model = MAUSP::find($request->id_msp);
            $base64String = $request->base64String;

            // định dạng hình
            $format = $this->IndexController->getImageFormat($base64String);

            $base64 = str_replace('data:image/'.$format.';base64,', '', $base64String);
            $imageName = strtolower(str_replace(' ', '_', $model->tenmau.' '.mt_rand(0, 100000).'.'.$format));

            // lưu hình
            $this->IndexController->saveImage('images/phone/'.$imageName, $base64);

            $data = [
                'id_msp' => $model->id,
                'hinhanh' => $imageName,
            ];

            HINHANH::create($data);

            // file cuối cùng  thì trả dữ liệu để render vào view
            if($request->lastItem === 'true') {
                $models = MAUSP::all();
                foreach($models as $val){
                    $imageQty = count(HINHANH::where('id_msp', $val->id)->get());
                    $val->imageQty = $imageQty;
                }   
    
                return [
                    'id' => $request->id_msp,
                    'data' => $models,
                ];
            } else {
                return ['status' => 'success'];
            }
        }
    }

    public function AjaxUpdateSingleFile(Request $request)
    {
        if($request->ajax()) {
            $id_msp = $request->id_msp;
            $model = MAUSP::find($id_msp);
            $base64String = $request->base64String;
            $index = $request->index;

            // định dạng hình
            $format = $this->IndexController->getImageFormat($base64String);

            $base64 = str_replace('data:image/'.$format.';base64,', '', $base64String);
            $imageName = strtolower(str_replace(' ', '_', $model->tenmau.' '.$index.mt_rand(1000, 9999).'.'.$format));

            // lưu hình
            $this->IndexController->saveImage('images/phone/'.$imageName, $base64);

            if($request->lastItem === 'true') {
                // mảng hình ảnh trong db hiện tại
                $imageNameCurrent = [];
                foreach(HINHANH::where('id_msp', $id_msp)->get() as $image) {
                    array_push($imageNameCurrent, $image->hinhanh);
                }

                // tất cả hình ảnh trong thư mục
                $allImage = scandir(public_path('images/phone'));

                // tên mẫu sp đặt theo tên hình
                $modelName = strtolower(str_replace(' ', '_', $model->tenmau));

                // hình ảnh của mẫu sp trong thư mục
                $imageOfModel = [];
                foreach($allImage as $image) {
                    if(str_contains($image, $modelName)) {
                        array_push($imageOfModel, $image);
                    }
                }

                // lọc ra những hình mới được thêm vào
                $newImage = [];
                foreach($imageOfModel as $image) {
                    // hình mới là những hình không có trong mảng hình cũ
                    if(!in_array($image, $imageNameCurrent)) {
                        array_push($newImage, $image);
                    }
                }

                // mảng các tên hình cũ
                $oldNameList = [];

                // xóa hình cũ
                if($request->arrayDelete){
                    foreach($request->arrayDelete as $nameDelete){
                        array_push($oldNameList, $nameDelete);
                        unlink('images/phone/' . $nameDelete);
                    }
                }

                foreach($newImage as $i => $image) {
                    // đổi tên hình mới thành tên hình cũ đã xóa
                    if(isset($oldNameList[$i])) {
                        // lấy định dạng của hình mới
                        $format = $this->splitNameAndFormat($image)['format'];
                        // tên cũ
                        $oldName = $this->splitNameAndFormat($oldNameList[$i])['name'];

                        $directory = 'images/phone/';

                        rename(
                            $directory.$image,
                            $directory.$oldName.$format
                        );

                        // cập nhật tên hình bảng hình ảnh
                        HINHANH::where('hinhanh', $oldNameList[$i])->update(['hinhanh' => $oldName.$format]);
                        // cập nhật tên hình bảng sản phẩm
                        SANPHAM::where('hinhanh', $oldNameList[$i])->update(['hinhanh' => $oldName.$format]);
                    }
                    // Lưu những hình mới vào db
                    else {
                        $data = [
                            'id_msp' => $id_msp,
                            'hinhanh' => $image,
                        ];
        
                        HINHANH::create($data);
                    }
                }

                $imageQty = count(HINHANH::where('id_msp', $id_msp)->get());
                $model->imageQty = $imageQty;

                return [$model];
            } else {
                return ['status' => 'success'];
            }
        }
    }

    public function splitNameAndFormat($imageName)
    {
        $data = [
            'name' => '',
            'format' => '',
        ];

        $array = explode('.', $imageName);

        $format = array_pop($array);

        $data['format'] = '.' . $format;
        $data['name'] = implode('', $array);

        return $data;
    }
}
