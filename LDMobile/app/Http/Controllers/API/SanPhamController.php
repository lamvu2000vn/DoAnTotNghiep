<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;
use App\Models\SANPHAM;
use App\Models\MAUSP;
use App\Models\NHACUNGCAP;
use App\Models\SLIDESHOW_CTMSP;
use App\Models\DANHGIASP;
use App\Models\PHANHOI;
use App\Models\SLIDESHOW;
use App\Models\KHUYENMAI;
use App\Models\DONHANG;
use App\Models\CTDH;
use App\Models\LUOTTHICH;
use App\Models\CTDG;
use App\Models\BAOHANH;
use App\Models\IMEI;
use App\Models\THONGBAO;
use App\Models\TAIKHOAN;
use App\Models\SP_YEUTHICH;
use App\Models\KHO;
use Carbon\Carbon;
use App\Classes\Helper;
use App\Events\sendNotification;
class SanPhamController extends Controller
{
    //
    public function __construct(){
        $this->IndexController = new IndexController;
    }
    public function getSupplier(){
        $supplier = NHACUNGCAP::all();
        $count =count($supplier);
        for($i=0;$i<$count;$i++){
         $supplier[$i]->anhdaidien = Helper::$URL."logo/".$supplier[$i]->anhdaidien;
        }
        return response()->json([
            'status' => true,
            'message' => '',
    		'data' => $supplier
    	]);
    }
    public function getSlideshow(){
        $slideshow = SLIDESHOW::all();
        $count = count($slideshow);
        for($i=0;$i<$count;$i++){
            $slideshow[$i]->hinhanh = Helper::$URL."slideshow/".$slideshow[$i]->hinhanh;
        }
        return response()->json([
            'status' => true,
            'message' => '',
    		'data' => $slideshow
    	]);
    }
    public function getBanner(Request $request){
        
    }
    public function getVoucherHot(){
        
    }
    public function getHotSale(){
        $content = "";
        $vote = -1;
        $time ="";
        $idUser = -1;
        $listProductHotSale = array();
        $listProduct = SANPHAM::where('trangthai', 1)->get();
        $listDiscount = KHUYENMAI::orderBy("chietkhau", "desc")->get();
        $max = 0; 
        $min = 0;
        $count =  count($listDiscount);

        if($count >= 2){
            $max = $listDiscount[0]->id;
            $min = $listDiscount[1]->id;
        }else{
            $max = $listDiscount[0]->id;
        }
        
        //ktra chi lay san pham khac dung luong
        foreach($listProduct as $product){
            $valid = false;
            if($product->id_km == $max || $product->id_km == $min){
               if(!empty($listProductHotSale)){
                foreach($listProductHotSale as $productHotSale){
                    if($product->id_msp == $productHotSale->id_msp){
                        if($product->dungluong != $productHotSale->dungluong){
                            $valid = true;
                        }else $valid = false;  //truong hop cung mau nhung cung dung luong. neu khong set lai = false thi se bang true o lan lap truoc
                    }else{
                        $valid = true; //truong hop khac mau
                    }
                }
               }else array_push($listProductHotSale, $product);
                
               if($valid == true){
                    array_push($listProductHotSale, $product);
                }
           }
        }
        foreach($listProductHotSale as $product){
            $listId = array();
            $product->tensp = $product->tensp." ".$product->dungluong;
            $product->hinhanh = Helper::$URL."phone/".$product->hinhanh;
            if(!empty(KHUYENMAI::find($product->id_km)->chietkhau))
            {
               $product->giamgia = KHUYENMAI::find($product->id_km)->chietkhau;
            } else $product->giamgia = 0;
            $temp = SANPHAM::where('id_msp', $product->id_msp)->where('dungluong', $product->dungluong)->get();
            foreach($temp as $pro){
                array_push($listId,  $pro->id);
            }
            $allJudge = DANHGIASP::whereIn("id_sp",  $listId)->get();
            $totalVote = 0;
            $totalJudge = 0;
            foreach($allJudge as $judge){
                if($judge->id_tk != $idUser){
                    $totalVote += $judge->danhgia;
                    $time =$judge->thoigian;
                    $idUser = $judge->id_tk;
                    $totalJudge++;
                   }else{
                       if($judge->thoigian != $time){
                        $totalVote += $judge->danhgia;
                        $totalJudge++; 
                        $time = $judge->thoigian;
                        $content = $judge->noidung;
                        $idUser = $judge->id_tk;
                       }  
                                   
                   }
            }
            $product->tongluotvote = $totalVote;
            $product->tongdanhgia =  $totalJudge;
            
            
        }
        return response()->json([
            'status' => true,
            'message' => '',
    		'data' => $listProductHotSale
    	]);
    }
    public function getAllProduct(Request $request){
        $vote = -1;
        $time ="";
        $idUser = -1;
        $page = !empty($request->page) ? $request->page : 1;
    	$itemsPerPage = !empty($request->per_page) ? $request->per_page : 5;
        $listProduct = SANPHAM::inRandomOrder()->where('trangthai', 1)->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy()->get();
        
        foreach($listProduct as $product){
            $product->tensp = $product->tensp." ".$product->dungluong;
            $product->hinhanh = Helper::$URL."phone/".$product->hinhanh;
            if(!empty(KHUYENMAI::find($product->id_km)->chietkhau))
            {
               $product->giamgia = KHUYENMAI::find($product->id_km)->chietkhau;
            } else $product->giamgia = 0;
            $allJudge = DANHGIASP::where("id_sp", $product->id)->get();
            $totalVote = 0;
            $totalJudge = 0;
            foreach($allJudge as $judge){
                if($judge->id_tk != $idUser){
                    $totalVote += $judge->danhgia;
                    $time =$judge->thoigian;
                    $idUser = $judge->id_tk;
                    $totalJudge++;
                   }else{
                       if($judge->thoigian != $time){
                        $totalVote += $judge->danhgia;
                        $totalJudge++; 
                       }  
                        $time = $judge->thoigian;
                        $content = $judge->noidung;
                        $idUser = $judge->id_tk;           
                   }
            }
            $product->tongluotvote = $totalVote;
            $product->tongdanhgia =  $totalJudge;
        }
        return response()->json([
            'status' => 'true',
            'message' => '',
    		'data' => $listProduct
    	]); 
    }
    public function getFeaturedProduct(){
        $vote = -1;
        $time ="";
        $idUser = -1;
        $listProductNew = array();
        $totalProductLeft= 10;
        $listProductOrderBy = SANPHAM::where('trangthai', 1)->orderBy('id',"desc")->get();

        //ktra chi lay san pham khac dung luong
        foreach($listProductOrderBy as $product){
            $valid = false;
            if(!empty($listProductNew)){
                foreach($listProductNew as $productNew){
                    if($product->id_mausp == $productNew->id_mausp){
                        if($product->dungluong != $productNew->dungluong){
                                $valid = true;
                            }else $valid = false;  //truong hop cung mau nhung cung dung luong. neu khong set lai = false thi se bang true o lan lap truoc
                        }else{
                            $valid = true; //truong hop khac mau
                    }
                }
            }else {
                array_push($listProductNew, $product);
                $totalProductLeft--;
            }
            if($valid == true){
                array_push($listProductNew, $product);
                $totalProductLeft--;
            }
            if($totalProductLeft == 0){
                break; 
            }
        }

        foreach($listProductNew as $product){
            $product->tensp = $product->tensp." ".$product->dungluong;
            $product->hinhanh = Helper::$URL."phone/".$product->hinhanh;
            if(!empty(KHUYENMAI::find($product->id_km)->chietkhau))
            {
               $product->giamgia = KHUYENMAI::find($product->id_km)->chietkhau;
            } else $product->giamgia = 0;
            $allJudge = DANHGIASP::where("id_sp", $product->id)->get();
            $totalVote = 0;
            $totalJudge = 0;
            foreach($allJudge as $judge){
                if($judge->id_tk != $idUser){
                    $totalVote += $judge->danhgia;
                    $time =$judge->thoigian;
                    $idUser = $judge->id_tk;
                    $totalJudge++;
                   }else{
                       if($judge->thoigian != $time){
                        $totalVote += $judge->danhgia;
                        $totalJudge++; 
                       }  
                        $time = $judge->thoigian;
                        $content = $judge->noidung;
                        $idUser = $judge->id_tk;           
                   }
            }
            $product->tongluotvote = $totalVote;
            $product->tongdanhgia =  $totalJudge;
        }

        return response()->json([
            'status' => 'true',
            'message' => '',
    		'data' => $listProductNew
    	]);
    }
    public function getDetailProduct($id, Request $request){
        $color = array();
        $storage = array();
        $images = array();
        $listID = array();
        $productCurrent = SANPHAM::find($id);
        array_push($images,  Helper::$URL."phone/".$productCurrent->hinhanh);
        $cateProduct = MAUSP::find($productCurrent->id_msp);
        $product = SANPHAM::where('id_msp', $cateProduct->id)->get();
        $nhacungcap = NHACUNGCAP::find($cateProduct->id_ncc);
        $nhacungcap->anhdaidien = Helper::$URL."logo/".$nhacungcap->anhdaidien;
        $checkWarehouse = 0; // Hang sap ve
        $dem = count($product);
        for($i=0;$i<$dem;$i++){
            if($this->checkArray($color, $product[$i]->mausac)){
                array_push($color, $product[$i]->mausac);
            }
            if($this->checkArray($storage, $product[$i]->dungluong)){
                 array_push($storage, $product[$i]->dungluong);
            }
            if($this->checkArray($images,  Helper::$URL."phone/".$product[$i]->hinhanh)){
                array_push($images,  Helper::$URL."phone/".$product[$i]->hinhanh);
            }
            //kho
            array_push($listID,  $product[$i]->id);
            
        }
        //kho
        $warehouses = KHO::whereIn('id_sp', $listID)->get();
        $count = count($warehouses);
        if($count > 0){
            $qty = 0;
            foreach($warehouses as $warehouse){
                $qty +=  $warehouse->slton;
            }
            $checkWarehouse = 1; //Con hang
            if($qty == 0){
                $checkWarehouse = 2; //Tam het hang
            }   
        }
        
        $cateProduct->nhacungcap = $nhacungcap;
        $cateProduct->mausac = $color;
        $cateProduct->dungluong = $storage;
        $cateProduct->dsHinhAnh = $images;
        $cateProduct->trangthai =  $checkWarehouse;
        $wish = SP_YEUTHICH::where('id_tk', $request->id_tk)->where('id_sp', $id)->get();
        $count = count($wish);

        if($count > 0){
            $cateProduct->like = true;
        }else  $cateProduct->like = false;
        return response()->json([
            'status' => true,
            'message' => '',
    		'data' => $cateProduct
    	]);
    }

    public function checkArray(array $array, String $string){
        $dem = count($array);
        for($i=0;$i<$dem;$i++){
            if($array[$i] == $string){
                return false;
            }
        }
        return true;
    }

    public function changeColorOrStorageProduct($id, Request $request){
            if(!empty($request->mausac) && !empty($request->dungluong)){
                $product = SANPHAM::where("id_msp", $id)->where("mausac","like",$request->mausac)->where("dungluong",$request->dungluong)->where('trangthai', 1)->get();
                $count = count($product);
                
            }else if(!empty($request->mausac)){
                $product = SANPHAM::where("id_msp",$id)->where("mausac","like", $request->mausac)->where('trangthai', 1)->get();
                $count = count($product);
    
            }else if(!empty($request->dungluong)){
                $product = SANPHAM::where("id_msp", $id)->where("dungluong","like", $request->dungluong)->where('trangthai', 1)->get();
                $count = count($product);
               
            }
            foreach($product as $pro){
                $pro->hinhanh = Helper::$URL."phone/".$pro->hinhanh;
                if(!empty(KHUYENMAI::find($pro->id_km)->chietkhau))
                {
                    $pro->giamgia = KHUYENMAI::find($pro->id_km)->chietkhau;
                } else  $pro->giamgia = 0;
              
                $allJudge = DANHGIASP::where("id_sp", $pro->id)->get();
                $totalVote = 0;
                foreach($allJudge as $judge){
                    $totalVote += $judge->danhgia;
                }
                $pro->tongluotvote = $totalVote;
                $pro->tongdanhgia = count($allJudge);
                $wish = SP_YEUTHICH::where('id_tk', $request->id_tk)->where('id_sp', $pro->id)->get();
                $count = count($wish);
                if($count > 0){
                    $pro->like = true;
                }else  $pro->like = false;
                
            }
            
            return response()->json([
                'status' => 'true',
                'message' => '',
                'data' => $product
            ]);
    }
    public function getRelatedProduct($id){
        $listIdCateFirst = array();
        $listIdCateFinish = array();
        $listIdCateResult = array();
        $listIdProductFirst = array();
        $listIdProductFinish = array();
        $listIdProductResult = array();
        $vote = -1;
        $time ="";
        $idUser = -1;
        $product = SANPHAM::find($id);
        $cateProduct = MAUSP::find($product->id_msp);
        $listCateRelated = MAUSP::where("id_ncc",  $cateProduct->id_ncc)->where('id', '!=', $product->id_msp)->get();
        foreach($listCateRelated as $cate){
            array_push($listIdCateFirst, $cate->id);
        }
        $listIdCateFinish = array_rand($listIdCateFirst, 5);
        foreach($listIdCateFinish as $cate){
            array_push($listIdCateResult, $listIdCateFirst[$cate]);
        }
        //random lan 2
        $listProduct = SANPHAM::whereIn("id_msp", $listIdCateResult)->where('trangthai', 1)->get();
        
        foreach($listProduct as $pro){
            array_push($listIdProductFirst, $pro->id);
        }
        $listIdProductFinish = array_rand($listIdProductFirst, 5);
        foreach($listIdProductFinish as $cate){
            array_push($listIdProductResult, $listIdProductFirst[$cate]);
        }
        $listResult = SANPHAM::whereIn('id', $listIdProductResult)->where('trangthai', 1)->get();
       
        foreach($listResult as $pro){
            $pro->hinhanh = Helper::$URL."phone/".$pro->hinhanh;
            if(!empty(KHUYENMAI::find($pro->id_km)->chietkhau))
            {
                $pro->giamgia = KHUYENMAI::find($pro->id_km)->chietkhau;
            }else  $pro->giamgia = 0;
            $allJudge = DANHGIASP::where("id_sp", $pro->id)->get();
            $totalVote = 0;
            $totalJudge = 0;
            foreach($allJudge as $judge){
                if($judge->id_tk != $idUser){
                    $totalVote += $judge->danhgia;
                    $time =$judge->thoigian;
                    $idUser = $judge->id_tk;
                    $totalJudge++;
                   }else{
                       if($judge->thoigian != $time){
                        $totalVote += $judge->danhgia;
                        $totalJudge++; 
                       }  
                        $time = $judge->thoigian;
                        $content = $judge->noidung;
                        $idUser = $judge->id_tk;           
                   }
            }
            $pro->tongluotvote = $totalVote;
            $pro->tongdanhgia =  $totalJudge;
        }
        return response()->json([
            'status' => 'true',
            'message' => '',
            'data' => $listResult
        ]);
    }

    public function getCompareProduct($id, Request $request){
        $listResult = array();
        $vote = -1;
        $time ="";
        $idUser = -1;
        $price = $request->price;
        $listProduct = SANPHAM::where('id_msp','!=',$id)->where('trangthai', 1)->where(function($query) use ($price){
            $query->where('gia','<=',$price+1000000);
            $query->where('gia','>=',$price-1000000);
        })->get();
        foreach($listProduct as $product){
            $count = false;
            if(!empty($listResult)){
                foreach($listResult as $pro){
                    if($product->id_msp == $pro->id_msp){
                        $count = true;
                    }
                }

                if($count == false ){
                    array_push($listResult, $product);
                }
            }else {
                array_push($listResult, $product);
            }
        }
        foreach($listResult as $pro){
            $pro->hinhanh = Helper::$URL."phone/".$pro->hinhanh;
            if(!empty(KHUYENMAI::find($pro->id_km)->chietkhau)){
                $pro->giamgia = KHUYENMAI::find($pro->id_km)->chietkhau;
            }else $pro->giamgia = 0;
            $allJudge = DANHGIASP::where("id_sp", $pro->id)->get();
            $totalVote = 0;
            $totalJudge = 0;
            foreach($allJudge as $judge){
                if($judge->id_tk != $idUser){
                    $totalVote += $judge->danhgia;
                    $time =$judge->thoigian;
                    $idUser = $judge->id_tk;
                    $totalJudge++;
                   }else{
                       if($judge->thoigian != $time){
                        $totalVote += $judge->danhgia;
                        $totalJudge++; 
                       }  
                        $time = $judge->thoigian;
                        $content = $judge->noidung;
                        $idUser = $judge->id_tk;           
                   }
            }
            $pro->tongluotvote = $totalVote;
            $pro->tongdanhgia = count($allJudge);
        }
        return response()->json([
            'status' => 'true',
            'message' => '',
            'data' => $listResult
        ]);
    }
    public function getSlideShowOfProduct($id){
        $listResult = array();
        $product = SANPHAM::find($id);
        $id_msp = $product->id_msp;
        $listSlideShow = SLIDESHOW_CTMSP::where('id_msp', $id_msp)->get();
        foreach($listSlideShow as $product){
            array_push($listResult, Helper::$URL."phone/slideshow/".$product->hinhanh);
        }
        return response()->json([
            'status' => 'true',
            'message' => '',
            'data' => $listResult
        ]);
    }
    public function getRamAndStorage(){
        $listRam  = array();
        $listStorage = array();
        $ram = SANPHAM::where('trangthai', 1)->orderBy('ram','desc')->select('ram')->distinct()->get();
        $storage =  SANPHAM::where('trangthai', 1)->orderBy('dungluong','desc')->select('dungluong')->distinct()->get();
        $suppliers = NHACUNGCAP::all();
        foreach($ram as $s){
            array_push($listRam, $s->ram);
        }
        foreach($storage as $r){
            array_push($listStorage, $r->dungluong);
        }
        foreach($suppliers as $supplier){
            $supplier->anhdaidien = Helper::$URL."logo/".$supplier->anhdaidien;
        }
        return response()->json([
            'status' => 'true',
            'message' => '',
            'data' => ([
                "ram"=>$listRam,
                "dungluong"=>$listStorage,
                "nhacungcap"=>$suppliers
                ])
        ]);
    }
   
    public function checkComment($id, Request $request){
        $listId = array();
        $listResults = array();
        $listOrder = DONHANG::where('id_tk', $id)->get();
        $product1 = SANPHAM::find($request->idProduct);
        foreach($listOrder as $order){
            $detailOrder = CTDH::where('id_dh', $order->id)->get();
            foreach($detailOrder as $detail){
                $product2 = SANPHAM::find($detail->id_sp);
                $review = DANHGIASP::orderBy("id", "desc")->where('id_sp', $detail->id_sp)->get();
                $sizeReview = count($review);
                if($sizeReview>0){
                    if($order->thoigian>$review[0]->thoigian){
                        if($detail->id_sp == $request->idProduct){
                                if($this->checkId($listId,$detail->id_sp)){
                                    array_push($listId, $detail->id_sp);
                                } 
                        
                        }else if($product1->id_msp == $product2->id_msp){
                            if($product1->dungluong ==  $product2->dungluong){
                                if($this->checkId($listId, $detail->id_sp)){
                                    array_push($listId, $detail->id_sp);
                                }
                            }
                        }
                    }
                }else{
                    if($detail->id_sp == $request->idProduct){
                        if($this->checkId($listId,$detail->id_sp)){
                            array_push($listId, $detail->id_sp);
                        } 
                
                     }else if($product1->id_msp == $product2->id_msp){
                    if($product1->dungluong ==  $product2->dungluong){
                        if($this->checkId($listId, $detail->id_sp)){
                            array_push($listId, $detail->id_sp);
                        }
                    }
                }
                }
                
            }
        }
        $listReview = DANHGIASP::where('id_tk', $id)->get();
        $sizeListId = count($listId);
            for($i=0;$i<$sizeListId;$i++){
                $check = true;
                foreach($listReview as $review){
                        if($listId[$i] == $review->id_sp){
                            $check = false;
                        }
                }
                if($check == true){
                    array_push($listResults, $listId[$i]);
                }
            }
            $count = count($listResults);
            if($sizeListId > 0){
                return response()->json([
                    'status' => true,
                    'message' => '',
                    'data' => $listId,
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => '',
                'data' => null,
            ]);
                
    }
    public function checkId($listID, $id1){
        foreach($listID as $id){
            if($id == $id1){
                return false;
            }
        }
        return true;
    }

    public function getInfoProductByListID(Request $request){
        $listProduct = SANPHAM::whereIn('id', Request('listID'))->where('trangthai', 1)->get();
        foreach($listProduct as $product){
            $product->hinhanh = Helper::$URL."phone/".$product->hinhanh;
        }
        return response()->json([
            'status' => 'true',
            'message' => '',
            'data' => $listProduct
        ]);
    }
    public function getProductFilter(Request $request){
        $vote = -1;
        $time ="";
        $idUser = -1;
        $page = !empty($request->page) ? $request->page : 1;
    	$itemsPerPage = !empty($request->per_page) ? $request->per_page : 5;
        $priceMax = $request->priceMax;
        $priceMin = $request->priceMin;
        $suppliers = json_decode($request->suppliers, true);
        if(!empty($priceMax)&&!empty($request->ram)&&!empty($request->dungluong)&&!empty($priceMin)&&!empty($suppliers)){
            $listProduct = SANPHAM::where('ram', $request->ram)->where('dungluong', $request->dungluong)->where('trangthai', 1)->where(function($query) use ($priceMax, $priceMin, $suppliers){
                $query->where('gia','<=',$priceMax);
                $query->where('gia','>=',$priceMin);
                $query->whereIn('id_msp',  function($query1) use ($suppliers){
                    $query1->select('id')
                        ->from('mausp')
                        ->whereIn('id_ncc', $suppliers);
                });
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($priceMax)&&!empty($request->ram)&&!empty($request->dungluong)&&!empty($priceMin)){
            $listProduct = SANPHAM::where('ram', $request->ram)->where('dungluong', $request->dungluong)->where('trangthai', 1)->where(function($query) use ($priceMax, $priceMin){
                $query->where('gia','<=',$priceMax);
                $query->where('gia','>=',$priceMin);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($request->ram)&&!empty($request->dungluong)&&!empty($suppliers)){
            $listProduct = SANPHAM::where('ram', $request->ram)->where('dungluong', $request->dungluong)->where('trangthai', 1)->whereIn('id_msp', function($query) use ($suppliers){
                $query->select('id')
                        ->from('mausp')
                        ->whereIn('id_ncc', $suppliers);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }
        else if(!empty($priceMax)&&!empty($request->ram)&&!empty($request->dungluong)){
            $listProduct = SANPHAM::where('ram', $request->ram)->where('dungluong', $request->dungluong)->where('trangthai', 1)->where(function($query) use ($priceMax){
                $query->where('gia','<=',$priceMax);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($priceMin)&&!empty($priceMax)){
            $listProduct = SANPHAM::where(function($query) use ($priceMax, $priceMin){
                $query->where('gia','<=',$priceMax);
                $query->where('gia','>=',$priceMin);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }
        else if(!empty($priceMax)&&!empty($request->ram)){
            $listProduct = SANPHAM::where('ram', $request->ram)->where('trangthai', 1)->where(function($query) use ($priceMax){
                $query->where('gia','<=',$priceMax);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($priceMax)&&!empty($request->dungluong)){
            $listProduct = SANPHAM::where('dungluong', $request->dungluong)->where(function($query) use ($priceMax){
                $query->where('gia','<=',$priceMax);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($priceMin)&&!empty($request->ram)&&!empty($request->dungluong)){
            $listProduct = SANPHAM::where('ram', $request->ram)->where('dungluong', $request->dungluong)->where('trangthai', 1)->where(function($query) use ($priceMin){
                $query->where('gia','>=',$priceMin);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($priceMin)&&!empty($request->ram)){
            $listProduct = SANPHAM::where('ram', $request->ram)->where('trangthai', 1)->where(function($query) use ($priceMin){
                $query->where('gia','>=',$priceMin);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($priceMin)&&!empty($request->dungluong)){
            $listProduct = SANPHAM::where('dungluong', $request->dungluong)->where('trangthai', 1)->where(function($query) use ($priceMin){
                $query->where('gia','>=',$priceMin);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($request->ram)&&!empty($request->dungluong)){
            $listProduct = SANPHAM::where('ram', $request->ram)->where('dungluong', $request->dungluong)->where('trangthai', 1)->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($request->ram)){
            $listProduct = SANPHAM::where('ram', $request->ram)->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();;
        }else if(!empty($request->dungluong)){
            $listProduct = SANPHAM::where('dungluong', $request->dungluong)->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($priceMax)){
            $listProduct = SANPHAM::where('trangthai', 1)->where(function($query) use ($priceMax){
                $query->where('gia','<=',$priceMax);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($priceMin)){
            $listProduct = SANPHAM::where('trangthai', 1)->where(function($query) use ($priceMin){
                $query->where('gia','>=',$priceMin);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }else if(!empty($suppliers)){
            $listProduct = SANPHAM::where('trangthai', 1)->whereIn('id_msp',function($query) use ($suppliers){
                $query->select('id')
                        ->from('mausp')
                    ->whereIn('id_ncc', $suppliers);
            })->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->groupBy('id_msp')->get();
        }
        foreach($listProduct as $product){
            $product->tensp = $product->tensp." ".$product->dungluong;
            $product->hinhanh = Helper::$URL."phone/".$product->hinhanh;
            if(!empty(KHUYENMAI::find($product->id_km)->chietkhau)){
                $product->giamgia = KHUYENMAI::find($product->id_km)->chietkhau;
           } else $product->giamgia = 0;
            
            $allJudge = DANHGIASP::where("id_sp", $product->id)->get();
            $totalVote = 0;
            $totalJudge = 0;
            foreach($allJudge as $judge){
                if($judge->id_tk != $idUser){
                    $totalVote += $judge->danhgia;
                    $time =$judge->thoigian;
                    $idUser = $judge->id_tk;
                    $totalJudge++;
                   }else{
                       if($judge->thoigian != $time){
                        $totalVote += $judge->danhgia;
                        $totalJudge++; 
                       }  
                        $time = $judge->thoigian;
                        $content = $judge->noidung;
                        $idUser = $judge->id_tk;           
                   }
            }
            $product->tongluotvote = $totalVote;
            $product->tongdanhgia = count($allJudge);
        }
        return response()->json([
            'status' => 'true',
            'message' => '',
            'data' => $listProduct
        ]);
    }

    public function getInfoProductByColorAndStorage(Request $request){
        $image = str_replace(Helper::$URL.'phone/','',$request->hinhanh);
        $pro = SANPHAM::where('hinhanh', $image)->where('dungluong', $request->dungluong)->get();
        if(!empty($pro)){
            $product = SANPHAM::find($pro[0]->id);
            $product->hinhanh = Helper::$URL."phone/".$product->hinhanh;
            if(!empty(KHUYENMAI::find($product->id_km)->chietkhau)){
                $product->giamgia = KHUYENMAI::find($product->id_km)->chietkhau;
             }else $product->giamgia = 0;
           return response()->json([
            'status' => 'true',
            'message' => '',
            'data' => $product
            ]);
        }
        
    }

    public function getComment($id, Request $request){
        $content = "";
        $temp = 0;
        $time ="";
        $idUser = -1;
        $listResult = array();
        $product = SANPHAM::find($id);
        $listProduct = SANPHAM::where('id_msp', $product->id_msp)->where('dungluong', $product->dungluong)->select('id')->pluck('id')->all();
       $listComment = DANHGIASP::whereIn('id_sp', $listProduct)->orderBy('id',"desc")->get();
        
        foreach($listComment as $comment){
            $listAttachment = CTDG::Where('id_dg', $comment->id)->get();
            foreach($listAttachment as $attachemnt){
                $attachemnt->hinhanh = Helper::$URL."evaluate/". $attachemnt->hinhanh;
            }
            $comment->dsHinhAnh = $listAttachment;
            $listReply = PHANHOI::where('id_dg', $comment->id)->orderBy('id',"desc")->take(5)->get();
            $totalReply = count(PHANHOI::where('id_dg',$comment->id)->get());
            foreach($listReply as $reply){
                $user = TAIKHOAN::find($reply->id_tk);
                if($user->htdn =="normal"){
                    $reply->anhdaidien = Helper::$URL.'user/'.$user->anhdaidien;
                }else $reply->anhdaidien = $user->anhdaidien;
                $reply->hoten = $user->hoten;
            }
            $comment->dsPhanHoi = $listReply;
            $comment->soluottraloi = $totalReply;
            $comment->thoigian = Carbon::createFromFormat('d/m/Y H:i:s',$comment->thoigian)->format('d/m/Y H:i');
            $usercomment = TAIKHOAN::find($comment->id_tk);
            if($usercomment->htdn =="normal"){
                $comment->anhdaidien = Helper::$URL."user/".$usercomment->anhdaidien;
            }else $comment->anhdaidien = $usercomment->anhdaidien;
            $comment->hoten = $usercomment->hoten;
            $product =  SANPHAM::find($comment->id_sp);
            $comment->mausac = $product->mausac;
            $comment->dungluong =  $product->dungluong;
        }
        $size = count($listComment);
        for($i=0;$i<$size;$i++){
             if($listComment[$i]->id_tk != $idUser){
                    array_push($listResult, $listComment[$i]);
                    $time = $listComment[$i]->thoigian;
                    $idUser = $listComment[$i]->id_tk;
                 }else{
                   if($listComment[$i]->thoigian == $time){
                    $count = count($listResult);
                    $listResult[$count - 1]->mausac = $listResult[$count - 1]->mausac.", ".$listComment[$i]->mausac;
                    $sizeAttachment = count($listComment[$i]->dsHinhAnh);
                    if($sizeAttachment>0){
                        $listResult[$count - 1]->dsHinhAnh = $listComment[$i]->dsHinhAnh;
                    }
    
                    $time = $listComment[$i]->thoigian;
                    $idUser = $listComment[$i]->id_tk;
                   }else{
                    array_push($listResult, $listComment[$i]);
                    $time = $listComment[$i]->thoigian;
                    $idUser = $listComment[$i]->id_tk;
                   }   
                }
        }

       foreach($listResult as $result){
            $liked = LUOTTHICH::where('id_tk', $request->id_tk)->where('id_dg', $result->id)->get();
            $size = count($liked);
            if($size>0){
                $result->like = true;
            }else $result->like = false;
            $result->soluotthich = count(LUOTTHICH::where('id_dg', $result->id)->get());
       }
      
        return response()->json([
            'status' => 'true',
            'message' => '',
            'data' => $listResult
        ]);
    }
    public function getReply($id){
        $listReply = PHANHOI::where('id_dg',$id)->get();
        foreach($listReply as $reply){
            $user = TAIKHOAN::find($reply->id_tk);
            if($user->htdn =="normal"){
                $user->anhdaidien = Helper::$URL.'user/'.$user->anhdaidien;
            }
            $reply->anhdaidien = $user->anhdaidien;
            $reply->hoten = $user->hoten;
        }
        return response()->json([
            'status' => 'true',
            'message' => '',
            'data' => $listReply
        ]);
    }
    public function postComment(Request $request){
        $idFirst = 0;
        $check = true;
        foreach(request('listID') as $id){
            $comment = new DANHGIASP();
            $comment->id_tk = request('id_tk');
            $comment->id_sp = $id;
            $comment->noidung = request('noidung');
            $comment->thoigian = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
            $comment->soluotthich = 0;
            $comment->danhgia = request('danhgia');
            $comment->chinhsua = false;
            $comment->save();
            if($check==true){
                $idFirst = $comment->id;
                $check = false;
            } 
        }
        
        return response()->json([
                'status' => true,
                'message' => 'Thành công',
                'data' =>  $idFirst
        ]);
               
    }
    public function updateComment($id, Request $request){
        $check = true;
        $comment = DANHGIASP::find($id);
        $comment->noidung = request('noidung');
        $comment->thoigian = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
        $comment->id_tk = request('id_tk');
        $comment->danhgia = request('danhgia');
        $comment->chinhsua = true;
        $comment->update();
        return response()->json([
                'status' => true,
                'message' => 'Thành công',
                'data' =>   $id
        ]);
               
    }
    public function deleteComment($id){
        $comment = DANHGIASP::find($id);
        $listReview = CTDG::where('id_dg', $id)->get();
        $listLike = LUOTTHICH::where('id_dg', $id)->get();
        foreach($listReview as $review){
            $review1 = CTDG::find($review->id);
            $review1->delete();
        }
        foreach($listLike as $like){
            $like1 = LUOTTHICH::find($like->id);
            $like1->delete();
        }
        $comment->delete();
        return response()->json([
            'status' => true,
            'message' => 'Xóa đánh giá thành công',
            'data' =>   null
        ]);
    }
    public function postReply(Request $request){
        $reply = new PHANHOI();
        $reply->id_tk = request('id_tk');
        $reply->id_dg = request('id_dg');
        $reply->noidung = request('noidung');
        $reply->thoigian = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
        $comment = DANHGIASP::find(request('id_dg'));
        if(request('id_tk') != $comment->id_tk){
            $sanpham = SANPHAM::find($comment->id);
            $user = TAIKHOAN::find(request('id_tk'));
            $notification = new THONGBAO();
            $notification->id_tk =$comment->id_tk;
            $notification->tieude = "Phản hồi";
            $notification->noidung = "Bạn có một phản hồi từ <b>".$user->hoten."</b> ở sản phẩm <b>".$sanpham->tensp." ".$sanpham->dungluong." - ".$sanpham->mausac."</b>.";
            $notification->trangthaithongbao = 0;
            $notification->save();
            $userComment = TAIKHOAN::find($comment->id_tk);
            $product = $this->IndexController->getProductById($comment->id_sp);
            $notification = [
                'user' => $userComment,
                'type' => 'reply',
                'data' => [
                    'userReply' => $user,
                    'avtURL' => $user->htdn == 'normal' ? 'images/user/'.$user->anhdaidien : $user->anhdaidien,
                    'link' => route('user/chi-tiet', ['name' => $product['tensp_url'], 'danhgia' => request('id_dg')])
                ]
            ];
            //web
            event(new sendNotification($notification));

            //app
            if(!empty($userComment->device_token))
            (new PushNotificationController)->sendPush($userComment->device_token, "Phản hồi", "Bạn có một phản hồi từ <b>".$user->hoten."</b> ở sản phẩm <b>".$sanpham->tensp." ".$sanpham->dungluong." - ".$sanpham->mausac."</b>.");
        }
        if($reply->save()){
            return response()->json([
                'status' => true,
                'message' => 'Thành công',
                'data' => null
            ]);
        }
        
        return response()->json([
            'status' => false,
            'message' => 'Thất bại',
            'data' => null
        ]);
    }
    public function postLike($id, Request $request){
        $like = new LUOTTHICH();
        $like->id_dg = $id;
        $like->id_tk = $request->id_tk;
        if($like->save()){
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => null
            ]);
        };
        return response()->json([
            'status' => false,
            'message' => '',
            'data' => null
        ]);
    }
    public function deleteLike($id, Request $request){
        $liked = LUOTTHICH::where('id_tk', $request->id_tk)->where('id_dg', $id)->get();
        $like = LUOTTHICH::find($liked[0]->id);
        
        if($like->delete()){
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => null
            ]);
        };
        return response()->json([
            'status' => false,
            'message' => '',
            'data' => null
        ]);
    }
    public function uploadImageComment($id, Request $request){
        $i = 1;
        foreach($request->files as $image){
            $detailComment = new CTDG();
            if($image->isValid()){
                $request->validate([
                    'image_'.$i => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                ]);
                $imageName = time().$i.'.'. $image->getClientOriginalExtension();
                $image->move('images/evaluate/', $imageName);
                $detailComment->hinhanh = $imageName;
                $detailComment->id_dg = $id;
                $detailComment->save();
                $i++;
            }
        }
        return response()->json([
                'status' => true,
                'message' => '',
                'data' => null
        ]);
        
    }
    public function updateImageOldComment($id, Request $request){
        if(!empty(request("listImageOld"))){
            foreach(request("listImageOld") as $idImage){
                $imageReview = CTDG::find($idImage);
                $imageReview->delete(); 
            }
        }
        return response()->json([
                'status' => true,
                'message' => '',
                'data' => null
        ]);
        
    }
    public function updateImageNewComment($id, Request $request){
        $i=1;
        if(!empty($request->files)){
            foreach($request->files as $image){
                $detailComment = new CTDG();
                if($image->isValid()){
                    $request->validate([
                        'image_'.$i => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                    ]);
                    $imageName = time().$i.'.'. $image->getClientOriginalExtension();
                    $image->move('images/evaluate/', $imageName);
                    $detailComment->hinhanh = $imageName;
                    $detailComment->id_dg = $id;
                    $detailComment->save();
                    $i++;
                }
            }
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => null
        ]);
        }
    }
    public function checkWarranty(Request $request){
        $warranty = BAOHANH::where('imei', $request->imei)->get();
        $size = count($warranty);
        if($size > 0){
            $imei = IMEI::find($warranty[0]->id_imei);
            $product = SANPHAM::find($imei->id_sp);
            $warranty[0]->image = Helper::$URL."phone/". $product->hinhanh;
            $warranty[0]->name = $product->tensp;
            $warranty[0]->color = $product->mausac;
            $warranty[0]->storage = $product->dungluong;
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $warranty[0]
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Imei không tồn tại',
            'data' => null
        ]);
    }
    public function addToWishList(Request $request){
        $wishList = new SP_YEUTHICH();
        $wishList->id_tk = $request->id_tk;
        $wishList->id_sp = $request->id_sp;
        if($wishList->save()){
            return response()->json([
                'status' => true,
                'message' => "",
                'data' => null
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => "Có lỗi xảy ra",
            'data' => null
        ]);
    }

    public function deleteProductInWishList(Request $request){
        $wishList = SP_YEUTHICH::where("id_sp", $request->id_sp)->where("id_tk", $request->id_tk)->get();
        $wishList1 = SP_YEUTHICH::find($wishList[0]->id);
        if($wishList1->delete()){
            return response()->json([
                'status' => true,
                'message' => "",
                'data' => null
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => "Có lỗi xảy ra",
            'data' => null
        ]);
    }

    public function getWishList($id){
        $listID = array();
        $wishList = SP_YEUTHICH::where("id_tk", $id)->get();
        foreach($wishList as $wish){
            array_push($listID, $wish->id_sp);
        }
        $listProduct = SANPHAM::whereIn('id', $listID)->get();
        foreach($listProduct as $pro){
            $pro->hinhanh = Helper::$URL."phone/".$pro->hinhanh;
            if(!empty(KHUYENMAI::find($pro->id_km)->chietkhau)){
                    $pro->giamgia = KHUYENMAI::find($pro->id_km)->chietkhau;
               } else  $pro->giamgia = 0;
            $allJudge = DANHGIASP::where("id_sp", $pro->id)->get();
            $totalVote = 0;
            $totalJudge = 0;
            $idUser = -1;
            foreach($allJudge as $judge){
                if($judge->id_tk != $idUser){
                    $totalVote += $judge->danhgia;
                    $time =$judge->thoigian;
                    $idUser = $judge->id_tk;
                    $totalJudge++;
                   }else{
                       if($judge->thoigian != $time){
                        $totalVote += $judge->danhgia;
                        $totalJudge++; 
                       }  
                        $time = $judge->thoigian;
                        $content = $judge->noidung;
                        $idUser = $judge->id_tk;           
                   }
            }
            $pro->tongluotvote = $totalVote;
            $pro->tongdanhgia = count($allJudge);
        }
       return response()->json([
                'status' => true,
                'message' => "",
                'data' => $listProduct
            ]);
        }
        public function searchName(Request $request){
            $listProduct = SANPHAM::where("tensp", 'like','%'.$request->q.'%')->groupBy("tensp")->get();
            foreach($listProduct as $pro){
                $pro->hinhanh = Helper::$URL."phone/".$pro->hinhanh;
                if(!empty(KHUYENMAI::find($pro->id_km)->chietkhau))
                {
                    $pro->giamgia = KHUYENMAI::find($pro->id_km)->chietkhau;
                } else  $pro->giamgia = 0;
                $allJudge = DANHGIASP::where("id_sp", $pro->id)->get();
                $totalVote = 0;
                $totalJudge = 0;
                $idUser = -1;
                foreach($allJudge as $judge){
                    if($judge->id_tk != $idUser){
                        $totalVote += $judge->danhgia;
                        $time =$judge->thoigian;
                        $idUser = $judge->id_tk;
                        $totalJudge++;
                       }else{
                           if($judge->thoigian != $time){
                            $totalVote += $judge->danhgia;
                            $totalJudge++; 
                           }  
                            $time = $judge->thoigian;
                            $content = $judge->noidung;
                            $idUser = $judge->id_tk;           
                       }
                }
                $pro->tongluotvote = $totalVote;
                $pro->tongdanhgia = count($allJudge);
            }
            return response()->json([
                'status' => true,
                'message' => "",
                'data' => $listProduct
            ]);
        }
    }
   
