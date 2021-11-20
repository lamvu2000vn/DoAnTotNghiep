<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Classes\Helper;
use App\Models\User;
use App\Models\THONGBAO;
use App\Models\LUOTTRUYCAP;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Hash;
use Auth;
class UserController extends Controller
{
    //
    public function signup(Request $request)
    { 
        if(!empty($request->email)){
            if(User::whereemail($request->email)->exists()){
                return response()->json([
                    'status' => true,
                    'messages' => 'accountSocialCreated'
                ]);
            }
            $user = new User([
                'hoten' => $request->hoten,
                'sdt' => $request->sdt,
                'email' => $request->email,
                'anhdaidien' => $request->anhdaidien,
                'loaitk'=>0,
                'htdn'=>$request->htdn,
                'thoigian'=> Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y'),
                'trangthai'=>1,
            ]);
            if($user->save()){
                return response()->json([
                    'status' => true,
                    'code'  => 200,
                    'messages' => 'accountSocialCreated'
                ]);
            }
        }  
        $user = new User([
            'hoten' => $request->hoten,
            'sdt' => $request->sdt,
            'password' => Hash::make($request->password),
            'anhdaidien' => "avatar-default.png",
            'loaitk'=>0,
            'htdn'=>$request->htdn,
            'thoigian'=> Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y'),
            'trangthai'=>1,
        ]);
        if($user->save()){
            return response()->json([
                'status' => true,
                'code'  => 200,
                'messages' => 'Đăng kí tài khoản thành công!'
            ]);
        }
            return response()->json([
                'status' => false,
                'code'  => 404,
                'messages' => 'Thất bại!'
            ]);
        
        
    }

    public function login(Request $request)
    {
        if($request->htdn =="socialNetwork"){
            $id = User::where("email", request('email'))->value('id');
            $user = User::find($id);
            $user->device_token =  request('device_token');
            $tokenResult = $user->createToken('LD Mobile');
            $token = $tokenResult->token;
            $token->save();
            $user->save();
            return response()->json([
                'status' => true,
                'code'  => 200,
                'messages'=> null,
                'data' => $user,
                'token' => $tokenResult->accessToken,
            ]);
        }else{
            $login = [
                'sdt' => request('phone'),
                'password' => request('password'),
            ];
        }
           
        if(Auth::attempt($login)){
            $user = $request->user();
            $user->device_token =  request('device_token');
            $tokenResult = $user->createToken('LD Mobile');
            $token = $tokenResult->token;
            $token->save();
            $user->save();
            if($user->htdn =="normal"){
                $user->anhdaidien = Helper::$URL.'user/'.$user->anhdaidien;
            }
            return response()->json([
                'status' => true,
                'code'  => 200,
                'messages'=> null,
                'data' => $user,
                'token' => $tokenResult->accessToken,
            ]);
        }
            return response()->json([
                'status' => false,
                'code'  => 401,
                'messages' => "* Số điện thoại hoặc mật khẩu không chính xác"
            ]);
        
    }
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->device_token = "";
        $user->update();
        $request->user()->token()->revoke();
        return response()->json([
            'status' => 'true',
            'messages' => 'Successfully logged out'
        ]);
    }
    public function checkNumberPhone(Request $request){

        if(User::where("sdt", $request->sdt)->exists()){
            return response()->json([
                'status' => false,
                'messages' => '* Số điện thoại đã được sử dụng cho tài khoản khác',
                'data'=>null
            ]);
        }
        return response()->json([
            'status' => true,
            'messages' => 'OK',
            'data'=>null
        ]);
    }
    public function changeAvatar($id, Request $request){
        $user = User::find($id);
        $user->anhdaidien = Helper::imageUpload($request);
        if($user->update()){
            if($user->htdn =="normal"){
                $user->anhdaidien = Helper::$URL.'user/'.$user->anhdaidien;
            }
            return response()->json([
                'status' => true,
                'messages' => "Đổi ảnh đại diện thành công",
                'data' => $user
            ]);
        }
        return response()->json([
            'status' => false,
            'messages' => "Có lỗi xảy ra",
            'data' => null
        ]);
        
        
    }
    public function changeInfoUser($id, Request $request){
        $user = User::find($id);
        if(!empty($request->name)){
            $user->hoten = $request->name;
            if($user->update()){
                return response()->json([
                    'status' => true,
                    'messages' => "Cập nhật thành công",
                    'data' => null
                ]);
            }
        }
        
        return response()->json([
            'status' => false,
            'messages' => "Có lỗi xảy ra, vui lòng thử lại sau...",
            'data' => null
        ]);
    }
    public function checkPassword(Request $request){
        $find = User::where('sdt', $request->phone)->value('password');
        if(Hash::check($request->password, $find)){
            return response()->json([
                'status' => true,
                'messages' => "",
                'data' => null
            ]);
        }
        return response()->json([
            'status' => false,
            'messages' => "Có lỗi xảy ra, vui lòng thử lại sau...",
            'data' => null
        ]);
    }
    public function changePassword(Request $request){
        $find = User::where('sdt', $request->phone)->value('id');
        $user = User::find($find);
        $user->password = Hash::make($request->password);
        if($user->update()){
            if($user->update()){
                return response()->json([
                    'status' => true,
                    'messages' => "Đổi mật khẩu thành công",
                    'data' => null
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'messages' => "Có lỗi xảy ra, vui lòng thử lại sau...",
            'data' => null
        ]);
    }
    public function getTotalNotification($id){
        $notification = THONGBAO::where('id_tk', $id)->where('trangthaithongbao', 0)->get();
      
        return response()->json([
            'status' => true,
            'messages' => "",
            'data' => $notification
        ]);
    }
    public function deleteNotification($id){
        $notification = THONGBAO::find($id);
        if($notification->delete()){
            return response()->json([
                'status' => true,
                'messages' => "Xóa thông báo thành công",
                'data' => null
            ]);
        };
        return response()->json([
            'status' => false,
            'messages' => "Có lỗi xảy ra, vui lòng thử lại sau...",
            'data' => null
        ]);
    }
    public function updateNotification($id){
        $notification = THONGBAO::find($id);
        $notification->trangthaithongbao = 1;
        if($notification->update()){
            return response()->json([
                'status' => true,
                'messages' => "",
                'data' => null
            ]);
        };
        return response()->json([
            'status' => false,
            'messages' => "Có lỗi xảy ra, vui lòng thử lại sau...",
            'data' => null
        ]);
       
    }
    public function getNotification($id){
        $notification = THONGBAO::orderBy("id","desc")->where('id_tk', $id)->get();
        return response()->json([
            'status' => true,
            'messages' => "",
            'data' => $notification
        ]);
    }
    public function increaseView(){
        $view = new LUOTTRUYCAP();
        $view->nentang = "app";
        $view->thoigian = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
        if($view->save()){
            return response()->json([
                'status' => true,
                'message' => "",
                'data' => null
            ]);
        }
        
    }
    
}
