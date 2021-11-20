<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use App\Http\Controllers\user\UserController;
use Illuminate\Http\Request;
use App\Events\sendNotification;

use App\Models\DONHANG;
use App\Models\CTDH;
use App\Models\TAIKHOAN;
use App\Models\TAIKHOAN_DIACHI;
use App\Models\TAIKHOAN_VOUCHER;
use App\Models\CHINHANH;
use App\Models\VOUCHER;
use App\Models\MAUSP;
use App\Models\SANPHAM;
use App\Models\KHUYENMAI;
use App\Models\IMEI;
use App\Models\BAOHANH;
use App\Models\THONGBAO;
use App\Models\KHO;
use App\Models\TINHTHANH;
use App\Models\DONHANG_DIACHI;
use App\Http\Controllers\PushNotificationController;

class DonHangController extends Controller
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
        $this->UserController = new UserController;
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    public function index()
    {
        $lst_order = DONHANG::orderBy('id', 'desc')->limit(10)->get();
        foreach($lst_order as $i => $key){
            $lst_order[$i]->taikhoan = TAIKHOAN::find($lst_order[$i]->id_tk);
        }

        $data = [
            'lst_order' => $lst_order,
        ];

        return view($this->admin."don-hang")->with($data);
    }


    public function AjaxGetDonHang(Request $request)
    {
        if($request->ajax()){
            // đơn hàng
            $order = DONHANG::find($request->id);

            // chi tiết đơn hàng
            $order->ctdh = DONHANG::find($request->id)->ctdh;
            foreach($order->ctdh as $i => $key){
                $order->ctdh[$i]->sanpham = SANPHAM::find($key->pivot->id_sp);
            }

            // tài khoản
            $order->taikhoan = TAIKHOAN::find($order->id_tk);

            // giao hàng tận nơi
            if($order->hinhthuc == 'Giao hàng tận nơi'){
                // địa chỉ tài khoản
                $order->taikhoan_diachi = DONHANG_DIACHI::find($order->id_dh_dc);
            }
            // nhận tại cửa hàng
            else {
                // chi nhánh
                $order->chinhanh = CHINHANH::find($order->id_cn);
            }

            // voucher
            if($order->id_vc){
                $order->voucher = VOUCHER::find($order->id_vc);
            }

            return $order;
        }
    }

    public function destroy($id)
    {
        // hủy đơn hàng
        DONHANG::where('id', $id)->update(['trangthaidonhang' => 'Đã hủy']);

        $order = DONHANG::find($id);
        $id_tk = $order->id_tk;

        // khôi phục voucher đã áp dụng
        if($order->id_vc){
            $this->UserController->restoreTheAppliedVoucher($order->id_vc, $id_tk);
        }

        // hoàn lại số lượng kho
        $this->UserController->refundOfInventory($id);

        $notification = [
            'user' => TAIKHOAN::find($id_tk),
            'type' => 'order',
            'orderStatus' => 'cancelled',
            'id_dh' => $id,
        ];
        
        event(new sendNotification($notification));

        //push notication to app
        $user = TAIKHOAN::find($order->id_tk);
        if(!empty($user->device_token))
        (new PushNotificationController)->sendPush($user->device_token, "Đơn hàng", "Đơn hàng #". $request->id ."của bạn đã bị hủy");

        return $notification;
    }

    // xác nhận đơn hàng
    public function AjaxOrderConfirmation(Request $request)
    {
        if($request->ajax()){
            DONHANG::where('id', $request->id)->update(['trangthaidonhang' => 'Đã xác nhận']);
            $order = DONHANG::find($request->id);
            // gửi thông báo
            $data = [
                'id_tk' => $order->id_tk,
                'tieude' => 'Đơn đã xác nhận',
                'noidung' => "Đã xác nhận đơn hàng <b>#$order->id</b> của bạn.",
                'thoigian' => date('d/m/Y h:i'),
                'trangthaithongbao' => 0,
            ];

            THONGBAO::create($data);

            $notification = [
                'user' => TAIKHOAN::find($order->id_tk),
                'type' => 'order',
                'orderStatus' => 'confirmed',
                'id_dh' => $order->id,
            ];
            
            event(new sendNotification($notification));

            //push notication to app
            $user = TAIKHOAN::find($order->id_tk);
            if(!empty($user->device_token))
            (new PushNotificationController)->sendPush($user->device_token, "Đơn hàng", "Đơn hàng #". $request->id." đã được xác nhận");
        }
    }

    // đơn hàng thành công
    public function AjaxSuccessfulOrder(Request $request)
    {
        if($request->ajax()){
            // cập nhật đơn hàng thành công
            DONHANG::where('id', $request->id)->update(['trangthaidonhang' => 'Thành công']);

            // đơn hàng
            $order = DONHANG::find($request->id);

            // kích hoạt imei & thêm bảo hành
            foreach($order->ctdh as $key){
                // kích hoạt và thêm theo theo số lượng sản phẩm trong đơn hàng
                for($i = 0; $i < $key->pivot->sl; $i++){
                    $imei = IMEI::where('id_sp', $key->pivot->id_sp)->where('trangthai', 0)->first();

                    // kích hoạt iemi
                    IMEI::where('id', $imei->id)->update(['trangthai' => 1]);

                    // ngày giao hàng thành công
                    $start = date('d/m/Y');

                    // timestamp ngày giao hàng thành công
                    $startTimestamp = strtotime(date('d-m-Y'));

                    // tháng bảo hành
                    $month = explode(' ', MAUSP::find(SANPHAM::find($imei->id_sp)->id_msp)->baohanh)[0];
                    // nếu có bảo hành
                    if($month){
                        $end = date('d/m/Y', strtotime('+'.$month.' months', $startTimestamp));
                    }
                    // không có bảo hành
                    else {
                        $end = $start;
                    }

                    $data = [
                        'id_imei' => $imei->id,
                        'imei' => $imei->imei,
                        'ngaymua' => $start,
                        'ngayketthuc' => $end,
                    ];

                    BAOHANH::create($data);
                }
            }

            // gửi thông báo thành công & tặng voucher
            $voucher = VOUCHER::where('code', 'GIAM10')->first();

            THONGBAO::create([
                'id_tk' => $order->id_tk,
                'tieude' => 'Giao hàng thành công',
                'noidung' => "Kiện hàng của đơn hàng <b>#$order->id</b> đã giao thành công đến bạn.",
                'thoigian' => date('d/m/Y h:i'),
                'trangthaithongbao' => 0,
            ]);
            THONGBAO::create([
                'id_tk' => $order->id_tk,
                'tieude' => 'Mã giảm giá',
                'noidung' => 'Cảm ơn bạn đã mua hàng tại LDMobile, chúng tôi xin gửi tặng bạn mã giảm giá giảm '.$voucher->chietkhau*100 .'% cho đơn hàng từ '.number_format($voucher->dieukien, 0, '', '.').'<sup>đ</sup>. Áp dụng đến hết ngày '.$voucher->ngayketthuc.'.',
                'thoigian' => date('d/m/Y h:i'),
                'trangthaithongbao' => 0,
            ]);

            // tặng voucher
            $userVoucher = TAIKHOAN_VOUCHER::where('id_vc', $voucher->id)->where('id_tk', $order->id_tk)->first();
            // nếu chưa có voucher
            if(!$userVoucher){
                TAIKHOAN_VOUCHER::create([
                    'id_vc' => $voucher->id,
                    'id_tk' => $order->id_tk,
                    'sl' => 1,
                ]);
            }
            // cập nhật số lượng voucher
            else {
                $qty = $userVoucher->sl;
                $userVoucher->sl = ++$qty;
                $userVoucher->save();
            }
            
            // giảm số lượng voucher
            $qty = $voucher->sl;
            $voucher->sl = --$qty;
            $voucher->save();

            $notification = [
                'user' => TAIKHOAN::find($order->id_tk),
                'type' => 'order',
                'orderStatus' => 'success',
                'id_dh' => $order->id,
            ];

            event(new sendNotification($notification));

            //push notication to app
            $user = TAIKHOAN::find($order->id_tk);
            if(!empty($user->device_token))
            (new PushNotificationController)->sendPush($user->device_token, "Đơn hàng", "Đơn hàng #". $request->id ." đã giao thành công. Cảm ơn bạn đã mua hàng tại LDMobile, chúng tôi xin gửi tặng bạn mã giảm giá...");
        }
    }

    public function AjaxSearch(Request $request)
    {
        if($request->ajax()){
            $keyword = $this->IndexController->unaccent($request->keyword);
            $lst_result = [];

            if($keyword == ''){
                $lst_order = DONHANG::orderBy('id', 'desc')->limit(10)->get();
                foreach($lst_order as $key){
                    $fullname = TAIKHOAN::find($key->id_tk)->hoten;
                    $key->fullname = $fullname;
                }

                return $lst_order;
            }

            foreach(DONHANG::orderBy('id', 'desc')->get() as $key){
                $fullname = TAIKHOAN::find($key->id_tk)->hoten;
                $data = strtolower($this->IndexController->unaccent($key->id.$key->thoigian.$fullname.$key->pttt.$key->hinhthuc.$key->tongtien.$key->trangthaidonhang));
                if(str_contains($data, $keyword)){
                    $key->fullname = $fullname;
                    array_push($lst_result, $key);
                }
            }

            return $lst_result;
        }
    }

    public function AjaxFilterSort(Request $request)
    {
        if($request->ajax()){
            $arrFilterSort = $request->arrFilterSort;
            $lst_temp = [];
            $lst_result = [];
            $lst_search = [];
            $keyword = $this->IndexController->unaccent($request->keyword);
            $html = '';

            // danh sách tìm kiếm
            if($keyword){
                $lst_search = $this->search($keyword);
            }

            // gỡ tất cả bỏ lọc | không có bộ lọc & có sắp xếp
            if(!key_exists('filter', $arrFilterSort)){
                $sort = $arrFilterSort['sort'];

                // Không có tìm kiếm
                if(empty($lst_search)){
                    if($sort == 'date-desc'){
                        foreach(DONHANG::orderBy('id', 'desc')->limit(10)->get() as $key){
                            array_push($lst_result, $key);
                        }
                    } elseif($sort == 'date-asc'){
                        foreach(DONHANG::orderBy('id')->limit(10)->get() as $key){
                            array_push($lst_result, $key);
                        }
                    } elseif($sort == 'total-asc'){
                        foreach(DONHANG::orderBy('tongtien')->limit(10)->get() as $key){
                            array_push($lst_result, $key);
                        }
                    } elseif($sort == 'total-desc'){
                        foreach(DONHANG::orderBy('tongtien', 'desc')->limit(10)->get() as $key){
                            array_push($lst_result, $key);
                        }
                    }

                    foreach($lst_result as $key){
                        $key->fullname = TAIKHOAN::find($key->id_tk)->hoten;
                    }

                    return $lst_result;
                } else {
                    if($sort == '' || $sort == 'date-desc'){
                        $lst_result = $this->sortDate($lst_search, 'desc');
                    } elseif($sort == 'date-asc'){
                        $lst_result = $this->sortDate($lst_search);
                    } elseif($sort == 'total-asc'){
                        $lst_result = $this->sortTotal($lst_search);
                    } elseif($sort == 'total-desc'){
                        $lst_result = $this->sortTotal($lst_search, 'desc');
                    }

                    foreach($lst_result as $key){
                        $key->fullname = TAIKHOAN::find($key->id_tk)->hoten;
                    }
                }

                return $lst_result;
            }

            $arrFilter = $arrFilterSort['filter'];

            // lọc tiêu chí đầu tiên trên danh sách tìm kiếm
            if(!empty($lst_search)){
                if(array_key_first($arrFilter) == 'paymentMethod'){
                    foreach($arrFilter['paymentMethod'] as $paymentMethod){
                        foreach($lst_search as $key){
                            if($key->pttt == $paymentMethod){
                                array_push($lst_temp, $key);
                            }
                        }
                    }
                } elseif(array_key_first($arrFilter) == 'receiveMethod'){
                    foreach($arrFilter['receiveMethod'] as $receiveMethod){
                        foreach($lst_search as $key){
                            if($key->hinhthuc == $receiveMethod){
                                array_push($lst_temp, $key);
                            }
                        }
                    }
                } elseif(array_key_first($arrFilter) == 'status'){
                    foreach($arrFilter['status'] as $status){
                        foreach($lst_search as $key){
                            if($key->trangthaidonhang == $status){
                                array_push($lst_temp, $key);
                            }
                        }
                    }
                }
            } else {
                if(array_key_first($arrFilter) == 'paymentMethod'){
                    foreach($arrFilter['paymentMethod'] as $paymentMethod){
                        foreach(DONHANG::orderBy('id', 'desc')->get() as $key){
                            if($key->pttt == $paymentMethod){
                                array_push($lst_temp, $key);
                            }
                        }
                    }
                } elseif(array_key_first($arrFilter) == 'receiveMethod'){
                    foreach($arrFilter['receiveMethod'] as $receiveMethod){
                        foreach(DONHANG::orderBy('id', 'desc')->get() as $key){
                            if($key->hinhthuc == $receiveMethod){
                                array_push($lst_temp, $key);
                            }
                        }
                    }
                } elseif(array_key_first($arrFilter) == 'status'){
                    foreach($arrFilter['status'] as $status){
                        foreach(DONHANG::orderBy('id', 'desc')->get() as $key){
                            if($key->trangthaidonhang == $status){
                                array_push($lst_temp, $key);
                            }
                        }
                    }
                }
            }

            // chỉ có 1 tiêu chí lọc
            if(count($arrFilter) == 1){
                $sort = $arrFilterSort['sort'];
                
                if($sort == 'date-desc'){
                    $lst_result = $this->sortDate($lst_temp, 'desc');
                } elseif($sort == 'date-asc'){
                    $lst_result = $this->sortDate($lst_temp);
                } elseif($sort == 'total-asc'){
                    $lst_result = $this->sortTotal($lst_temp);
                } elseif($sort == 'total-desc'){
                    $lst_result = $this->sortTotal($lst_temp, 'desc');
                }

                foreach($lst_result as $key){
                    $key->fullname = TAIKHOAN::find($key->id_tk)->hoten;
                }

                return $lst_result;
            }

            // tiếp tục lọc các tiêu chí khác
            array_push($lst_result, $lst_temp);

            for($i = 1; $i < count($arrFilter); $i++){
                $lst_temp = [];

                if(array_keys($arrFilter)[$i] == 'paymentMethod'){
                    foreach($arrFilter['paymentMethod'] as $paymentMethod){
                        foreach($lst_result[$i - 1] as $key){
                            if($key->pttt == $paymentMethod){
                                array_push($lst_temp, $key);
                            }
                        }
                    }
                    array_push($lst_result, $lst_temp);
                } elseif(array_keys($arrFilter)[$i] == 'receiveMethod'){
                    foreach($arrFilter['receiveMethod'] as $receiveMethod){
                        foreach($lst_result[$i - 1] as $key){
                            if($key->hinhthuc == $receiveMethod){
                                array_push($lst_temp, $key);
                            }
                        }
                    }
                    array_push($lst_result, $lst_temp);
                } elseif(array_keys($arrFilter)[$i] == 'status'){
                    foreach($arrFilter['status'] as $status){
                        foreach($lst_result[$i - 1] as $key){
                            if($key->trangthaidonhang == $status){
                                array_push($lst_temp, $key);
                            }
                        }
                    }
                    array_push($lst_result, $lst_temp);
                }
            }

            // lấy danh sách kết quả cuối cùng
            $lst_result = $lst_result[count($lst_result) - 1];

            $sort = $arrFilterSort['sort'];

            if($sort == 'date-desc'){
                $lst_result = $this->sortDate($lst_temp, 'desc');
            } elseif($sort == 'date-asc'){
                $lst_result = $this->sortDate($lst_temp);
            } elseif($sort == 'total-asc'){
                $lst_result = $this->sortTotal($lst_temp);
            } elseif($sort == 'total-desc'){
                $lst_result = $this->sortTotal($lst_temp, 'desc');
            }

            foreach($lst_result as $key){
                $key->fullname = TAIKHOAN::find($key->id_tk)->hoten;
            }

            return $lst_result;
        }
    }

    public function search($keyword)
    {
        $lst_result = [];
        foreach(DONHANG::orderBy('id', 'desc')->get() as $key){
            $fullname = TAIKHOAN::find($key->id_tk)->hoten;
            $data = strtolower($this->IndexController->unaccent($key->id.$key->thoigian.$fullname.$key->pttt.$key->hinhthuc.$key->tongtien.$key->trangthaidonhang));
            if(str_contains($data, $keyword)){
                array_push($lst_result, $key);
            }
        }
        return $lst_result;
    }

    // sắp xếp ngày
    public function sortDate($lst, $sort = 'asc')
    {
        if($sort == 'asc'){
            for($i = 0; $i < count($lst) - 1; $i++){
                for($j = $i + 1; $j < count($lst); $j++){
                    $timestamp_i = strtotime(str_replace('/', '-', $lst[$i]->thoigian));
                    $timestamp_j = strtotime(str_replace('/', '-', $lst[$j]->thoigian));
                    if($timestamp_i >= $timestamp_j){
                        $temp = $lst[$i];
                        $lst[$i] = $lst[$j];
                        $lst[$j] = $temp;
                    }
                }
            }
        } else {
            for($i = 0; $i < count($lst) - 1; $i++){
                for($j = $i + 1; $j < count($lst); $j++){
                    $timestamp_i = strtotime(str_replace('/', '-', $lst[$i]->thoigian));
                    $timestamp_j = strtotime(str_replace('/', '-', $lst[$j]->thoigian));
                    if($timestamp_i <= $timestamp_j){
                        $temp = $lst[$i];
                        $lst[$i] = $lst[$j];
                        $lst[$j] = $temp;
                    }
                }
            }
        }
        return $lst;
    }

    // sắp xếp tổng tiền
    public function sortTotal($lst, $sort = 'asc')
    {
        if($sort == 'asc'){
            for($i = 0; $i < count($lst) - 1; $i++){
                for($j = $i + 1; $j < count($lst); $j++){
                    if($lst[$i]->tongtien >= $lst[$j]->tongtien){
                        $temp = $lst[$i];
                        $lst[$i] = $lst[$j];
                        $lst[$j] = $temp;
                    }
                }
            }
        } else {
            for($i = 0; $i < count($lst) - 1; $i++){
                for($j = $i + 1; $j < count($lst); $j++){
                    if($lst[$i]->tongtien <= $lst[$j]->tongtien){
                        $temp = $lst[$i];
                        $lst[$i] = $lst[$j];
                        $lst[$j] = $temp;
                    }
                }
            }
        }
        return $lst;
    }
}
