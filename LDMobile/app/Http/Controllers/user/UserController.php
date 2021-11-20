<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Session;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\user\IndexController;
use Illuminate\Support\Facades\Cookie;
use App\Events\sendNotification;

use App\Models\TAIKHOAN;
use App\Models\TAIKHOAN_DIACHI;
use App\Models\THONGBAO;
use App\Models\LUOTTHICH;
use App\Models\SP_YEUTHICH;
use App\Models\DANHGIASP;
use App\Models\GIOHANG;
use App\Models\VOUCHER;
use App\Models\DONHANG;
use App\Models\TAIKHOAN_VOUCHER;
use App\Models\CTDG;
use App\Models\PHANHOI;
use App\Models\CTDH;
use App\Models\KHO;
use App\Models\CHINHANH;
use App\Models\TINHTHANH;
use App\Models\SANPHAM;
use App\Models\DONHANG_DIACHI;
use App\Models\HANGDOI;
use App\Http\Controllers\PushNotificationController;

class UserController extends Controller
{
    public function __construct()
    {
        $this->viewprefix='user.pages.';
        $this->user='user/content/';
        $this->IndexController = new IndexController;
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        // chưa có thư mục lưu hình
        if(!is_dir('images/evaluate')){
            // tạo thư mục lưu hình
            mkdir('images/evaluate', 0777, true);
        }
    }
    /*============================================================================================================
                                                        Auth
    ==============================================================================================================*/
    public function DangNhap(){
        if(Auth::check() || session('user')){
            return back()->with('toast_message', 'Bạn đã đăng nhập');
        }
        // $this->IndexController->print(Session::all()); return false;
        // url trước đó
        if(!Session::get('prev_url')){
            $prev_url = '/';
            if(Session::get('_previous')){
                $url = Session::get('_previous')['url'];
                $arrUrl = explode('/', $url);
                $page = $arrUrl[count($arrUrl) - 1];
                if($page != 'dangky' && $page != 'khoiphuctaikhoan'){
                    $prev_url = $url;
                }
            }
            Session::put('prev_url', $prev_url);
        }

        return view($this->user."dang-nhap");
    }

    public function DangKy(){
        if(Auth::check()){
            return back()->with('toast_message', 'Bạn đã đăng nhập');
        }
        return view($this->user."dang-ky");
    }

    public function KhoiPhucTaiKhoan()
    {
        // đang đăng nhập
        if(session('user')){
            return back();
        }

        return view($this->user."khoi-phuc-tai-khoan");
    }

    public function SignUp(Request $request)
    {
        $data = [
            'sdt' => $request->su_tel,
            'password' => Hash::make($request->su_pw),
            'hoten' => $request->su_fullname,
            'anhdaidien' => 'avatar-default.png',
            'loaitk' => 0,
            'htdn' => 'normal',
            'login_status' => 0,
            'thoigian' => date('d/m/Y'),
            'trangthai' => 1,
        ];

        $newUser = TAIKHOAN::create($data);

        return redirect('dangnhap')->with('success_message', 'Đăng ký tài khoản thành công!');
    }

    public function Login(Request $request)
    {
        $data = [
            'sdt' => $request->login_tel,
            'password' => $request->login_pw,
            'trangthai' => 1,
        ];
        
        if(Auth::attempt($data, $request->remember)){
            Session::regenerate();

            $user = TAIKHOAN::where('sdt', $data['sdt'])->first();

            /**
             * nếu tài khoản đã đăng nhập trước đó ở 1 trình duyệt khác
             * thì đăng xuất tài khoản ở trình duyệt đó
             */
            if($user->login_status == 1) {
                $notification = [
                    'user' => $user,
                    'type' => 'logout',
                ];

                event(new sendNotification($notification));
            }
            // đăng nhập bình thường
            else {
                $user->login_status = 1;
                $user->save();
            }

            session(['user' => $user]);

            // quay về url trước đó
            $prev_url = session('prev_url');
            if($prev_url){
                Session::forget('prev_url');
                return redirect($prev_url)->with('toast_message', 'Đăng nhập thành công');
            }

            return redirect('/')->with('toast_message', 'Đăng nhập thành công');
        }

        return back()->with('error_message', 'số điện thoại hoặc mật khẩu không chính xác');
    }

    public function RecoverAccount(Request $request)
    {
        $user = TAIKHOAN::where('sdt', $request->forget_tel)->first();
        $hashPW = Hash::make($request->forget_pw);
        $user->password = $hashPW;
        $user->save();

        return redirect('dangnhap')->with('success_message', 'Khôi phục tài khoản thành công!');
    }

    public function FacebookRedirect()
    {
        return Socialite::driver('facebook')
            ->scopes(['email', 'public_profile'])
            ->redirect();
    }

    public function FacebookCallback()
    {
        try{
            $user = Socialite::driver('facebook')->stateless()->user();

            if($user->email == ''){
                return redirect('dangnhap')->with('error_message', 'Cần quyền truy cập email của bạn để tiếp tục');
            }

            $exists = TAIKHOAN::where('email', $user->email)->first();

            // đã tồn tại
            if($exists){
                // đã đăng nhập bằng facebook
                if($exists->htdn == 'facebook'){
                    /**
                     * nếu tài khoản đã đăng nhập trước đó ở 1 trình duyệt khác
                     * thì đăng xuất tài khoản ở trình duyệt đó
                     */
                    if($exists->login_status == 1) {
                        $notification = [
                            'user' => $exists,
                            'type' => 'logout',
                        ];

                        event(new sendNotification($notification));
                    }

                    $exists->update([
                        'anhdaidien' => $user->avatar_original . "&access_token={$user->token}",
                        'user_social_token' => $user->token,
                        'login_status' => 1
                    ]);
                    
                    Auth::login($exists, true);
                    session(['user' => $exists]);
                    Cookie::queue('acccount_social_id', $exists->id, 60*24*30*12);

                    // quay về url trước đó
                    $prev_url = session('prev_url');
                    if($prev_url){
                        Session::forget('prev_url');
                        return redirect($prev_url)->with('toast_message', 'Đăng nhập thành công');
                    }

                    return redirect('/')->with('toast_message', 'Đăng nhập thành công');
                }

                return redirect('dangnhap')->with('error_message', 'Email này đã được sử dụng');
            } else {
                // tạo tài khoản mới
                $newUser = TAIKHOAN::create([
                    'email' => $user->email,
                    'hoten' => $user->name,
                    'anhdaidien' => $user->avatar_original . "&access_token={$user->token}",
                    'loaitk' => 0,
                    'htdn' => 'facebook',
                    'user_social_token' => $user->token,
                    'login_status' => 1,
                    'thoigian' => date('d/m/Y'),
                    'trangthai' => 1,
                ]);
                
                Auth::login($newUser, true);
                session(['user' => $newUser]);
                Cookie::queue('acccount_social_id', 'facebook_'.$newUser->id, 60*24*30*12);

                // quay về url trước đó
                $prev_url = session('prev_url');
                if($prev_url){
                    Session::forget('prev_url');
                    return redirect($prev_url)->with('toast_message', 'Đăng nhập thành công');
                }

                return redirect('/')->with('toast_message', 'Đăng nhập thành công');
            }
        } catch(Exception $e){
            return redirect('dangnhap')->with('error_message', 'Đã có lỗi xảy ra. Vui lòng thử lại');
            $this->IndexController->print($e->getMessage());
        }
    }

    public function GoogleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function GoogleCallback()
    {
        try {      
            $user = Socialite::driver('google')->user();
       
            $exists = TAIKHOAN::where('email', $user->email)->first();
       
            if($exists){
                if($exists->htdn == 'google'){
                    /**
                     * nếu tài khoản đã đăng nhập trước đó ở 1 trình duyệt khác
                     * thì đăng xuất tài khoản ở trình duyệt đó
                     */
                    if($exists->login_status == 1) {
                        $notification = [
                            'user' => $exists,
                            'type' => 'logout',
                        ];

                        event(new sendNotification($notification));
                    }
                    
                    $exists->update(['login_status' => 1]);

                    Auth::login($exists, true);
                    session(['user' => $exists]);
                    Cookie::queue('acccount_social_id', $exists->id, 60*24*30*12);

                    // quay về url trước đó
                    $prev_url = session('prev_url');
                    if($prev_url){
                        Session::forget('prev_url');
                        return redirect($prev_url)->with('toast_message', 'Đăng nhập thành công');
                    }

                    return redirect('/')->with('toast_message', 'Đăng nhập thành công');
                }

                return redirect('dangnhap')->with('error_message', 'Email này đã được sử dụng');
            }

            $newUser = TAIKHOAN::create([
                'email' => $user->email,
                'hoten' => $user->name,
                'anhdaidien' => $user->avatar,
                'loaitk' => 0,
                'htdn' => 'google',
                'user_social_token' => $user->token,
                'login_status' => 1,
                'thoigian' => date('d/m/Y'),
                'trangthai' => 1,
            ]);

            Auth::login($newUser, true);
            Session::regenerate();
            session(['user' => $newUser]);
            Cookie::queue('acccount_social_id', $newUser->id, 60*24*30*12);

            // quay về url trước đó
            $prev_url = session('prev_url');
            if($prev_url){
                Session::forget('prev_url');
                return redirect($prev_url)->with('toast_message', 'Đăng nhập thành công');
            }
    
            return redirect('/')->with('toast_message', 'Đăng nhập thành công');
        } catch (Exception $e) {
            return back()->with('error_message', 'Đã có lỗi xảy ra. Vui lòng thử lại');
        }
    }

    public function LogOut(Request $request)
    {
        $user = session('user');
        // đang không đăng nhập
        if(!$user){
            return back();
        }

        // nếu đang thanh toán thì xóa hàng đợi
        $isQueue = HANGDOI::where('id_tk', $user->id)->first();
        if($isQueue) {
            $this->IndexController->removeQueue($isQueue->id);
        }

        Auth::logout();

        // cập nhật login_status = 0 nếu k có request ?login_status=1
        if(!$request->login_status) {
            TAIKHOAN::where('id', $user->id)->update(['login_status' => 0]);
        }

        if($user->htdn !== 'normal'){
            Cookie::queue(Cookie::forget('acccount_social_id'));
        }
        
        Session::flush();
        Session::put('visitor', '1');
        Session::flash('toast_message', 'Đã đăng xuất');
        return redirect('/');
    }

    public function AjaxPhoneNumberIsExists(Request $request)
    {
        if($request->ajax()){
            return TAIKHOAN::where('sdt', $request->sdt)->first() ? 'exists' : 'valid';
        }
    }

    /*============================================================================================================
                                                        Page
    ==============================================================================================================*/

    public function TaiKhoan(){
        $addressDefault = $this->IndexController->getAddressDefault(session('user')->id);

        $array = [
            'page' => 'sec-tai-khoan',
            'addressDefault' => $addressDefault,
        ];
        return view($this->user."tai-khoan")->with($array);
    }

    public function ThongBao()
    {
        // danh sách thông báo
        $user = session('user');
        $lst_noti = THONGBAO::where('id_tk', $user->id)->orderBy('id', 'desc')->limit(10)->get();

        $array = [
            'page' => 'sec-thong-bao',
            'lst_noti' => $lst_noti
        ];
        return view($this->user."tai-khoan")->with($array);
    }

    public function DonHang()
    {
        $processing = [];
        $complete = [];

        $user = session('user');

        $allOrderOfUser = DONHANG::where('id_tk', $user->id)->orderBy('id', 'desc')->get();
        foreach($allOrderOfUser as $userOrder){
            $order = [
                'order' => $userOrder,
                'detail' => $this->IndexController->getOrderDetail($userOrder->id)
            ];

            // đơn hàng đang xử lý
            if($userOrder['trangthaidonhang'] !== 'Thành công' && $userOrder['trangthaidonhang'] !== 'Đã hủy') {
                array_push($processing, $order);
            } else {
                array_push($complete, $order);
            }
        }

        $array = [
            'page' => 'sec-don-hang',
            'processing' => $processing,
            'complete' => $complete
        ];

        return view ($this->user."tai-khoan")->with($array);
    }

    public function DiaChi()
    {
        $addressList = [
            'status' => false,
            'default' => [],
            'another' => []
        ];

        $user = session('user');

        $allAddress = TAIKHOAN::find($user->id)->taikhoan_diachi;

        if($allAddress->count()) {
            $addressList['status'] = true;

            foreach($allAddress as $address) {
                if($address->macdinh === 1) {
                    $addressList['default'] = $address;
                } else {
                    array_push($addressList['another'], $address);
                }
            }
        }
        
        $array = [
            'page' => 'sec-dia-chi',
            'addressList' => $addressList,
        ];

        return view($this->user."tai-khoan")->with($array);
    }

    public function ChiTietDonHang($id){
        $data = [
            'order' => $this->IndexController->getOrderById($id),
            'page' => 'sec-chi-tiet-don-hang',
        ];

        return view ($this->user."tai-khoan")->with($data);
    }

    public function YeuThich()
    {
        $favoriteList = [];

        $user = session('user');

        foreach(TAIKHOAN::find($user->id)->sp_yeuthich as $key){
            $id = $key->pivot->id;

            $id_sp_list = $this->IndexController->getListIdSameCapacity($key->pivot->id_sp);
            $product = $this->IndexController->getProductById($key->pivot->id_sp);
            
            array_push($favoriteList, [
                'id' => $id,
                'sanpham' => $product
            ]);
        }

        $array = [
            'page' => 'sec-yeu-thich',
            'favoriteList' => $favoriteList
        ];

        return view ($this->user."tai-khoan")->with($array);
    }

    public function Voucher()
    {
        $user = session('user');

        $voucherList = TAIKHOAN_VOUCHER::where('id_tk', $user->id)->get();

        foreach($voucherList as $i => $key){
            $voucher = VOUCHER::find($key->id_vc);
            $voucherList[$i]->voucher = $voucher;

            // ngày kết thúc
            $end = strtotime(str_replace('/', '-', $voucher->ngayketthuc));
            // ngày hiện tại
            $current = strtotime(date('d-m-Y'));

            if($end < $current){
                $voucherList[$i]->trangthai = false;
            } else {
                $voucherList[$i]->trangthai = true;
            }
        }

        $array = [
            'page' => 'sec-voucher',
            'voucherList' => $voucherList,
        ];

        return view ($this->user."tai-khoan")->with($array);
    }

    public function DiaChiGiaoHang(Request $request)
    {
        // bắt buộc request phải từ trang thanh toán
        if(Session::get('_previous')){
            $url = Session::get('_previous')['url'];
            $arrUrl = explode('/', $url);
            $page = $arrUrl[count($arrUrl) - 1];

            // mảng các trang cho phép truy cập | redirect
            $lst_allowPage = [
                'diachigiaohang',
                'thanhtoan',
                'create-update-address'
            ];

            // các trang không nằm trong mảng cho phép
            if(!in_array($page, $lst_allowPage)){
                return back();
            }
        } else {
            return redirect('/');
        }

        $addressList = [
            'status' => false,
            'default' => [],
            'another' => []
        ];

        $user = session('user');

        $allAddress = TAIKHOAN::find($user->id)->taikhoan_diachi;

        if($allAddress->count()) {
            $addressList['status'] = true;

            foreach($allAddress as $address) {
                if($address->macdinh === 1) {
                    $addressList['default'] = $address;
                } else {
                    array_push($addressList['another'], $address);
                }
            }
        }

        $data = [
            'addressList' => $addressList,
        ];

        return view($this->user."dia-chi-giao-hang")->with($data);
    }

    /*============================================================================================================
                                                    Submit
    ==============================================================================================================*/
    
    public function ChangeAddressDelivery(Request $request)
    {
        TAIKHOAN_DIACHI::where('macdinh', 1)->update(['macdinh' => 0]);
        TAIKHOAN_DIACHI::where('id', $request->address_id)->update(['macdinh' => 1]);

        return redirect('thanhtoan')->with('toast_message', 'Đã thay đổi địa chỉ giao hàng');
    }

    public function AjaxCreateUpdateAddress(Request $request)
    {
        if($request->ajax()) {
            $response = [
                'message' => ''
            ];

            $data = [
                'hoten' => $request->hoten,
                'diachi' => $request->diachi,
                'phuongxa' => $request->phuongxa,
                'quanhuyen' => $request->quanhuyen,
                'tinhthanh' => $request->tinhthanh,
                'sdt' => $request->sdt,
                'macdinh' => $request->macdinh,
            ];

            $type = $request->type;
    
            if($type === 'create') {
                $data['id_tk'] = session('user')->id;

                // không đặt làm mặc định
                if(!$data['macdinh']){
                    // nếu người dùng chưa có địa chỉ mặc định nào thì tự động chọn làm mặc định
                    if(!TAIKHOAN_DIACHI::where('id_tk', session('user')->id)->where('macdinh' , 1)->first()){
                        $data['macdinh'] = 1;
                    }
                }
                // chọn địa chỉ làm mặc định
                else {
                    // cập nhật các địa chỉ khác trạng thái = 0
                    TAIKHOAN_DIACHI::where('id_tk', session('user')->id)->where('macdinh', 1)->update(['macdinh' => 0]);
                }
    
                TAIKHOAN_DIACHI::create($data);

                $response['message'] = 'Tạo địa chỉ thành công';
            } else {
                // không đặt làm mặc định
                if(!$data['macdinh']){
                    // tự động chọn mặc định khi chưa có địa chỉ mặc định
                    $defaultAddress = TAIKHOAN_DIACHI::where('id_tk', session('user')->id)->where('macdinh' , 1)->first();
                    // không có địa chỉ mặc định
                    if(!$defaultAddress) {
                        $data['macdinh'] = 1;
                    }
                    // địa chỉ đang chỉnh sửa là mặc định thì sẽ vẫn là địa chỉ mặc định
                    elseif ($defaultAddress->id == $request->tk_dc_id) {
                        $data['macdinh'] = 1;
                    }
                }
                else {
                    $address = TAIKHOAN_DIACHI::where('id_tk', session('user')->id)->where('macdinh' , 1)->first();
                    if($address){
                        $address->macdinh = 0;
                        $address->save();
                    }
                }
    
                TAIKHOAN_DIACHI::where('id', $request->tk_dc_id)->update($data);
    
                $response['message'] = 'Chỉnh sửa địa chỉ thành công';
            }

            return $response;
        }
    }

    public function AjaxSetDefaultAddress(Request $request)
    {
        if($request->ajax()) {
            TAIKHOAN_DIACHI::where('macdinh', 1)->update(['macdinh' => 0]);
            TAIKHOAN_DIACHI::where('id', $request->id)->update(['macdinh' => 1]);
    
            $response = [
                'message' => 'Đã thay đổi địa chỉ'
            ];

            return $response;
        }
    }

    public function ApplyVoucher(Request $request)
    {
        if($request->ajax()){
            $id = $request->id;

            $response = [
                'status' => 'success',
                'voucher' => null
            ];
    
            if(session('user')){
                if(count(TAIKHOAN::find(session('user')->id)->giohang) == 0){
                    return back();
                }
    
                if(session('voucher')){
                    Session::forget('voucher');

                    $response['status'] = 'cancel';
                } else {
                    $voucher = VOUCHER::find($id);

                    session(['voucher' => $voucher]);
                    $response['voucher'] = $voucher;
                }

                return $response;
            }
        }
    }

    public function AjaxDeleteObject(Request $request)
    {
        if($request->ajax()) {
            $response = [
                'message' => ''
            ];

            $id = $request->id;
            $object = $request->object;

            switch($object) {
                case 'address':
                    TAIKHOAN_DIACHI::destroy($id);
                    $response['message'] = 'Xóa địa chỉ thành công';
                    break;
                case 'item-cart':
                    GIOHANG::destroy($id);

                    if(!GIOHANG::count()) {
                        GIOHANG::truncate();
                    }
    
                    // xóa voucher đang áp dụng khi giỏ hàng rỗng
                    if(!GIOHANG::where('id_tk', session('user')->id)->count() && session('voucher')){
                        $request->session()->forget('voucher');
                    }
    
                    $response['message'] = 'Đã xóa sản phẩm';
                    break;
                case 'all-cart':
                    GIOHANG::where('id_tk', session('user')->id)->delete();
                    if(!GIOHANG::count()) {
                        GIOHANG::truncate();
                    }
                    // xóa session voucher
                    if(session('voucher')){
                        $request->session()->forget('voucher');
                    }
                    $response['message'] =  'Đã xóa giỏ hàng';
                    break;
                case 'order':
                    // cập nhật trạng thái đơn hàng: Đã hủy
                    DONHANG::where('id', $id)->update(['trangthaidonhang' => 'Đã hủy']);
    
                    $order = DONHANG::find($id);
                    $id_tk = $order->id_tk;
    
                    // hoàn lại số lượng kho
                    $this->refundOfInventory($order->id);
    
                    // khôi phục voucher đã áp dụng
                    if($order->id_vc){
                        $id_vc = DONHANG::find($id)->id_vc;
                        $this->restoreTheAppliedVoucher($id_vc, $id_tk);
                    }
    
                    $response['message'] = 'Đã hủy đơn hàng';
                    break;
                case 'evaluate':
                    $id_dg = $id;
    
                    $evaluate = DANHGIASP::find($id_dg);
    
                    // xóa hình đánh giá trong thư mục và db
                    foreach(CTDG::where('id_dg', $id_dg)->get() as $key){
                        unlink('images/evaluate/' . $key['hinhanh']);
                        CTDG::destroy($key['id']);
                    }
    
                    // xóa phản hồi
                    PHANHOI::where('id_dg', $id_dg)->delete();
    
                    // xóa lượt thích
                    LUOTTHICH::where('id_dg', $id_dg)->delete();
                    
                    // kiểm tra các dòng thuộc cùng 1 đánh giá
                    $lst_id = $this->IndexController->getListIdSameCapacity(DANHGIASP::find($id_dg)->id_sp);
                    
                    $lst_id_dg = [];
    
                    // danh sách đánh giá trong khoảng của id_sp và cùng thuộc 1 đánh giá
                    foreach(DANHGIASP::whereBetween('id_sp', [$lst_id[0], $lst_id[count($lst_id) - 1]])->get() as $key){
                        if($evaluate->id_tk == $key['id_tk'] && $evaluate->noidung == $key['noidung'] && $evaluate->thoigian == $key['thoigian']){
                            array_push($lst_id_dg, $key['id']);
                        }
                    }
    
                    // xóa các dòng thuộc 1 đánh giá
                    if(!empty($lst_id_dg)){
                        foreach($lst_id_dg as $key){
                            DANHGIASP::destroy($key);
                        }
                    } else {
                        DANHGIASP::destroy($id_dg);
                    }
    
                    $response['message'] = 'Đã xóa đánh giá';
                    break;
            }
    
            return $response;
        }
    }

    // hoàn lại số lượng kho
    public function refundOfInventory($id_dh)
    {
        $order = DONHANG::find($id_dh);

        foreach(CTDH::where('id_dh', $id_dh)->get() as $detail){
            // kho tại chi nhánh
            if($order->id_cn){
                // số lượng sp trong kho hiện tại
                $qtyInStock = KHO::where('id_cn', $order->id_cn)->where('id_sp', $detail->id_sp)->first()->slton;
                // số lượng sản phẩm mua
                $qtyBuy = $detail->sl;
                // trả lại số lượng kho
                $qtyInStock += $qtyBuy;
                // cập nhật kho
                KHO::where('id_cn', $order->id_cn)->where('id_sp', $detail->id_sp)->update(['slton' => $qtyInStock]);
            }
            // kho theo khu vực người đặt
            else {
                // tỉnh thành của người dùng
                $userProvince = DONHANG_DIACHI::find($order->id_dh_dc)->tinhthanh;

                // api tỉnh/ thành
                $provinceList = Http::get("https://provinces.open-api.vn/api/p/search/?q=$userProvince")->json();
                $province = [];

                foreach($provinceList as $val) {
                    if($val['name'] === $userProvince) {
                        $province = $val;
                        break;
                    }
                }

                // chi nhánh tại Hà Nội
                if($province['code'] < 48){
                    $branch = CHINHANH::where('id_tt', TINHTHANH::where('tentt', 'like', 'Hà Nội')->first()->id)->first();
                }
                // chi nhánh tại Hồ Chí Minh
                else {
                    $branch = CHINHANH::where('id_tt', TINHTHANH::where('tentt', 'like', 'Hồ Chí Minh')->first()->id)->first();
                }

                // số lượng sp trong kho tại chi nhánh
                $qtyInStock = KHO::where('id_cn', $branch->id)->where('id_sp', $detail->id_sp)->first()->slton;
                // số lượng sản phẩm mua
                $qtyBuy = $detail->sl;
                // trả lại số lượng kho
                $qtyInStock += $qtyBuy;
                // cập nhật kho
                KHO::where('id_cn', $branch->id)->where('id_sp', $detail->id_sp)->update(['slton' => $qtyInStock]);
            }
        }
    }

    // khôi phục voucher đã sử dụng
    public function restoreTheAppliedVoucher($id_vc, $id_tk)
    {
        $userVoucher = TAIKHOAN_VOUCHER::where('id_vc', $id_vc)->where('id_tk', $id_tk)->first();
        if(!$userVoucher){
            TAIKHOAN_VOUCHER::create([
                'id_tk' => $id_tk,
                'id_vc' => $id_vc,
                'sl' => 1
            ]);
        } else {
            $qty = $userVoucher->sl;
            $qty++;
            $userVoucher->sl = $qty;
            $userVoucher->save();
        }
    }

    /*============================================================================================================
                                                    Ajax
    ==============================================================================================================*/

    public function AjaxCheckNoti(Request $request)
    {
        if($request->ajax()){
            THONGBAO::where('id', $request->id)->update(['trangthaithongbao' => 1]);
        }
    }

    public function AjaxDeleteNoti(Request $request)
    {
        if($request->ajax()){
            THONGBAO::destroy($request->id);
        }
    }

    public function AjaxCheckAllNoti(Request $request)
    {
        if($request->ajax()){
            THONGBAO::where('id_tk', session('user')->id)->update(['trangthaithongbao' => 1]);
        }
    }

    public function AjaxDeleteAllNoti(Request $request)
    {
        if($request->ajax()){
            THONGBAO::where('id_tk', session('user')->id)->delete();
        }
    }

    public function AjaxDeleteFavorite(Request $request)
    {
        if($request->ajax()){
            SP_YEUTHICH::destroy($request->id);
        }
    }

    public function AjaxDeleteAllFavorite(Request $request)
    {
        if($request->ajax()){
            SP_YEUTHICH::where('id_tk', session('user')->id)->delete();
        }
    }

    public function AjaxChangeAvatar(Request $request)
    {
        if($request->ajax()) {
            $data = $request->base64String;
            $image_arr_1 = explode(';', $data);
            $image_arr_2 = explode(',', $image_arr_1[1]);
    
            $data = base64_decode($image_arr_2[1]);
    
            $imageName = session('user')->id.'.jpg';
            $urlImage = 'images/user/'.$imageName;
    
            file_put_contents($urlImage, $data);
    
            TAIKHOAN::where('id', session('user')->id)->update(['anhdaidien' => $imageName]);
    
            $this->IndexController->userSessionUpdate();
    
            return ['message' => 'Cập nhật ảnh đại diện thành công'];
        }
    }

    public function AjaxChangeFullname(Request $request)
    {
        if($request->ajax()){
            $update = TAIKHOAN::where('id', session('user')->id)->update(['hoten' => $request->hoten]);
            
            $this->IndexController->userSessionUpdate();
        }
    }

    public function AjaxChangePassword(Request $request)
    {
        if($request->ajax()){
            // kiểm tra mật khẩu cũ có chính xác không
            if(Hash::check($request->old_pw, TAIKHOAN::where('id', session('user')->id)->first()->password)){
                $new_pw = Hash::make($request->new_pw);
                TAIKHOAN::where('id', session('user')->id)->update(['password' => $new_pw]);

                $this->IndexController->userSessionUpdate();

                return [
                    'status' => 'success'
                ];
            }

            return [
                'status' => 'invalid password'
            ];
        }
    }

    public function AjaxAddDeleteFavorite(Request $request)
    {
        if($request->ajax()){
            // chưa đăng nhập
            if(!session('user')){
                return [
                    'status' => 'login required'
                ];
            }

            // chưa có: thêm vào danh sách yêu thích
            if(!SP_YEUTHICH::where('id_tk', session('user')->id)->where('id_sp', $request->id_sp)->first()){
                SP_YEUTHICH::create([
                    'id_tk' => session('user')->id,
                    'id_sp' => $request->id_sp,
                ]);

                return [
                    'status' => 'add success'
                ];
            }
            // đã có: xóa khỏi danh sách yêu thích
            else {
                SP_YEUTHICH::where('id_tk', session('user')->id)->where('id_sp', $request->id_sp)->delete();

                return [
                    'status' => 'delete success'
                ];
            }
        }
    }

    public function AjaxLikeComment(Request $request)
    {
        if($request->ajax()){
            $evaluate = DANHGIASP::where('id', $request->id_dg)->first();
            $user = session('user');
            // chưa thích bình luận
            if(!LUOTTHICH::where('id_tk', $user->id)->where('id_dg', $request->id_dg)->first()) {
                LUOTTHICH::create([
                    'id_tk' => $user->id,
                    'id_dg' => $request->id_dg,
                ]);

                // cập nhật lượt thích bảng DANHGIASP
                $qty = intval($evaluate->soluotthich);
                $qty++;
                DANHGIASP::where('id', $request->id_dg)->update(['soluotthich' => $qty]);

                return [
                    'status' => 'like success'
                ];
            }
            // bỏ thích
            LUOTTHICH::where('id_tk', $user->id)->where('id_dg', $request->id_dg)->delete();

            // cập nhật lượt thích bảng DANHGIASP
            $qty = intval($evaluate->soluotthich);
            $qty--;
            DANHGIASP::where('id', $request->id_dg)->update(['soluotthich' => $qty]);

            return [
                'status' => 'unlike success'
            ];
        }
    }

    public function CheckVoucherConditions(Request $request)
    {
        if($request->ajax()){
            $cartTotal = $request->cartTotal;
            $lst_voucher = [];

            $userVoucher = TAIKHOAN_VOUCHER::where('id_tk', session('user')->id)->get();

            if(!empty($userVoucher)){
                foreach($userVoucher as $key) {
                    $voucher = VOUCHER::find($key->id_vc);
    
                    // ngày hết hạn
                    $end = strtotime(str_replace('/', '-', $voucher->ngayketthuc));
                    // ngày hiện h
                    $current = strtotime(date('d-m-Y'));
    
                    // voucher còn HSD
                    if($end >= $current){
                        if($request->cartTotal >= $voucher->dieukien){
                            $voucher->status = 'satisfied';
                            $voucher->sl = $key->sl;
                        } else {
                            $voucher->status = 'unsatisfied';
                            $voucher->sl = $key->sl;
                        }
    
                        array_push($lst_voucher, $voucher);
                    }
                }
            }

            return $lst_voucher;
        }
    }

    public function AjaxChoosePhoneToEvaluate(Request $request)
    {
        if($request->ajax()){
            $lst_product = [];
            $lst_id = $request->lst_id;

            for($i = 0; $i < count($lst_id); $i++){
                $lst_product[$i] = $this->IndexController->getProductById($lst_id[$i]);
            }
            return $lst_product;
        }
    }

    public function AjaxCreateEvaluate(Request $request)
    {
        if($request->ajax()){
            // danh sách id_sp
            $lst_id = explode(',', $request->lst_id);
            $id_dg = 0;

            // đánh giá cho 1 sản phẩm
            if(count($lst_id) == 1){
                $data = [
                    'id_tk' => session('user')->id,
                    'id_sp' => $lst_id[0],
                    'noidung' => $request->evaluateContent,
                    'thoigian' => date('d/m/Y H:i:s'),
                    'soluotthich' => 0,
                    'danhgia' => $request->evaluateStarRating,
                    'chinhsua' => 0
                ];

                $create = DANHGIASP::create($data);
                $id_dg = $create->id;
            }
            // đánh giá cho nhiều sản phẩm
            else {
                $time = date('d/m/Y H:i:s');
                $id_tk = session('user')->id;

                // lấy id_dg đầu tiên cho trường hợp có ảnh đính kèm
                $data = [
                    'id_tk' => $id_tk,
                    'id_sp' => $lst_id[0],
                    'noidung' => $request->evaluateContent,
                    'thoigian' =>$time,
                    'soluotthich' => 0,
                    'danhgia' => $request->evaluateStarRating,
                    'chinhsua' => 0
                ];

                $create = DANHGIASP::create($data);

                $id_dg = $create->id;

                for($i = 1; $i < count($lst_id); $i++){
                    $data = [
                        'id_tk' => $id_tk,
                        'id_sp' => $lst_id[$i],
                        'noidung' => $request->evaluateContent,
                        'thoigian' =>$time,
                        'soluotthich' => 0,
                        'danhgia' => $request->evaluateStarRating,
                        'chinhsua' => 0
                    ];

                    DANHGIASP::create($data);
                }
            }

            // có ảnh đính kèm
            if($request->evaluateImage) {
                // lấy hình ảnh của đánh giá vừa thêm vào
                $evaluateImageList = [];
                $allImages = scandir(public_path('images/evaluate'));
                $tempName = 'temp_';

                foreach($allImages as $image) {
                    if(str_contains($image, $tempName)) {
                        array_push($evaluateImageList, $image);
                    } 
                }

                // đổi lại tên hình
                $directory = 'images/evaluate/';
                foreach($evaluateImageList as $i => $image) {
                    $format = $this->splitNameAndFormat($image)['format'];
                    $newName = time().$i.$format;
                    rename(
                        $directory.$image,
                        $directory.$newName
                    );
    
                    // thêm vào db
                    CTDG::create([
                        'id_dg' => $id_dg,
                        'hinhanh' => $newName
                    ]);
                }
            }

            return ['message' => 'Đã đánh giá sản phẩm'];
        }
    }

    public function AjaxUploadSingleImageEvaluate(Request $request)
    {
        if($request->ajax()) {
            $base64String = $request->base64String;

            // định dạng hình
            $format = $this->IndexController->getImageFormat($base64String);
            
            $base64 = str_replace('data:image/'.$format.';base64,', '', $base64String);
            $imageName = 'temp_' . $request->index . '.' . $format; // vd: temp_0.png

            // lưu hình
            $this->IndexController->saveImage('images/evaluate/'.$imageName, $base64);

            return true;
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

    public function AjaxEditEvaluate(Request $request)
    {
        if($request->ajax()){
            // cập nhật đánh giá
            // kiểm tra gộp đánh giá
            $id_dg = $request->id_dg;
            $evaluate = DANHGIASP::find($id_dg);

            $lst_id = $this->IndexController->getListIdSameCapacity($evaluate->id_sp);
            
            $lst_id_dg = [];

            // danh sách đánh giá trong khoảng của id_sp
            foreach(DANHGIASP::whereBetween('id_sp', [$lst_id[0], end($lst_id)])->get() as $key){
                if($evaluate->id_tk == $key->id_tk &&
                    $evaluate->noidung == $key->noidung &&
                    $evaluate->thoigian == $key->thoigian) {
                    array_push($lst_id_dg, $key->id);
                }
            }

            // cập nhật các dòng của cùng 1 đánh giá
            if(!empty($lst_id_dg)){
                foreach($lst_id_dg as $key){
                    $data = [
                        'noidung' => $request->evaluateContent,
                        'thoigian' => date('d/m/Y H:i:s'),
                        'danhgia' => $request->evaluateStarRating,
                        'chinhsua' => 1
                    ];

                    DANHGIASP::where('id', $key)->update($data);
                }
            } else {
                $data = [
                    'noidung' => $request->evaluateContent,
                    'thoigian' => date('d/m/Y H:i:s'),
                    'danhgia' => $request->evaluateStarRating,
                    'chinhsua' => 1
                ];

                DANHGIASP::where('id', $request->id_dg)->update($data);
            }

            // xóa hình đánh giá trong thư mục và db
            foreach(CTDG::where('id_dg', $id_dg)->get() as $key){
                unlink('images/evaluate/' . $key->hinhanh);
                CTDG::destroy($key->id);
            }

            // cập nhật hình mới
            if(!empty($request->evaluateImage)){
                // lấy hình ảnh của đánh giá vừa thêm vào
                $evaluateImageList = [];
                $allImages = scandir(public_path('images/evaluate'));
                $tempName = 'temp_';

                foreach($allImages as $image) {
                    if(str_contains($image, $tempName)) {
                        array_push($evaluateImageList, $image);
                    } 
                }

                // đổi lại tên hình
                $directory = 'images/evaluate/';
                foreach($evaluateImageList as $i => $image) {
                    $format = $this->splitNameAndFormat($image)['format'];
                    $newName = time().$i.$format;
                    rename(
                        $directory.$image,
                        $directory.$newName
                    );
    
                    // thêm vào db
                    CTDG::create([
                        'id_dg' => $id_dg,
                        'hinhanh' => $newName
                    ]);
                }
            }

            return ['message' => 'Đã chỉnh sửa đánh giá'];
        }
    }

    public function AjaxReply(Request $request)
    {
        if($request->ajax()){
            $create = PHANHOI::create([
                'id_tk' => session('user')->id,
                'id_dg' => $request->id_dg,
                'noidung' => $request->replyContent,
                'thoigian' => date('d/m/Y H:i:s'),
            ]);

            $evaluate = DANHGIASP::find($request->id_dg);
            $user = TAIKHOAN::find($evaluate->id_tk);
            $userReply = TAIKHOAN::find($create->id_tk);
            $product = $this->IndexController->getProductById($evaluate->id_sp);

            // gửi thông báo
            if($userReply->id != $user->id){
                THONGBAO::create([
                    'id_tk' => $user->id,
                    'tieude' => 'Phản hồi',
                    'noidung' => "Bạn có một phản hồi từ <b>$userReply->hoten</b> ở sản phẩm <b>".$product['tensp'] .' - '. $product['mausac']."</b>.",
                    'thoigian' => date('d/m/Y H:i:s')
                ]);

                $notification = [
                    'user' => $user,
                    'type' => 'reply',
                    'data' => [
                        'userReply' => $userReply,
                        'avtURL' => $userReply->htdn == 'normal' ? 'images/user/'.$userReply->anhdaidien : $userReply->anhdaidien,
                        'link' => route('user/chi-tiet', ['name' => $product['tensp_url'], 'danhgia' => $request->id_dg])
                    ]
                ];
                //PUSH NOTI TO APP 
                if(!empty($user->device_token))
                (new PushNotificationController)->sendPush($user->device_token, "Phản hồi", "Bạn có một phản hồi từ <b>".$userReply->hoten."</b> ở sản phẩm <b>".$product['tensp']." ".$product['dungluong']." - ".$product['mausac']."</b>.");

                event(new sendNotification($notification));
            }
        }
    }

    public function AjaxGetTypeNotification(Request $request)
    {
        if($request->ajax()){
            $data = [];
            $type = $request->type;
            $user = session('user');
            switch($type){
                case 'all':
                    $data = THONGBAO::where('id_tk', $user->id)->orderBy('id', 'desc')->limit(10)->get();
                    break;
                case 'not-seen':
                    $data = THONGBAO::where('id_tk', $user->id)->orderBy('id', 'desc')->where('trangthaithongbao', 0)->get();
                    break;
                case 'seen':
                    $data = THONGBAO::where('id_tk', $user->id)->orderBy('id', 'desc')->where('trangthaithongbao', 1)->get();
                    break;
                case 'order':
                    $userNoti = THONGBAO::where('id_tk', $user->id)
                                        ->orderBy('id', 'desc')->get();
                    
                    foreach($userNoti as $noti) {
                        if($noti->tieude === 'Đơn đã tiếp nhận' || $noti->tieude === 'Đơn đã xác nhận' || $noti->tieude === 'Giao hàng thành công') {
                            array_push($data, $noti);
                        }
                    }
                    break;
                case 'voucher':
                    $data = THONGBAO::where('id_tk', $user->id)->orderBy('id', 'desc')->where('tieude', 'Mã giảm giá')->get();
                    break;
                case 'reply':
                    $data = THONGBAO::where('id_tk', $user->id)->orderBy('id', 'desc')->where('tieude', 'Phản hồi')->get();
                    break;
            }

            return $data;
        }
    }

    public function AjaxDeleteExpiredVoucher(Request $request){
        if($request->ajax()){
            TAIKHOAN_VOUCHER::destroy($request->id);
        }
    }

    public function AjaxRemoveVoucher(Request $request){
        if($request->ajax()){
            Session::forget('voucher');
        }
    }

    public function AjaxIsAppliedVoucher(Request $request)
    {
        if($request->ajax()) {
            return session('voucher') ? true : false;
        }
    }

    public function AjaxIsExpiredVoucher(Request $request){
        if($request->ajax()){
            $id = session('voucher')->id;
            $voucher = VOUCHER::find($id);

            // ngày kết thúc
            $end = strtotime(str_replace('/', '-', $voucher->ngayketthuc));
            // ngày hiện tại
            $current = strtotime(date('d-m-Y'));
            // voucher hết HSD
            if($end < $current){
                $request->session()->forget('voucher');
                return true;
            }

            return false;
        }
    }

    public function AjaxCheckSatisfiedVoucher(Request $request) {
        if($request->ajax()) {
            $response = [
                'status' => true
            ];

            $total = 0;
            $id_tk = session('user')->id;

            // lấy tổng tiền giỏ hàng thanh toán
            foreach($request->idList as $id_sp) {
                $qtyInStock = KHO::where('id_sp', $id_sp)->sum('slton');

                if($qtyInStock > 0) {
                    $product = $this->IndexController->getProductById($id_sp);
                    $price = $product['giakhuyenmai'];
                    $qtyInCart = GIOHANG::where('id_tk', $id_tk)->where('id_sp', $id_sp)->first()->sl;
                    $total += $price * $qtyInCart;
                }
            }

            // điều kiện voucher
            $condition = session('voucher')->dieukien;

            return $total >= $condition;
        }
    }
}
