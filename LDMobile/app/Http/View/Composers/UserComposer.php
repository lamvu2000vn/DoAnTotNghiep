<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\user\IndexController;

use App\Models\TAIKHOAN;
use App\Models\SANPHAM;
use App\Models\VOUCHER;
use App\Models\DONHANG;
use App\Models\CHINHANH;
use App\Models\TAIKHOAN_DIACHI;
use App\Models\THONGBAO;
use App\Models\DONHANG_DIACHI;
use App\Models\TAIKHOAN_VOUCHER;
use App\Models\GIOHANG;

class UserComposer
{
    public function __construct()
    {
        $this->IndexController = new IndexController;
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    public function compose(View $view)
    {
        if(session('user')){
            $id_tk = session('user')->id;
            
            $data = [
                'notSeen' => $this->getNotSeenNotification($id_tk),
                'processing' => $this->getProcessingOrder($id_tk),
                'cartQty' => $this->getCartQty($id_tk),
            ];
    
            $view->with('data', $data);
        }
    }

    // lấy số thông báo chưa đọc của tài khoản
    public function getNotSeenNotification($id_tk)
    {
        $notSeen = 0;

        if(count(TAIKHOAN::find($id_tk)->thongbao) == 0){
            return $notSeen;
        }

        foreach(TAIKHOAN::find($id_tk)->thongbao as $key){
            if($key['trangthaithongbao'] == 0){
                $notSeen++;
            }
        }

        return $notSeen;
    }

    // lấy số đơn hàng đang xử lý của tài khoản
    public function getProcessingOrder($id_tk)
    {
        $processing = 0;

        if(DONHANG::where('id_tk', $id_tk)->get()->count() === 0){
            return $processing;
        }

        // lấy đơn hàng của người dùng, sắp sếp ngày mua mới nhất
        $allOrderOfUser = DONHANG::where('id_tk', $id_tk)->orderBy('id', 'desc')->get();
        foreach($allOrderOfUser as $userOrder){
            // đơn hàng đang xử lý
            if($userOrder->trangthaidonhang !== 'Thành công' && $userOrder->trangthaidonhang !== 'Đã hủy') {
                $processing++;
            }
        }

        return $processing;
    }

    // lấy số lượng sản phẩm có trong giỏ hàng
    public function getCartQty($id_tk)
    {
        return GIOHANG::where('id_tk', $id_tk)->count();
    }
}