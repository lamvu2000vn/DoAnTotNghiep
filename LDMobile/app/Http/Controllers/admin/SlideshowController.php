<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;

use App\Models\SLIDESHOW;

class SlideshowController extends Controller
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
        if(!is_dir('images/slideshow')){
            // tạo thư mục lưu hình
            mkdir('images/slideshow', 0777, true);
        }
    }
    public function index()
    {
        $data = [
            'slideshow' => SLIDESHOW::all()
        ];

        return view($this->admin."slideshow")->with($data);
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            // định dạng hình
            $format = $this->IndexController->getImageFormat($request->hinhanh);

            $base64 = str_replace('data:image/'.$format.';base64,', '', $request->hinhanh);
            $imageName = 'slide'.time().'.'.$format;

            // lưu hình
            $this->IndexController->saveImage('images/slideshow/'.$imageName, $base64);

            $data = [
                'link' => $request->link,
                'hinhanh' => $imageName,
            ];

            $create = SLIDESHOW::create($data);

            return [
                'id' => $create->id,
                'data' => [$create],
            ];
        }
    }

    public function update(Request $request, $id)
    {
        if($request->ajax()){
            $oldData = SLIDESHOW::find($id);

            $imageName = $oldData->hinhanh;

            // nếu có thay đổi hình ảnh
            if($request->hinhanh){
                // xóa hình cũ
                unlink('images/slideshow/' . $oldData->hinhanh);

                // định dạng hình
                $format = $this->IndexController->getImageFormat($request->hinhanh);

                $base64 = str_replace('data:image/'.$format.';base64,', '', $request->hinhanh);
                $imageName = 'slide'.time().'.'.$format;

                // lưu hình
                $this->IndexController->saveImage('images/slideshow/'.$imageName, $base64);
            }
            
            $data = [
                'link' => $request->link,
                'hinhanh' => $imageName,
            ];

            SLIDESHOW::where('id', $id)->update($data);

            $newRow = SLIDESHOW::find($id);

            return [$newRow];
        }
    }

    public function destroy($id)
    {
        $slideshow = SLIDESHOW::find($id);
        // xóa hình
        unlink('images/slideshow/' . $slideshow->hinhanh);
        SLIDESHOW::destroy($id);
    }

    public function AjaxGetslideshow(Request $request)
    {
        if($request->ajax()){
            $slide = SLIDESHOW::find($request->id);
            $slide->hinhanh .= '?'.time();
            return $slide;
        }
    }
}
