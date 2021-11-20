<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;

use App\Models\BANNER;

class BannerController extends Controller
{
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
        $data = [
            'banner' => BANNER::all(),
        ];
        return view($this->admin."banner")->with($data);
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            // định dạng hình
            $format = $this->IndexController->getImageFormat($request->hinhanh);
            
            $base64 = str_replace('data:image/'.$format.';base64,', '', $request->hinhanh);
            $imageName = 'banner'.time().'.'.$format;
            
            // lưu Hình
            $this->IndexController->saveImage('images/banner/'.$imageName, $base64);

            $data = [
                'link' => $request->link,
                'hinhanh' => $imageName,
            ];

            $create = BANNER::create($data);

            return [
                'id' => $create->id,
                'data' => [$create]
            ];
        }
    }

    public function update(Request $request, $id)
    {
        if($request->ajax()){
            $oldData = BANNER::find($id);

            $imageName = $oldData->hinhanh;

            // nếu có thay đổi hình ảnh
            if($request->hinhanh){
                // xóa hình cũ
                unlink('images/banner/' . $oldData->hinhanh);

                // định dạng hình
                $format = $this->IndexController->getImageFormat($request->hinhanh);

                $base64 = str_replace('data:image/'.$format.';base64,', '', $request->hinhanh);
                $imageName = 'banner'.time().'.'.$format;
                
                // lưu Hình
                $this->IndexController->saveImage('images/banner/'.$imageName, $base64);
            }

            $data = [
                'link' => $request->link,
                'hinhanh' => $imageName,
            ];
            
            BANNER::where('id', $id)->update($data);

            $newRow = BANNER::find($id);

            return [$newRow];
        }
    }

    public function destroy($id)
    {
        $banner = BANNER::find($id);
        // xóa hình
        unlink('images/banner/' . $banner->hinhanh);
        BANNER::destroy($id);
    }

    public function AjaxGetBanner(Request $request)
    {
        if($request->ajax()){
            $banner = BANNER::find($request->id);
            $banner->hinhanh .= '?'.time();
            
            return $banner;
        }
    }
}
