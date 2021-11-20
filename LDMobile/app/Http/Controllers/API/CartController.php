<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SANPHAM;
use App\Models\MAUSP;
// use App\Models\ThongBao;
use App\Models\GIOHANG;
// use App\Models\CHITIETGIOHANG;
use App\Models\DONHANG;
use App\Models\CTDH;
use App\Models\KHO;
use App\Models\TAIKHOAN;
use App\Models\KHUYENMAI;
use App\Models\THONGBAO;
use App\Models\TAIKHOAN_VOUCHER;
use App\Models\CHINHANH;
use App\Models\VOUCHER;
use App\Models\TINHTHANH;
use App\Models\TAIKHOAN_DIACHI;
use App\Models\HANGDOI;
use App\Classes\Helper;
use App\Models\DONHANG_DIACHI;
use App\Models\CTHD;
use session;
use Carbon\Carbon;
class CartController extends Controller
{
    //
    public function getTotalProductInCart($id){
        $cart = GIOHANG::where("id_tk", $id)->get();
        $total = count($cart);
        return response()->json([
            'status' => true,
            'message' => '',
    		'data' =>$total
    	]);
    }
    public function getMyCart($id){
        $listProduct = GIOHANG::where("id_tk", $id)->get();
        $totalMoney = 0;
        foreach($listProduct as $product){
            $pro = SANPHAM::find($product->id_sp);
            $product->name = $pro->tensp;
            $product->avatar = Helper::$URL."phone/".$pro->hinhanh;
            $product->color = $pro->mausac;
            $product->storage = $pro->dungluong;
            if(!empty($pro->id_km)){
                $product->discount = KHUYENMAI::find($pro->id_km)->chietkhau;
            }else  $product->discount = 0 ;
            
            $product->price = $pro->gia-($pro->gia*$product->discount);
            $product->priceRoot =  $pro->gia;
            $product->isAvailable = true;
            $product->isQtyAvailable = true;
            $qty = 0;
            $products = KHO::where('id_sp', $product->id_sp)->get();
            foreach($products  as $pr){
                    $qty +=  $pr->slton;
            }
            $product->slton = $qty;

        }
        return response()->json([
            'status' => true,
            'message' => '',
    		'data' => $listProduct
    	]);
    }
    public function addToCart(Request $req){
        $i = 0;
        $image = str_replace(Helper::$URL.'phone/','',$req->hinhanh);
       
        $pro = SANPHAM::where('dungluong', $req->dungluong)->where('hinhanh','LIKE','%'.$image.'%')->get();
        $product = SANPHAM::find($pro[0]->id);
        $cart = GIOHANG::where("id_tk", $req->id_user)->where("id_sp", $product->id)->get();
        if(count($cart)==0){
                $cart = new GIOHANG;
                $cart->id_tk = $req->id_user;
                $cart->id_sp = $product->id;
                $cart->sl= $req->qty;
                // $GioHang->TongGia=0;
                // $GioHang->TongTien=0;
                // $GioHang->TrangThai=1;
                // $GioHang->NgayLap = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y H:i');
                $cart->save();
                return response()->json([
                    'status' => true,
                    'message' => 'Thêm vào giỏ hàng thành công',
                    'data' =>null
                ]);
        }else{ 
            if($cart[0]->sl==5){
                return response()->json([
                    'status' => false,
                    'message' => 'Số lượng được mua đã giới hạn',
                    'data' =>null
                ]);
            }
            $updateQtyProductInCart = GIOHANG::find($cart[0]->id);
            $updateQtyProductInCart->sl = $updateQtyProductInCart->sl + $req->qty;
            if($updateQtyProductInCart->sl > 5){
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn chỉ được mua tối đa 5 sản phẩm! ',
                    'data' =>null
                ]);
            }
            $updateQtyProductInCart->update();
            return response()->json([
                'status' => true,
                'message' => 'Thêm vào giỏ hàng thành công',
                'data' =>null
            ]);
        }      
         return response()->json([
                'status' => false,
                'message' => 'Thất bại',
                'data' =>null
        ]);   
    }
    public function updateCart($id, Request $request){
        $cart = GIOHANG::find($id);
        if($request->plusOrMin =="plus"){
            $cart->sl++;
        }else $cart->sl--;
     
        if($cart->update());{
            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thành công',
                'data' =>null
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Có lỗi xảy ra',
            'data' =>null
        ]);
        
    }
    public function deleteProductInCart($id){
        $cart = GIOHANG::find($id);
        if($cart->delete()){
            return response()->json([
                'status' => true,
                'message' => 'Xóa sản phẩm thành công',
                'data' =>null
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Có lỗi xảy ra',
            'data' =>null
        ]);
    }
    public function getMyVoucher($id){
        $listResult = array();
        $myVoucher = TAIKHOAN_VOUCHER::where('id_tk', $id)->get();
        foreach($myVoucher as $voucher){
            $infoVoucher = VOUCHER::find($voucher->id_vc);
            $infoVoucher->sl = $voucher->sl;
            array_push($listResult, $infoVoucher);
        }
        $currentDate = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y');
     
        foreach($listResult as $voucher){
            $date1 = Carbon :: createFromFormat ('d/m/Y', $currentDate);
            $date2 = Carbon :: createFromFormat ('d/m/Y', $voucher->ngayketthuc);
           if($date2->gte($date1)){
            $voucher->active = true;
           }else $voucher->active = false;
           $voucher->chietkhau = $voucher->chietkhau*100;
           $voucher->ngayketthuc = $date2->format('d/m/Y');
        }
        return response()->json([
            'status' => true,
            'message' => '',
            'data' =>$listResult
        ]);
    }
    public function deleteMyVoucher($id){
        
        $myVoucher = TAIKHOAN_VOUCHER::where('id_vc', $id)->get();
        $voucher = TAIKHOAN_VOUCHER::find($myVoucher[0]->id);
        if($voucher->delete()){
            return response()->json([
                'status' => true,
                'message' => 'Xóa voucher thành công',
                'data' =>null
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Có lỗi xảy ra',
            'data' =>null
        ]);
    }
    public function createOrder($id, Request $request){
        $order = new DONHANG();
        $order->id_tk = $id;
        $order->pttt = request('pttt');
        if(!empty(request('id_tk_dc'))){
            $address = TAIKHOAN_DIACHI::find(request('id_tk_dc'));
            $exists = DONHANG_DIACHI::where('diachi', $address->diachi)->where('phuongxa', $address->phuongxa)->where('quanhuyen', $address->quanhuyen)->where('tinhthanh', $address->tinhthanh)->where('sdt', $address->sdt)->get();
            $exist = count($exists);
            if($exist==0){
                $orderAddress = new DONHANG_DIACHI();
                $orderAddress->diachi = $address->diachi;
                $orderAddress->phuongxa = $address->phuongxa;
                $orderAddress->quanhuyen = $address->quanhuyen;
                $orderAddress->tinhthanh = $address->tinhthanh;
                $orderAddress->sdt = $address->sdt;
                $orderAddress->hoten = $address->hoten;
                $orderAddress->save();
                $order->id_dh_dc = $orderAddress->id;
            }else $order->id_dh_dc =  $exists[0]->id;
        }else $order->id_cn = request('id_cn');
        if(!empty(request('id_vc'))){
            $voucher = TAIKHOAN_VOUCHER::where('id_vc',(request('id_vc')))->get();
            $myVoucher= TAIKHOAN_VOUCHER::find($voucher[0]->id);
            $myVoucher->sl--;
            if($myVoucher->sl==0){
                $myVoucher->delete();
                
            }else $myVoucher->update();
        }
        $order->id_vc = request('id_vc');
        $order->hinhthuc = request('hinhthuc');
        $order->trangthaidonhang = "Đã tiếp nhận";
        $order->thoigian = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y H:i');
        $order->tongtien = request('tongtien');
        $order->trangthaidonhang = "Đã tiếp nhận";
        if($order->save()){
            foreach(request('listProduct') as $product){
                $detailOrder = new CTDH();
                $detailOrder->id_dh = $order->id;
                $detailOrder->id_sp = $product['id_sp'];
                $detailOrder->gia = $product['priceRoot'];
                $detailOrder->sl = $product['sl'];
                $detailOrder->giamgia = $product['discount'];
                $detailOrder->thanhtien =  $product['price']*$product['sl'];
                $detailOrder->save();
                
                //kho
                if(!empty(request('id_tk_dc'))){
                    $city = DONHANG_DIACHI::find($order->id_dh_dc);
                    $url ="TinhThanh.json" ;
                    $datos = file_get_contents($url);
                    $data = json_decode($datos, true);
                    if($city->tinhthanh == "Thành phố Hồ Chí Minh")
                     $data = collect($data)->where("Name", "Hồ Chí Minh")->all();
                    else if($city->tinhthanh == "Thành phố Hà Nội"){
                         $data = collect($data)->where("Name", "Hà Nội")->all();
                    }
                    else if($city->tinhthanh == "Thành phố Hải Phòng"){
                         $data = collect($data)->where("Name", "Hải Phòng")->all();
                    }
                    else if($city->tinhthanh == "Thành phố Đà Nẵng"){
                         $data = collect($data)->where("Name", "Đà Nẵng")->all();
                    }else if($city->tinhthanh == "Thành phố Cần Thơ"){
                         $data = collect($data)->where("Name", "Cần Thơ")->all();
                    }
                    else $data = collect($data)->where("Name", $city->tinhthanh)->all();
                    if(reset($data)['ID'] < 48 ){
                        $id_city = TINHTHANH::where('tentt', "Hà Nội")->get();
                    }else{
                        $id_city = TINHTHANH::where('tentt', "Hồ Chí Minh")->get();
                    }
                  
                    $shop = CHINHANH::where('id_tt', $id_city[0]->id)->get();
                    $ware = KHO::where('id_cn', $shop[0]->id)->where('id_sp', $product['id_sp'])->get();
                    $wareHouse = KHO::find($ware[0]->id);
                    if($wareHouse->slton>0){
                        if($wareHouse->slton==1&&$product['sl']==2){
                            if($city->tinhthanh == "Hồ Chí Minh"){
                                $cityOther = TINHTHANH::where('tentt', "Hà Nội")->get();
                            }else $cityOther= TINHTHANH::where('tentt', "Hồ Chí Minh")->get();
                            $shop = CHINHANH::where('id_tt', $cityOther[0]->id)->get();
                            $ware = KHO::where('id_cn', $shop[0]->id)->where('id_sp', $product['id_sp'])->get();
                            $wareHouse = KHO::find($ware[0]->id);
                            if($wareHouse->slton>0){
                                if($wareHouse->slton==1&&$product['sl']==2){
                                    return response()->json([
                                        'status' => false,
                                        'message' => 'Có lỗi xảy ra',
                                        'data' =>null
                                    ]);
                                }else{ 
                                    $wareHouse->slton--;
                                    if($product['sl']==2)$wareHouse->slton--;
                                    $wareHouse->update();
                                }  
                            }else{
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Có lỗi xảy ra',
                                    'data' =>null
                                ]);
                            }
                        }else{
                            $wareHouse->slton--;
                            if($product['sl']==2)$wareHouse->slton--;
                            $wareHouse->update();
                        }      
                    }else{
                        $cityCurrent = TINHTHANH::find($id_city[0]->id);
                        if($cityCurrent->tentt == "Hồ Chí Minh"){
                            $cityOther = TINHTHANH::where('tentt', "Hà Nội")->get();
                        }else $cityOther= TINHTHANH::where('tentt', "Hồ Chí Minh")->get();
                        
                        $shop = CHINHANH::where('id_tt', $cityOther[0]->id)->get();
                        $ware = KHO::where('id_cn', $shop[0]->id)->where('id_sp', $product['id_sp'])->get();
                        $wareHouse = KHO::find($ware[0]->id);
                        if($wareHouse->slton>0){
                            if($wareHouse->slton==1&&$product['sl']==2){
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Có lỗi xảy ra',
                                    'data' =>null
                                ]);
                            }else{
                                $wareHouse->slton--;
                                    if($product['sl']==2)$wareHouse->slton--;
                                $wareHouse->update();
                            }  
                        }else{
                            return response()->json([
                                'status' => false,
                                'message' => 'Có lỗi xảy ra',
                                'data' =>null
                            ]);
                        }
                       
                    }  
                }else{
                    $ware = KHO::where('id_cn', request('id_cn'))->where('id_sp', $product['id_sp'])->get();
                    $wareHouse = KHO::find($ware[0]->id);
                    $wareHouse->slton--;
                    if($product['sl']==2)$wareHouse->slton--;
                    $wareHouse->update();
                }
                
                if(!empty($product['id'])){
                     $cart = GIOHANG::find($product['id']);
                     $cart->delete();
                }
                
            }
            $notification = new THONGBAO();
            $notification->id_tk = $id;
            $notification->tieude = "Đơn đã tiếp nhận";
            $notification->noidung = "Đã tiếp nhận đơn hàng số <b>#".$order->id."</b>";
            $notification->thoigian = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y H:i');
            $notification->trangthaithongbao = 0;
            $notification->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Đặt hàng thành công',
                'data' => null
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Có lỗi xảy ra',
            'data' =>null
        ]);
    }
    public function checkProduct($id, Request $request){
        
        return response()->json([
            'status' => true,
            'message' => '',
            'data' =>  ([
                "exist"=> $listValid,
                "nonExist"=> $listInvalid,
                "downOne"=> $listDecrease,
            ])
        ]);
    
    }
    public function checkQtyInWareHouse(Request $request){
        $image = str_replace(Helper::$URL.'phone/','',$request->hinhanh);
        $product = SANPHAM::where('hinhanh', $image)->where('dungluong', $request->dungluong)->get();
        $qty = 0;
        $products = KHO::where('id_sp', $product[0]->id)->get();
        foreach($products  as $product){
            $qty +=  $product->slton;
        }
        if($qty <=5){
            return response()->json([
                'status' => true,
                'message' => 'Chỉ còn '.$qty.' sản phẩm',
                'data' => $qty
            ]);
        }  
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => null,
        ]);
    }

    public function checkQtyByColorStorage(Request $request, $id){
        $listID = array();
        $listResult = array();
        $price = 0;
        // $listProduct = SANPHAM::where('id_msp', $id)->get();
        if($request->color){
            $image = str_replace(Helper::$URL.'phone/','',$request->color);
            $listProduct = SANPHAM::where('id_msp', $id)->where('hinhanh',$image)->get();
            foreach($listProduct as $product){
                array_push($listID,  $product->id);
            }
            foreach($listID as $id){
                $qty = 0;
                $products = KHO::where('id_sp', $id)->get();
                foreach($products  as $product){
                    $qty +=  $product->slton;
                }
                $product = SANPHAM::find($id);
                $product->hinhanh = Helper::$URL.'phone/'.$product->hinhanh;
                if($qty == 0){
                    array_push($listResult, $product);
                }
            }
            
        }else{
            $listProduct = SANPHAM::where('id_msp', $id)->where('dungluong', $request->storage)->get();
            foreach($listProduct as $product){
                array_push($listID,  $product->id);
            }
            foreach($listID as $id){
                $qty = 0;
                $products = KHO::where('id_sp', $id)->get();
                foreach($products  as $product){
                    $qty +=  $product->slton;
                }
                $product = SANPHAM::find($id);
                $product->hinhanh = Helper::$URL.'phone/'.$product->hinhanh;
                if($qty == 0){
                    array_push($listResult, $product);
                }
                if(!empty($product->id_km)){
                    $product->discount = KHUYENMAI::find($product->id_km)->chietkhau;
                }else  $product->discount = 0 ;
                $price = $product->gia-($product->gia*$product->discount);
            }
        }
       
        return response()->json([
            'status' => true,
            'message' => '',
            'data' =>  ([
                "list"=> $listResult,
                "priceOfProduct"=> $price,
            ])
        ]);          
        
    }

    public function getProvinceStore(){
        $listProvince = TINHTHANH::all();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => $listProvince
        ]);
    }
    public function getAddressStore($id){
        $branch = CHINHANH::where('id_tt', $id)->get();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => $branch
        ]);
    }
    public function getProvince(){
        $url ="TinhThanh.json" ;
        $datos = file_get_contents($url);
        $data = json_decode($datos, true);
        return response()->json([
            'status' => true,
            'message' => '',
            'data' =>  $data
        ]);
        
    }
    public function getDistrict(Request $request){
        $url ="QuanHuyen.json" ;
        $datos = file_get_contents($url);
        $data = json_decode($datos, true);
        return response()->json([
            'status' => true,
            'message' => '',
            'data' =>  $data[strval($request->id)]
        ]);
        
    }
    public function getWard(Request $request){
        $url ="PhuongXa.json" ;
        $datos = file_get_contents($url);
        $data = json_decode($datos, true);
        return response()->json([
            'status' => true,
            'message' => '',
            'data' =>  $data[strval($request->id)]
        ]);
    }
    public function getMyAddress($id){
        $listAddress = TAIKHOAN_DIACHI::where('id_tk', $id)->get();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' =>  $listAddress
        ]);
    }
    public function getID(Request $request){
        $IDCity = "";
        $IDDistrict ="";
        $url ="TinhThanh.json" ;
        $datos = file_get_contents($url);
        $data = json_decode($datos, true);
        $data = array_filter($data);
        $data = collect($data)->where("Name", $request->city)->all();
        $firstKey = array_key_first($data);
        $IDCity = $data[$firstKey]['ID'];
        $url ="QuanHuyen.json" ;
        $datos = file_get_contents($url);
        $data = json_decode($datos, true);
        $data = array_filter($data[$IDCity]);
        $data = collect($data)->where("Name",$request->district)->all();
        $firstKey = array_key_first($data);
        $IDDistrict = $data[$firstKey]['ID'];
  
        return response()->json([
                 $IDCity,
                $IDDistrict,
                
        ]);
    }
    public function createMyAddress(Request $request){
        $newAddress = new TAIKHOAN_DIACHI();
        $newAddress->id_tk = request('id_tk');
        $newAddress->diachi = request('diachi');
        $newAddress->phuongxa = request('phuongxa');
        $newAddress->hoten = request('hoten');
        $newAddress->quanhuyen = request('quanhuyen');
        $newAddress->tinhthanh = request('tinhthanh');
        $newAddress->sdt = request('sdt');
        $newAddress->macdinh = request('macdinh');
        if(request('macdinh')==1){
            $oldDefault = TAIKHOAN_DIACHI::where('macdinh', 1)->get();
            $count = count($oldDefault);
            if($count > 0){
                $oldAddress = TAIKHOAN_DIACHI::find($oldDefault[0]->id);
                $oldAddress->macdinh = 0;
                $oldAddress->update();
            }
            
        }
        if($newAddress->save()){
            return response()->json([
                'status' => true,
                'message' => 'Thêm địa chỉ thành công',
                'data' =>  null
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Có lỗi xảy ra',
            'data' =>  null
        ]);
        
    }
    public function updateMyAddress($id, Request $request){
        $updateAddress = TAIKHOAN_DIACHI::find($id);
        $updateAddress->id_tk = request('id_tk');
        $updateAddress->hoten = request('hoten');
        $updateAddress->diachi = request('diachi');
        $updateAddress->phuongxa = request('phuongxa');
        $updateAddress->quanhuyen = request('quanhuyen');
        $updateAddress->tinhthanh = request('tinhthanh');
        $updateAddress->sdt = request('sdt');
        $updateAddress->macdinh = request('macdinh');
        if(request('macdinh')==1){
            $oldDefault = TAIKHOAN_DIACHI::where('macdinh', 1)->get();
            $count = count($oldDefault);
            if($count > 1){
                $oldAddress = TAIKHOAN_DIACHI::find($oldDefault[0]->id);
                $oldAddress->macdinh = 0;
                $oldAddress->update();
            }
        }
        if($updateAddress->update()){
            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thành công',
                'data' =>  null
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Có lỗi xảy ra',
            'data' =>  null
        ]);
    }
    public function deleteMyAddress($id){
        $deleteAddress = TAIKHOAN_DIACHI::find($id);
        if($deleteAddress->delete()){
            return response()->json([
                'status' => true,
                'message' => "Xóa địa chỉ thành công",
                'data' => null
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Có lỗi xảy ra',
            'data' =>  null
        ]);
       
    }
    public function getMyAddressDefault($id){
        $myAddress = TAIKHOAN_DIACHI::where("macdinh", 1)->where("id_tk", $id)->get();
        $count = count($myAddress);
        if($count>0){
            return response()->json($myAddress[0]); 
        }else return response()->json(null); 
        
    }
    public function checkProductInStore($id, Request $request){
        $listValid= array();
        $listInvalid= array();
        $listDecrease = array();
        if($request->method=="store"){
            foreach(request('listID') as $pro){
                $exist = KHO::where('id_cn', $id)->where('id_sp', $pro['id'])->get();
                $count = count($exist);
                if($count == 0){
                    array_push($listInvalid, $pro['id']);
                }else if($exist[0]->slton==0){
                    array_push($listInvalid, $pro['id']);
                }else if($exist[0]->slton==1&&$pro['qty']==2){
                    array_push($listDecrease, $pro['id']);
                }else  array_push($listValid, $pro['id']);
            }
        }else{
            $check = true;
            $city = TAIKHOAN_DIACHI::find($id);
            $url ="TinhThanh.json" ;
            $datos = file_get_contents($url);
            $data = json_decode($datos, true);
            $data = collect($data)->where("Name", $city->tinhthanh)->all();
            if(reset($data)['ID'] < 48 ){
                $id_city = TINHTHANH::where('tentt', "Hà Nội")->get();
            }else{
                $id_city = TINHTHANH::where('tentt', "Hồ Chí Minh")->get();
            }
            $shop = CHINHANH::where('id_tt', $id_city[0]->id)->get();
            foreach(request('listID') as $pro){
                $exist = KHO::where('id_cn', $shop[0]->id)->where('id_sp', $pro['id'])->get();
                $count = count($exist);
                if($count == 0){
                    array_push($listInvalid, $pro['id']);
                    $check = false;
                }else if($exist[0]->slton==0){
                    array_push($listInvalid, $pro['id']);
                    $check = false;
                }else if($exist[0]->slton==1&&$pro['qty']==2){
                    $check = false;
                    array_push($listDecrease, $pro['id']);
                }else  array_push($listValid, $pro['id']);
            }
            
            if($check == false){
                if($city->tinhthanh == "Hồ Chí Minh"){
                    $id_city = TINHTHANH::where('tentt', "Hà Nội")->get();
                }else if($city->tinhthanh == "Hà Nội"){
                    $id_city = TINHTHANH::where('tentt', "Hồ Chí Minh")->get();
                }
                $shop = CHINHANH::where('id_tt', $id_city[0]->id)->get();
              
                $sizeInvalid = count($listInvalid); 
                $sizeDecrease= count($listDecrease); 
                for($i=0;$i<$sizeInvalid;$i++){
                    $exist = KHO::where('id_cn', $shop[0]->id)->where('id_sp', $listInvalid[$i])->get();
                    $count = count($exist);
                    $qty = 0;
                    foreach(request('listID') as $pro){
                        if($listInvalid[$i]==$pro['id']){
                            $qty = $pro['qty'];
                            break;
                        }
                    }
                    if($exist[0]->slton==1&&$qty==2){
                        array_push($listDecrease, $listInvalid[$i]);
                        unset($listInvalid[$i]);
                    }else if($exist[0]->slton>0){
                        unset($listInvalid[$i]);
                    }  
                }
                for($i=0;$i<$sizeDecrease;$i++){
                    $exist = KHO::where('id_cn', $shop[0]->id)->where('id_sp', $listDecrease[$i])->get();
                    $count = count($exist);
                    $qty = 0;
                    if($exist[0]->slton>=2){
                        unset($listDecrease[$i]);
                    }  
                }  
            }
        }
        return response()->json([
            'status' => true,
            'message' => '',
            'data' =>  ([
                "exist"=> $listValid,
                "nonExist"=> $listInvalid,
                "downOne"=> $listDecrease,
            ])
        ]);
    }
    public function getMyOrder($id, Request $request){
        if($request->state == "all"){
            $listOrder = DONHANG::orderBy("id", "desc")->where('id_tk', $id)->get();
        }else if($request->state == "Đã tiếp nhận"){
            $listOrder = DONHANG::orderBy("id", "desc")->where('trangthaidonhang', 'Đã tiếp nhận')->where('id_tk', $id)->get();
        }else if($request->state == "Đã xác nhận"){
            $listOrder = DONHANG::orderBy("id", "desc")->where('trangthaidonhang', 'Đã xác nhận')->where('id_tk', $id)->get();
        }else if($request->state == "Đang giao hàng"){
            $listOrder = DONHANG::orderBy("id", "desc")->where('trangthaidonhang', 'Đang giao hàng')->where('id_tk', $id)->get();
        }else if($request->state == "Thành công"){
            $listOrder = DONHANG::orderBy("id", "desc")->where('trangthaidonhang', 'Thành công')->where('id_tk', $id)->get();
        }
        else if($request->state == "Đã hủy"){
            $listOrder = DONHANG::orderBy("id", "desc")->where('trangthaidonhang', 'Đã hủy')->where('id_tk', $id)->get();
        }
        
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => $listOrder
        ]);
    }
    public function cancelOrder($id){
        $order = DONHANG::find($id);
        $detailOrder = CTDH::where('id_dh', $id)->get();
        if(!empty($order->id_vc)){
            $vc = TAIKHOAN_VOUCHER::where('id_vc', $order->id_vc)->where('id_tk', $order->id_tk)->get();
            $exist = count($vc);
            if($exist > 0){
                $voucher = TAIKHOAN_VOUCHER::find($vc[0]->id);
                $voucher->sl++;
                $voucher->update();
            }else{
                $voucher = new TAIKHOAN_VOUCHER();
                $voucher->id_tk = $order->id_tk;
                $voucher->id_vc = $order->id_vc;
                $voucher->sl = 1;
                $voucher->save();
            }
            
        }
        foreach($detailOrder as $detail){
            if(!empty($order->id_dh_dc)){
                $city = DONHANG_DIACHI::find($order->id_dh_dc);
                $url ="TinhThanh.json" ;
                $datos = file_get_contents($url);
                $data = json_decode($datos, true);
                $data = collect($data)->where("Name", $city->tinhthanh)->all();
                if(reset($data)['ID'] < 48 ){
                    $id_city = TINHTHANH::where('tentt', "Hà Nội")->get();
                }else{
                    $id_city = TINHTHANH::where('tentt', "Hồ Chí Minh")->get();
                }
                $shop = CHINHANH::where('id_tt', $id_city[0]->id)->get();
                $ware = KHO::where('id_cn', $shop[0]->id)->where('id_sp', $detail->id_sp)->get();
                $wareHouse = KHO::find($ware[0]->id);
                if($wareHouse->slton == 0){
                   
                    if($city->tinhthanh == "Hồ Chí Minh"){
                        $id_city = TINHTHANH::where('tentt', "Hà Nội")->get();
                    }else if($city->tinhthanh == "Hà Nội"){
                        $id_city = TINHTHANH::where('tentt', "Hồ Chí Minh")->get();
                    }
                    $shop = CHINHANH::where('id_tt', $id_city[0]->id)->get();
                    $ware = KHO::where('id_cn', $shop[0]->id)->where('id_sp', $detail->id_sp)->get();
                    $wareHouse = KHO::find($ware[0]->id);
                    $wareHouse->slton++;
                    if($detail->sl==2) $wareHouse->slton++;
                    $wareHouse->update();
                }else{
                    $wareHouse->slton++;
                    if($detail->sl==2) $wareHouse->slton++;
                    $wareHouse->update();
                }
                
            }else{
                $ware = KHO::where('id_cn', $order->id_cn)->where('id_sp', $detail->id_sp)->get();
                $wareHouse = KHO::find($ware[0]->id);
                $wareHouse->slton++;
                if($detail->sl==2) $wareHouse->slton++;
                $wareHouse->update();
            }
        }
        
        $order->trangthaidonhang = "Đã hủy";
        if($order->update()){
            return response()->json([
                'status' => true,
                'message' => 'Đã hủy thành công',
                'data' => null
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Có lỗi xảy ra',
            'data' => null
        ]);
    }
    public function getDetailOrder($id){
        $listProduct = [];
        $order = DONHANG::find($id);
        $detailOrder = CTDH::where("id_dh", $order->id)->get();
        if($order->hinhthuc=="Nhận hàng tại cửa hàng"){
            $user = TAIKHOAN::find($order->id_tk);
            $order->hoten = $user->hoten;
            $order->sdt =$user->sdt;
            $order->diachigiaohang = CHINHANH::find($order->id_cn)->diachi;
        }else{
            $infoAddress =  DONHANG_DIACHI::find($order->id_dh_dc);
            $order->diachigiaohang = $infoAddress->diachi.", ".$infoAddress->phuongxa.", ".$infoAddress->quanhuyen.", ".$infoAddress->tinhthanh;
            $order->hoten = $infoAddress->hoten;
            $order->sdt =$infoAddress->sdt;
        }
        if(!empty($order->id_vc)){
                $order->id_vc = (VOUCHER::find($order->id_vc)->chietkhau)*100;
        }
        foreach($detailOrder as $product){
            $pro = SANPHAM::find($product->id_sp);
            $product->name = $pro->tensp;
            $product->avatar = Helper::$URL."phone/".$pro->hinhanh;
            $product->color = $pro->mausac;
            $product->storage = $pro->dungluong;
            if(!empty($pro->id_km)){
                $product->discount = KHUYENMAI::find($pro->id_km)->chietkhau;
            }else  $product->discount = 0 ;
            $product->price = $pro->gia-($pro->gia*$product->discount);
            $product->priceRoot =  $pro->gia;
            $product->isAvailable = true;
            $product->isQtyAvailable = true;
            array_push($listProduct, (["product"=>$product,"qty"=>$product->sl]));
        }
        return response()->json([
            'status' => true,
            'message' => 'Đã hủy thành công',
            'data' =>  ([
                "infoOrder"=> $order,
                "listProduct"=> $listProduct,
            ])
        ]);
    }
    public function updateQueue(Request $request){
        if($request->action){
            if($request->action == "add"){
                $oldQueues = HANGDOI::where('id_tk', $request->id_tk)->get();
                $size = count($oldQueues);
                if($size > 0){
                    if($oldQueues[0]->trangthai == 0){
                        $oldQueue = HANGDOI::find($oldQueues[0]->id);
                        $oldQueue->trangthai = 1;
                        $oldQueue->nentang = "app";
                        $oldQueue->update();
                        return response()->json([
                            'status' => true,
                            'message' => '',
                            'data' => null
                        ]);
                    }else{
                        return response()->json([
                            'status' => false,
                            'message' => '',
                            'data' => null
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => true,
                        'message' => '',
                        'data' => null
                    ]);
                }
                
            }else if($request->action == "delete"){
                $queues = HANGDOI::where('id_tk', $request->id_tk)->get();
                if($queues){
                    $cthd = CTHD::where('id_hd', $queues[0]->id)->delete();
                    $queue = HANGDOI::find($queues[0]->id);
                    $queue->delete();
                    return response()->json([
                        'status' => true,
                        'message' => '',
                        'data' => null
                    ]);
                }
               
            }
        }
        //cung user -> dang thanh toan
        //khac user -> con hang? het hang?
    }
    public function checkID($sizeIDs, $idCheck){
        foreach($sizeIDs as $id){
            if($id == $idCheck){
                return true;
            }
        }
        return false;
    }
    public function checkProductInQueue($id, Request $request){
        $queues = HANGDOI::where('trangthai', 1)->get();
        $myCart = GIOHANG::where('id_tk', $id)->get();
        $totalPro = array();
        $listIDs = array();
        $listIDsException = json_decode($request->listIDsException, true);
        $sizeIDs = count($listIDsException);
        foreach($myCart as $mCart){ 
            $total = 0;
            if($sizeIDs > 0){
                if(!$this->checkID($listIDsException, $mCart->id_sp)){
                    $otherCart = CTHD::where('id_sp', $mCart->id_sp)->get();
                    foreach($otherCart as $oCart){ 
                            $total += $oCart->sl;
                    }
                    if($total > 0){
                        array_push($totalPro,  (object)["id" => $mCart->id_sp, "total" => $total + $mCart->sl]);
                    }
                }
            }else{
                $otherCart = CTHD::where('id_sp', $mCart->id_sp)->get();
                foreach($otherCart as $oCart){ 
                    $total += $oCart->sl;
                }
                if($total > 0){
                    array_push($totalPro,  (object)["id" => $mCart->id_sp, "total" => $total + $mCart->sl]);
                }
            }
            
        }
        $check = false;
        foreach($totalPro as $product){
            $wares = KHO::where('id_sp', $product->id)->get();
            $total = 0;
            foreach($wares as $ware){ 
                $total += $ware->slton;
            }
            if($product->total >= $total){
                array_push($listIDs, $product->id);
                $check = true;
            }
        }
        if($check == true){
            return response()->json([
                'status' => false,
                'message' => '',
                'data' => $listIDs
            ]);
        }else{
            $queue = HANGDOI::where('id_tk', $id)->get();
            $size = count($queue);
            if($size == 0){
                $newQueue = new HANGDOI();
                $newQueue->id_tk = $id;
                $newQueue->trangthai = 1;
                $newQueue->timestamp = time();
                $newQueue->nentang = "app";
                $newQueue->save();
                if($sizeIDs > 0){
                    foreach($myCart as $cart){
                        if(!$this->checkID($listIDsException, $cart->id_sp)){
                            $detailQueue = new CTHD();
                            $detailQueue->id_hd = $newQueue->id;
                            $detailQueue->id_sp = $cart->id_sp;
                            $detailQueue->sl = $cart->sl;
                            $detailQueue->save();
                        }
                    }
                }else {
                    foreach($myCart as $cart){
                        $detailQueue = new CTHD();
                        $detailQueue->id_hd = $newQueue->id;
                        $detailQueue->id_sp = $cart->id_sp;
                        $detailQueue->sl = $cart->sl;
                        $detailQueue->save();
                    } 
                }
               
            }
         
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => null
            ]);
        }
       
    }
    //thanh toán -> *ktra xem có ai đang thanh toán cùng sản phẩm với mình không -> **ktra soluong ton cua người đang tt + sl hien tai < kho -> dc thanh toan
    //* lấy tất cả sp trog gio hang của mình
    //* duyet tất cả user trong hàng đợi
        //* lấy tất cả sp trog giỏ hàng của từng user đang thanh toán
            //* so sanh voi id_sp trong gio hang cua minh
                //* cùng nhau -> cộng số lượng
    //checkbox: bỏ chọn 1 số sp trong giỏ hàng?
        //checkbox = FALSE: ĐC QUYÊN THANH TOÁN
}
