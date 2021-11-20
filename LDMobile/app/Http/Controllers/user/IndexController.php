<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use File;
use Session;
use Cookie;

use App\Models\BANNER;
use App\Models\BAOHANH;
use App\Models\CHINHANH;
use App\Models\CTDG;
use App\Models\CTDH;
use App\Models\DANHGIASP;
use App\Models\DONHANG;
use App\Models\GIOHANG;
use App\Models\HINHANH;
use App\Models\IMEI;
use App\Models\KHO;
use App\Models\KHUYENMAI;
use App\Models\LUOTTHICH;
use App\Models\MAUSP;
use App\Models\NHACUNGCAP;
use App\Models\PHANHOI;
use App\Models\SANPHAM;
use App\Models\SLIDESHOW_CTMSP;
use App\Models\SLIDESHOW;
use App\Models\SP_YEUTHICH;
use App\Models\TAIKHOAN_DIACHI;
use App\Models\TAIKHOAN_VOUCHER;
use App\Models\TAIKHOAN;
use App\Models\THONGBAO;
use App\Models\TINHTHANH;
use App\Models\VOUCHER;
use App\Models\LUOTTRUYCAP;
use App\Models\DONHANG_DIACHI;
use App\Models\HANGDOI;
use App\Models\CTHD;

class IndexController extends Controller
{
    public function __construct()
    {
        $this->viewprefix='user.pages.';
        $this->user='user/content/';
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }
    
    public function Index(){
        /*=================================
                    hotsale
        ===================================*/
        // sắp xếp khuyến mãi giảm dần
        $lst_promotion = $this->getHotSales(2);

        /*=================================
                    featured
        ===================================*/

        $lst_featured = $this->getFeatured();

        $data = [
            // slideshow
            'lst_slide' => SLIDESHOW::all(),
            'qty_slide' => count(SLIDESHOW::all()),

            // banner
            'lst_banner' => BANNER::all(),

            // list hotsale
            'lst_promotion' => $lst_promotion,

            // list featured
            'lst_featured' => $lst_featured,
        ];

        return view($this->user."index")->with($data);
    }

    public function DienThoai(){
        $lst_product = [];
        $totalQty = 0;
        $models = MAUSP::limit(10)->where('trangthai', 1)->get();

        // danh sách sản phẩm
        foreach($models as $model){
            $products = $this->getProductByCapacity($model->id);
            foreach($products as $product){
                // sản phẩm đang kinh doanh và có hàng trong kho
                if($this->isShow($product)) {
                    array_push($lst_product, $product);
                }
            }
        }

        // các loại ram hiện có
        $lst_ram = $this->getRamAllProduct();
        // các loại dung lượng hiện có
        $lst_capacity = $this->getCapacityAllProduct();
        
        $data = [
            'lst_product' => $lst_product,
            'lst_ram' => $lst_ram,
            'lst_capacity' => $lst_capacity,
        ];

        return view($this->user."dien-thoai")->with($data);
    }

    public function TimKiemDienThoai()
    {
        $queryParams = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        
        if (!$queryParams){
            return back();
        }

        $keyword = substr($queryParams, 8);
        $keyword = str_replace('-', ' ', $keyword);

        $data = [
            'keyword' => $keyword,
            'lst_product' => $this->searchPhone($keyword),
        ];

        return view($this->user.'tim-kiem')->with($data);
    }

    public function ChiTiet($name){
        $id = $this->getProductIdByName($name);
        
        if(!$id){
            return redirect()->route('user/dien-thoai')->with('toast_message', 'Sản phẩm không hợp lệ');
        }

        $SANPHAM = SANPHAM::find($id);

        $id_msp = $SANPHAM->id_msp;
        $capacity = $SANPHAM->dungluong;
        $ram = $SANPHAM->ram;
        $id_km = $SANPHAM->id_km;
        $model = MAUSP::where('id', $id_msp)->first();
        $user = session('user');

        // các điện thoại cùng mẫu
        $phoneByModel = MAUSP::find($id_msp)->sanpham;

        // các điện thoại cùng mẫu theo dung lượng
        $phoneByCapacity = $this->getProductByCapacity($id_msp);

        // danh sách id_sp cùng dung lượng
        $id_sp_list = $this->getListIdSameCapacity($id);
        
        /*==============================================================================================
                                                       Phone
        ================================================================================================*/

        // điện thoại theo id
        $phone = $this->getProductById($id);

        $phone['id_youtube'] = $model->id_youtube;
        $phone['baohanh'] = MAUSP::where('id', $id_msp)->first()->baohanh;
        $phone['cauhinh'] = $this->getSpecifications($id);
        if($id_km) {
            $phone['khuyenmai'] = $this->getPromotionById($id_km);
        } else {
            $phone['khuyenmai'] = [];
        }

        /*==============================================================================================
                                                  product variation
        ================================================================================================*/

        $lst_variation = [
            'color' => [],
            'image' => [],
        ];

        // lấy màu sắc, hình ảnh biến thể
        $i = 0;
        foreach($phoneByModel as $key){
            if($key['dungluong'] === $capacity && $key['ram'] === $ram){
                $lst_variation['color'][$i] = [
                    'id' => $key['id'],
                    'hinhanh' => $key['hinhanh'],
                    'mausac' => $key['mausac'],
                    'giakhuyenmai' => $phone['giakhuyenmai'],
                    'yeuthich' => 0,
                ];

                array_push($lst_variation['image'], [
                    'id' => $key['id'],
                    'hinhanh' => $key['hinhanh'],
                ]);

                if(session('user')) {
                    // đã thêm màu sắc vào danh sách yêu thích
                    if(SP_YEUTHICH::where('id_tk', session('user')->id)->where('id_sp', $key['id'])->first()){
                        $lst_variation['color'][$i]['yeuthich'] = 'true';
                    } else {
                        $lst_variation['color'][$i]['yeuthich'] = 'false';
                    }
                }
                $i++;
            }
        }

        // danh sách dung lượng không trùng nhau
        $distinctCapacityList = [];
        // danh sách ram không trùng nhau
        $distinctRamList = [];

        foreach($phoneByCapacity as $product) {
            if(array_search($product['dungluong'], array_column($distinctCapacityList, 'dungluong')) === false) {
                array_push($distinctCapacityList, [
                    'dungluong' => $product['dungluong'],
                    'tensp_url' => $product['tensp_url'],
                    'giakhuyenmai' => $product['giakhuyenmai'],
                ]);
            }

            if(array_search($product['ram'], array_column($distinctRamList, 'ram')) === false) {
                array_push($distinctRamList, [
                    'ram' => $product['ram'],
                    'tensp_url' => $product['tensp_url'],
                    'giakhuyenmai' => $product['giakhuyenmai'],
                ]);
            }
        }

        /*==============================================================================================
                                                 Detail Evaluate
        ================================================================================================*/

        // đánh giá theo dung lượng
        $userEvaluate = [];
        $anotherEvaluate = [];

        $user ?
            $lst_evaluate = $this->getEvaluateByCapacity($id_sp_list, $user->id)
            :
            $lst_evaluate = $this->getEvaluateByCapacity($id_sp_list);

        if($user){
            $id_tk = $user->id;
            foreach($lst_evaluate['evaluate'] as $evaluate){
                if($evaluate['taikhoan']['id'] == $id_tk){
                    array_push($userEvaluate, $evaluate);
                } else {
                    array_push($anotherEvaluate, $evaluate);
                }
            }
        }

        // đánh giá sao
        $starRating = [
            'total-rating' => $lst_evaluate['total-rating'],
            'rating' => $lst_evaluate['rating'],
            'total-star' => $lst_evaluate['total-star'],
        ];

        /*==============================================================================================
                                                    supplier
        ================================================================================================*/

        $supplier = $this->getSupplierByModelId($id_msp);

        /*==============================================================================================
                                                     branch
        ================================================================================================*/

        $lst_area = TINHTHANH::all();

        /*==============================================================================================
                                                   slideshow model
        ==============================================================================================*/

        $slide_model = MAUSP::find($id_msp)->slideshow_ctmsp;

        /*==============================================================================================
                                                   same brand
        ==============================================================================================*/

        $lst_proSameBrand = $this->getRandomProductBySupplierId($id, $model->id_ncc);

        /*==============================================================================================
                                                similar product
        ==============================================================================================*/
        
        $lst_similarPro = $this->getProductByPriceRange($id);

        /*==============================================================================================
                                                Have not Evaluated
        ==============================================================================================*/

        // sản phẩm chưa đánh giá
        $haveNotEvaluated = [];

        // đơn hàng của người dùng
        if($user){
            foreach(DONHANG::where('id_tk', $user->id)->get() as $order){
                // đơn hàng thành công
                if($order->trangthaidonhang === 'Thành công'){
                    // chi tiết đơn hàng
                    foreach(DONHANG::find($order['id'])->ctdh as $detail){
                        $product = SANPHAM::find($detail->pivot->id_sp);
                        // sản phẩm cùng id_msp và dung lượng
                        if($product->id_msp === $id_msp && $product->dungluong === $capacity){
                            //thời gian đơn hàng
                            $timeOrder = strtotime(str_replace('/', '-', $order['thoigian']));
                            // đánh giá
                            $evaluate = DANHGIASP::where('id_tk', session('user')->id)->where('id_sp', $product->id)->get();
                            // không có id_sp trong bảng đánh giá
                            if(count($evaluate) == 0){
                                // nếu đã thêm vào mảng chưa đánh giá rồi thì tiếp tục
                                $flag = 0;
                                if(!empty($haveNotEvaluated)){
                                    foreach($haveNotEvaluated as $arr){
                                        if($arr['id'] == $product->id){
                                            $flag = 1;
                                            break;
                                        }
                                    }
                                    if($flag == 0){
                                        array_push($haveNotEvaluated, $this->getProductById($product->id));    
                                    }
                                } else {
                                    array_push($haveNotEvaluated, $this->getProductById($product->id));
                                }
                            }
                            // đã có id_sp trong bảng đánh giá. kiểm tra ngày mua với ngày đánh giá
                            else {
                                // thời gian đánh giá của id_sp mới nhất
                                $timeEvaluate = strtotime(str_replace('/', '-', $evaluate[count($evaluate) - 1]['thoigian']));
                                // nếu thời gian mua mới > thời gian đánh giá => chưa đánh giá
                                if($timeOrder > $timeEvaluate){
                                    // nếu đã thêm vào mảng chưa đánh giá rồi thì tiếp tục
                                    $flag = 0;
                                    if(!empty($haveNotEvaluated)){
                                        foreach($haveNotEvaluated as $arr){
                                            if($arr['id'] == $product->id){
                                                $flag = 1;
                                                break;
                                            }
                                        }
                                        if($flag == 0){
                                            array_push($haveNotEvaluated, $this->getProductById($product->id));    
                                        }
                                    } else {
                                        array_push($haveNotEvaluated, $this->getProductById($product->id));
                                    }
                                }
                            }
                        }
                    }
                }
            }   
        }

        /*==============================================================================================
                                                    data
        ================================================================================================*/

        $data = [
            'phone' => $phone,
            'starRating' => $starRating,
            'lst_variation' => $lst_variation,
            'distinctCapacityList' => $distinctCapacityList,
            'distinctRamList' => $distinctRamList,
            'supplier' => $supplier,
            'lst_area' => $lst_area,
            'slide_model' => $slide_model,
            'lst_proSameBrand' => $lst_proSameBrand,
            'lst_similarPro' => $lst_similarPro,
            'haveNotEvaluated' => $haveNotEvaluated,
            'userEvaluate' => $userEvaluate,
            'anotherEvaluate' => $anotherEvaluate,
        ];

        return view($this->user."chi-tiet-dien-thoai")->with($data);
    }

    public function LienHe()
    {
        $data = [
            'lst_branch' => CHINHANH::all(),
        ];

        return view($this->user.'lien-he')->with($data);
    }

    public function SoSanh($str){
        $lst_urlName = explode('vs', $str);

        if(count($lst_urlName) === 1){
            if(session('_previous')) {
                return back()->with('toast_message', 'Cần ít nhất 2 sản phẩm để có thể so sánh');
            } else {
                return redirect('/')->with('toast_message', 'Cần ít nhất 2 sản phẩm để có thể so sánh');
            }
        }

        $id_sp_list = [];
        // lấy danh sách id_sp so sánh
        foreach($lst_urlName as $url) {
            $id = $this->getProductIdByName($url);

            if($id) {
                array_push($id_sp_list, $id);
            }
        }

        if(count($id_sp_list) === 1){
            if(session('_previous')) {
                return back()->with('toast_message', 'Sản phẩm không hợp lệ để so sánh');
            } else {
                return redirect('/')->with('toast_message', 'Sản phẩm không hợp lệ để so sánh');
            }
        }

        //danh sách sản phẩm so sánh
        $productList = [];
        foreach($id_sp_list as $id_sp) {
            array_push($productList, $this->getProductInformation($id_sp));
        }

        $currentProduct = [];
        $compareProduct = [];
        $thirdProduct = [];

        $currentProduct = $productList[0];
        $compareProduct = $productList[1];

        if(count($productList) === 3) {
            $thirdProduct = $productList[2];
        }

        $data = [
            'currentProduct' => $currentProduct,
            'compareProduct' => $compareProduct,
            'thirdProduct' => $thirdProduct,
        ];

        return view($this->user.'so-sanh')->with($data);
    }

    public function TraCuu()
    {
        return view($this->user.'tra-cuu');
    }

    public function ThongBao()
    {
        // return view($this->user."thong-bao");
        if(session('message')){
            return view($this->user."thong-bao");
        }
        return back();
    }

    /*==========================================================================================================
                                                    ajax                                                            
    ============================================================================================================*/

    public function AjaxChangeLocation(Request $request)
    {   
        if($request->ajax()){
            $type = $request->type;
            $id = $request->id;

            if($type == 'TinhThanh'){
                $file = file_get_contents('QuanHuyen.json');
                $quanHuyen = json_decode($file, true)[$id];

                $name = array_column($quanHuyen, 'Name');
                array_multisort($name, SORT_ASC, $quanHuyen);

                return $quanHuyen;
            } else {
                $file = file_get_contents('PhuongXa.json');
                $phuongXa = json_decode($file, true)[$id];

                $name = array_column($phuongXa, 'Name');
                array_multisort($name, SORT_ASC, $phuongXa);

                return $phuongXa;
            }
        }
    }

    public function AjaxBindAddress(Request $request){
        if($request->ajax()){
            $userAddress = TAIKHOAN_DIACHI::find($request->id);

            return $userAddress;
        }
    }

    public function AjaxGetUserFullname(Request $request) {
        if($request->ajax()) {
            $response = [
                'fullname' => null
            ];

            if(session('user')) {
                $response['fullname'] = session('user')->hoten;
            }

            return $response;
        }
    }

    public function AjaxForgetLoginStatusSession(Request $request)
    {
        if($request->ajax()){
            $request->session()->forget('login_status');
        }

        return back();
    }

    // tìm kiếm điện thoại
    public function AjaxSearchPhone(Request $request)
    {
        if($request->ajax()){
            $keyword = $request->str;

            $lst_product = [
                'phoneList' => $this->searchPhone($keyword),
                'url_phone' => 'images/phone/',
            ];

            return $lst_product;
        }
    }

    public function searchPhone($keyword) {
        $productList = [];

        $allProducts = $this->getAllProductByCapacity();
        foreach($allProducts as $product){
            // vd: -19%
            $discountText = $product['khuyenmai'] ? ('-'. ($product['khuyenmai'] * 100) . '%') : '';

            $string = $product['tensp'].$product['ram'].
                $product['dungluong'].$product['gia'].$product['giakhuyenmai'].$discountText;

            $unaccent = $this->unaccent($string);
            $lower = strtolower($unaccent);
                
            if(str_contains($lower, $keyword)){
                array_push($productList, $product);
            }
        }

        return $productList;
    }

    // lọc sản phẩm
    public function AjaxFilterProduct(Request $request)
    {
        if($request->ajax()){
            $data = $request->arrFilterSort;

            $filter = null;
            
            // có lọc
            if(array_key_exists('filter', $data)){
                $filter = $request->arrFilterSort['filter'];
            }
            
            // sắp xếp
            $sort = $request->arrFilterSort['sort'];

            $allProducts = $this->getAllProductByCapacity(true);

            // nếu gỡ bỏ hết bộ lọc thì trả về tất cả sản phẩm
            if(!$filter){
                // sắp xếp
                if($sort === 'default'){
                    return $allProducts;
                } elseif($sort === 'high-to-low'){
                    return $this->sortPrice($allProducts, 'desc');
                } elseif($sort === 'low-to-high'){
                    return $this->sortPrice($allProducts);
                } else {
                    return $this->sortProductByPromotion($allProducts, 'desc');
                }
            }

            $resultList = [];
            $lst_product = [];

            // lọc theo tiêu chí đầu tiên
            // lấy mảng dữ liệu được lọc, sử dụng tiếp cho các lần lọc tiếp theo
            $firstFilterKey = array_keys($filter)[0];
            switch($firstFilterKey) {
                case 'brand':
                    foreach($filter['brand'] as $supplier){
                        $lst_temp = $this->getAllProductBySupplierId(NHACUNGCAP::where('tenncc', 'like', $supplier.'%')->first()->id);
                        foreach($lst_temp as $key){
                            array_push($lst_product, $key);
                        }
                    }
                    break;
                case 'price':
                    foreach($filter['price'] as $price){
                        switch($price) {
                            case '2':
                                foreach($allProducts as $product){
                                    if($product['gia'] < 2000000){
                                        array_push($lst_product, $product);
                                    }
                                }
                                break;
                            case '3-4':
                                foreach($allProducts as $product){
                                    if($product['gia'] >= 3000000 && $product['gia'] <= 4000000){
                                        array_push($lst_product, $product);
                                    }
                                }
                                break;
                            case '4-7':
                                foreach($allProducts as $product){
                                    if($product['gia'] >= 4000000 && $product['gia'] <= 7000000){
                                        array_push($lst_product, $product);
                                    }
                                }
                                break;
                            case '7-13':
                                foreach($allProducts as $product){
                                    if($product['gia'] >= 7000000 && $product <= 13000000){
                                        array_push($lst_product, $product);
                                    }
                                }
                                break;
                            case '13-20':
                                foreach($allProducts as $product){
                                    if($product['gia'] >= 13000000 && $product['gia'] <= 20000000){
                                        array_push($lst_product, $product);
                                    }
                                }
                                break;
                            case '20':
                                foreach($allProducts as $product){
                                    if($product['gia'] >= 20000000){
                                        array_push($lst_product, $product);
                                    }
                                }
                                break;
                        }
                    }
                    break;
                case 'os':
                    foreach($filter['os'] as $os){
                        if($os === 'Android'){
                            foreach(NHACUNGCAP::where('id', '!=' , NHACUNGCAP::where('tenncc', 'like', 'Apple%')->first()->id)->get() as $supplier){
                                foreach($this->getAllProductBySupplierId($supplier['id']) as $product){
                                    array_push($lst_product, $product);
                                }
                            }
                        } else {
                            foreach($this->getAllProductBySupplierId(NHACUNGCAP::where('tenncc', 'like', 'Apple%')->first()->id) as $product){
                                array_push($lst_product, $product);
                            }
                        }
                    }
                    break;
                case 'ram':
                    foreach($filter['ram'] as $os){
                        foreach($allProduct as $product){
                            if(strcmp($product['ram'], $os) == 0){
                                array_push($lst_product, $product);
                            }
                        }
                    }
                    break;
                case 'capacity':
                    foreach($filter['capacity'] as $capacity){
                        foreach($allProducts as $product){
                            if(strcmp($product['dungluong'], $capacity) == 0){
                                array_push($lst_product, $product);
                            }
                        }
                    }
                    break;
            }

            array_push($resultList, $lst_product);

            // nếu chỉ có 1 tiêu chí lọc thì trả về kết quả
            if(count($filter) === 1){
                // kiểm tra sắp xếp
                if($sort === 'default'){
                    return $resultList[0];
                } else {
                    if($sort === 'high-to-low'){
                        return $this->sortPrice($resultList[0], 'desc');
                    } elseif($sort === 'low-to-high'){
                         return $this->sortPrice($resultList[0]);
                    } else {
                        return $this->sortProductByPromotion($resultList[0], 'desc');
                    }
                }
            }

            unset($lst_product);

            // lọc tiếp tục các tiêu chí khác
            for($i = 1; $i < count($filter); $i++){
                $lst_product = [];

                $filterKey = array_keys($filter)[$i];
                switch($filterKey) {
                    case 'brand':
                        foreach($filter[$filterKey] as $supplier){
                            foreach(NHACUNGCAP::find(NHACUNGCAP::where('tenncc', 'like', $supplier.'%')->first()->id)->mausp as $model){
                                foreach($resultList[$i - 1] as $product){
                                    if($product['id_msp'] == $model['id']){
                                        array_push($lst_product, $product);
                                    }
                                }
                            }
                        }
                        break;
                    case 'price':
                        foreach($filter[$filterKey] as $price) {
                            switch($price) {
                                case '2':
                                    foreach($resultList[$i - 1] as $product){
                                        if(intval($product['gia']) < 2000000){
                                            array_push($lst_product, $product);
                                        }
                                    }
                                    break;
                                case '3-4':
                                    foreach($resultList[$i - 1] as $product){
                                        if(intval($product['gia']) >= 3000000 && intval($product['gia']) <= 4000000){
                                            array_push($lst_product, $product);
                                        }
                                    }
                                    break;
                                case '4-7':
                                    foreach($resultList[$i - 1] as $product){
                                        if(intval($product['gia']) >= 4000000 && intval($product['gia']) <= 7000000){
                                            array_push($lst_product, $product);
                                        }
                                    }
                                    break;
                                case '7-13':
                                    foreach($resultList[$i - 1] as $product){
                                        if(intval($product['gia']) >= 7000000 && intval($product['gia']) <= 13000000){
                                            array_push($lst_product, $product);
                                        }
                                    }
                                    break;
                                case '13-20':
                                    foreach($resultList[$i - 1] as $product){
                                        if(intval($product['gia']) >= 13000000 && intval($product['gia']) <= 20000000){
                                            array_push($lst_product, $product);
                                        }
                                    }
                                    break;
                                case '20':
                                    foreach($resultList[$i - 1] as $product){
                                        if(intval($product['gia']) >= 20000000){
                                            array_push($lst_product, $product);
                                        }
                                    }
                                    break;
                            }
                        }
                        break;
                    case 'os':
                        foreach($filter[$filterKey] as $os){
                            if($os === 'Android'){
                                foreach(NHACUNGCAP::where('id', '!=', NHACUNGCAP::where('tenncc', 'like', 'Apple%')->first()->id)->get() as $supplier){
                                    foreach(NHACUNGCAP::find($supplier['id'])->mausp as $model){
                                        foreach($resultList[$i - 1] as $product){
                                            if($product['id_msp'] == $model['id']){
                                                array_push($lst_product, $product);
                                            }
                                        }
                                    }
                                }
                            } else {
                                foreach(NHACUNGCAP::find(NHACUNGCAP::where('tenncc', 'like', 'Apple%')->first()->id)->mausp as $model){
                                    foreach($resultList[$i - 1] as $product){
                                        if($product['id_msp'] == $model['id']){
                                            array_push($lst_product, $product);
                                        }
                                    }
                                }
                            }
                        }
                        break;
                    case 'ram':
                        foreach($filter[$filterKey] as $ram){
                            foreach($resultList[$i - 1] as $product){
                                if(strcmp($product['ram'], $ram) == 0){
                                    array_push($lst_product, $product);
                                }
                            }
                        }
                        break;
                    case 'capacity':
                        foreach($filter[$filterKey] as $capacity){
                            foreach($resultList[$i - 1] as $product){
                                if(strcmp($product['dungluong'], $capacity) == 0){
                                    array_push($lst_product, $product);
                                }
                            }
                        }
                        break;
                }

                array_push($resultList, $lst_product);
            }

            $lst_result = $resultList[count($resultList) - 1];

            // trả về danh sách kết quả cuối cùng
            // kiểm tra sắp xếp
            if($sort === 'default'){
                return $lst_result;
            } elseif($sort === 'high-to-low'){
                return $this->sortPrice($lst_result, 'desc');
            } elseif($sort === 'low-to-high'){
                    return $this->sortPrice($lst_result);
            } else {
                return $this->sortProductByPromotion($lst_result, 'desc');
            }
        }
    }

    // chọn màu sắc
    public function AjaxChooseColor(Request $request)
    {
        if($request->ajax()){
            // chưa đăng nhập và ở trang điện thoại
            if(!session('user') && !$request->page){
                return false;
            }

            $product = $this->getProductById($request->id_sp);

            $lst_color = [
                'mausac' => [],
                'tensp' => $product['tensp'],
                'khuyenmai' => $product['khuyenmai'],
                'giakhuyenmai' => $product['giakhuyenmai'],
                'gia' => $product['gia'],
                'url_phone' => 'images/phone/',
            ];

            $products = SANPHAM::where('id_msp', $product['id_msp'])->get();
            foreach($products as $key){
                $qtyInStock = KHO::where('id_sp', $key->id)->sum('slton');

                if($qtyInStock > 0 && $key->dungluong == $product['dungluong'] && $key->ram == $product['ram'] && $key->trangthai == 1){
                    array_push($lst_color['mausac'], [
                        'id' => $key['id'],
                        'mausac' => $key['mausac'],
                        'hinhanh' => $key['hinhanh'],
                    ]);
                }
            }

            return $lst_color;
        }
    }

    public function AjaxGetQtyInStock(Request $request)
    {
        if($request->ajax()) {
            $response = [
                'product' => $this->getProductById($request->id_sp),
                'qtyInStock' => KHO::where('id_sp', $request->id_sp)->sum('slton')
            ];

            
            return $response;
        }
    }

    public function AjaxCheckQtyInStockBranch(Request $request)
    {
        if($request->ajax()){
            $response = [
                'productList' => [],
                'status' => true
            ];

            $id_tk = session('user')->id;
            $id_cn = $request->id;
            $idList = $request->idList;

            foreach($idList as $id_sp) {
                $product = $this->getProductById($id_sp);
                $warehouse = KHO::where('id_cn', $id_cn)->where('id_sp', $id_sp)->first();

                if($warehouse) {
                    // slton
                    $qtyInStock = $warehouse->slton;
                    // sl trong giỏ hàng
                    $qtyInCart = GIOHANG::where('id_tk', $id_tk)->where('id_sp', $id_sp)->first()->sl;

                    // hết hàng
                    if($qtyInStock == 0) {
                        array_push($response['productList'], [
                            'id' => $product['id'],
                            'tensp' => $product['tensp'],
                            'mausac' => $product['mausac'],
                            'ram' => $product['ram'],
                            'hinhanh' => $product['hinhanh'],
                            'trangthai' => 'out of stock',
                        ]);
                        $response['status'] = false;
                    }
                    // còn hàng
                    elseif($qtyInStock >= $qtyInCart){
                        array_push($response['productList'], [
                            'id' => $product['id'],
                            'tensp' => $product['tensp'],
                            'mausac' => $product['mausac'],
                            'ram' => $product['ram'],
                            'hinhanh' => $product['hinhanh'],
                            'trangthai' => 'in stock',
                        ]);
                    }
                    // quá số lượng
                    else{
                        array_push($response['productList'], [
                            'id' => $product['id'],
                            'tensp' => $product['tensp'],
                            'mausac' => $product['mausac'],
                            'ram' => $product['ram'],
                            'hinhanh' => $product['hinhanh'],
                            'slton' => $qtyInStock,
                            'trangthai' => 'not enough',
                        ]);
                        $response['status'] = false;
                    }
                }
                // kho tại chi nhánh chưa có sản phẩm
                else {
                    array_push($response['productList'], [
                        'id' => $product['id'],
                        'tensp' => $product['tensp'],
                        'mausac' => $product['mausac'],
                        'ram' => $product['ram'],
                        'hinhanh' => $product['hinhanh'],
                        'trangthai' => 'out of stock',
                    ]);
                    $response['status'] = false;
                }
            }

            return $response;
        }
    }

    public function AjaxCheckImei(Request $request)
    {
        if($request->ajax()){
            $warranty = BAOHANH::where('imei', $request->imei)->first();

            // imei không hợp lệ hoặc chưa kích hoạt
            if(!$warranty){
                return ['status' => 'invalid imei'];
            }
            // imei hợp lệ
            else {
                // sản phẩm
                $product = SANPHAM::where('id', IMEI::find($warranty->id_imei)->id_sp)->first();

                // bảo hành
                $product->baohanh = MAUSP::find($product['id_msp'])->baohanh;

                // có bảo hành
                if($product->baohanh){
                    $product->ngaymua = $warranty->ngaymua;

                    $product->ngayketthuc = $warranty->ngayketthuc;
    
                    // trạng thái bảo hành
                    $product->trangthaibaohanh = strtotime(str_replace('/', '-', $product['ngayketthuc'])) >= time() ? true : false;
                }

                return [
                    'status' => 'success',
                    'product' => $product,
                ];
            }
        }
    }

    // load thêm dữ liệu khi cuộn trang
    public function AjaxLoadMore(Request $request)
    {
        if($request->ajax()){
            $page = $request->page;
            $row = $request->row;
            $limit = $request->limit;
            $html = '';

            if($page === 'dienthoai'){
                $models = MAUSP::offset($row)->limit($limit)->where('trangthai', 1)->get();

                if(count($models) == 0){
                    return 'done';
                }

                $lst_product = [];
                foreach($models as $model){
                    $products = $this->getProductByCapacity($model->id);
                    foreach($products as $product){
                        // sản phẩm đang kinh doanh và có hàng trong kho
                        if($this->isShow($product)) {
                            array_push($lst_product, $product);
                        }
                    }
                }

                return $lst_product;
            } elseif($page === 'thongbao'){
                $data = THONGBAO::orderBy('id', 'desc')->where('id_tk', session('user')->id)->offset($row)->limit($limit)->get();

                if(count($data) == 0){
                    return 'done';
                }

                return $data;
            }
        }
    }

    // lấy sản phẩm theo nhà cung cấp
    public function AjaxGetProductByBrand(Request $request)
    {
        if($request->ajax()){
            $lst_product = [];

            $brand = NHACUNGCAP::where('tenncc', 'like', '%'.$request->brand.'%')->first();
            $models = MAUSP::where('id_ncc', $brand->id)->get();

            foreach($models as $model) {
                $products = $this->getProductByCapacity($model->id);

                foreach($products as $product){
                    if($this->isShow($product)) {
                        array_push($lst_product, $product);
                    }
                }
            }

            $data = [
                'lst_product' => $lst_product,
                'brandName' => $brand->tenncc,
                'fs_title' => count($lst_product) . ' điện thoại ' . explode(' ', $brand->tenncc)[0], // fs: filter - sort
            ];

            return $data;
        }
    }

    // xem tất cả phản hồi
    public function AjaxGetAllReply(Request $request){
        if($request->ajax()){
            $lst_reply = $this->getReply($request->id_dg);

            unset($lst_reply[0]);

            return $lst_reply;
        }
    }

    // có yêu cầu hàng đợi không
    public function isNeedToQueue($id_tk, $checkoutList){
        $result = [
            'status' => 'continue'
        ];

        // danh sách các sản phẩm mà slton trong kho không đủ để thanh toán
        $notEnoughQuantity = [];

        // 1. xét trong giỏ hàng của người dùng hiện tại
        $currentUserCart = GIOHANG::where('id_tk', $id_tk)->get();
        foreach($currentUserCart as $cart) {
            if(in_array($cart->id_sp, $checkoutList)) {
                // sl của sp trong giỏ hàng
                $qtyInCart = $cart->sl;
                // slton của sản phẩm trong kho
                $qtyInStock = KHO::where('id_sp', $cart->id_sp)->sum('slton');

                // nếu sp hết hàng
                if($qtyInStock === 0){
                    $result['status'] = 'out of stock';
                    return $result;
                }
                // nếu sl của sp trong giỏ hàng >= slton trong kho => không đủ hàng
                elseif($qtyInCart > $qtyInStock){
                    // cập nhật sl của sp trong giỏ hàng = slton hiện tại
                    GIOHANG::where('id', $cart->id)->update(['sl' => $qtyInStock]);

                    $product = $this->getProductById($cart->id_sp);
                    $product['qtyInStock'] = $qtyInStock;
                    array_push($notEnoughQuantity, $product);
                }
            }
        }

        // có sản phẩm không thể thanh toán
        if(!empty($notEnoughQuantity)) {
            $result['status'] = 'not enough quantity';
            $result['productList'] = $notEnoughQuantity;

            return $result;
        }

        // 2. lấy lần lượt các CTHD của những người dùng khác đã xếp hàng trước
        // lọc và lấy ra sản phẩm giống với sản phẩm của người dùng hiện tại

        $queues = HANGDOI::orderBy('id')->get();

        // nếu người dùng là người đầu tiên trong hàng đợi thì đi tới thanh toán
        if(count($queues) === 1) {
            return $result;
        }

        // mảng id_sp và sl trong CTHD của tất cả người dùng đang trong thanh toán
        $idAndQtyOfUsers = [];

        foreach($queues as $queue) {
            /**
             * kiểm tra hàng đợi của người dùng
             * nếu trạng thái = 0, xét thêm nếu thời gian tồn tại của hàng đợi quá 30s
             * thì xóa hàng đợi
             * 
             * trường hợp trạng thái = 1 mà người dùng bị rớt mạng không thể thanh toán
             * hệ thống kiểm tra thời gian tồn tại của hàng đợi quá 10 phút thì xóa hàng đợi
             */
            if(!$queue->trangthai) {
                // thời gian của hàng đợi
                $queueTime = $queue->timestamp;
                // thời gian đã tồn tại
                $timeOfexistence = time() - $queueTime;
                // từ 30s trở lên
                if($timeOfexistence >= 30) {
                    $this->removeQueue($queue->id);
                    continue;
                }
            } else {
                // thời gian của hàng đợi
                $queueTime = $queue->timestamp;
                // thời gian đã tồn tại
                $timeOfexistence = time() - $queueTime;
                // 10 phút = 600s
                if($timeOfexistence >= 600) {
                    $this->removeQueue($queue->id);
                    continue;
                }
            }

            // chi tiết hàng đợi của từng người dùng gồm các sản phẩm mà người dùng đó chọn thanh toán
            $QueueDetail = CTHD::where('id_hd', $queue->id)->get();

            foreach($QueueDetail as $detail) {
                // lấy ra sp giống vs sản phẩm đang xét
                if(in_array($detail->id_sp, $checkoutList)) {
                    // thêm dòng mới vào danh sách kết quả
                    if(empty($idAndQtyOfUsers) ||
                        array_search($detail->id_sp, array_column($idAndQtyOfUsers, 'id_sp')) === false) {
                        $row = [
                            'id_sp' => $detail->id_sp,
                            'sl' => $detail->sl
                        ];
                        array_push($idAndQtyOfUsers, $row);
                    }
                    // cập nhật sl của dòng đã tồn tại
                    else {
                        $key = array_search($detail->id_sp, array_column($idAndQtyOfUsers, 'id_sp'));
                        $idAndQtyOfUsers[$key]['sl'] += $detail->sl;
                    }
                }
            }
        }

        // 3. so sánh với slton kho
        foreach($idAndQtyOfUsers as $row) {
            // slton của sp
            $qtyInStock = KHO::where('id_sp', $row['id_sp'])->sum('slton');

            // nếu tổng sl của 1 sp trong tất cả giỏ hàng của người dùng > slton kho thì yêu cầu hàng đợi
            if($row['sl'] > $qtyInStock) {
                $result['status'] = 'waiting';
                break;
            }
        }

        return $result;
    }

    // kiểm tra đơn hàng đã được thanh toán bên app
    public function haveBeenPaid($id_tk, $checkoutList) {
        $userCart = GIOHANG::where('id_tk', $id_tk)->get();
        // giỏ hàng rỗng
        if(count($userCart) === 0) {
            return true;
        }

        // nếu id_sp được chọn không còn nằm trong giỏ hàng => đã được thanh toán bên app
        $isPaid = false;
        foreach($checkoutList as $id_sp) {
            $cart = GIOHANG::where('id_tk', $id_tk)->where('id_sp', $id_sp)->first();

            if(!$cart) {
                $isPaid = true;
                break;
            }
        }

        return $isPaid;
    }

    // hàng đợi thanh toán
    public function AjaxCheckoutQueue(Request $request){
        if($request->ajax()){
            $id_tk = $request->id_tk;
            $checkoutList = $request->checkoutList;

            // nếu đơn hàng đã được thanh toán trên app di động
            $isPaid = $this->haveBeenPaid($id_tk, $checkoutList);

            if($isPaid) {
                return ['status' => 'have been paid'];
            }

            // hàng đợi của người dùng
            $exists = HANGDOI::where('id_tk', $id_tk)->first();

            // thêm vào hàng đợi nếu chưa có
            if(!$exists) {
                $exists = HANGDOI::create([
                    'id_tk' => $id_tk,
                    'nentang' => 'web',
                    'timestamp' => time(),
                    'trangthai' => 1
                ]);

                // tạo CTHD
                $this->createQueueDetail($exists->id, $id_tk, $checkoutList);
            }
            // nếu đã có hàng đợi và đó là của nền tảng khác
            elseif($exists->nentang === 'app') {
                return ['status' => 'another platform'];
            }
            // nếu hàng đợi của người dùng trạng thái = 0 thì cập nhật lại = 1
            elseif(!$exists->trangthai) {
                $exists->timestamp = time();
                $exists->trangthai = 1;
                $exists->save();

                // xóa CTHD cũ
                CTHD::where('id_hd', $exists->id)->delete();

                // tạo CTHD mới
                $this->createQueueDetail($exists->id, $id_tk, $checkoutList);
            }
            /**
             * trường hợp khi tắt trang/ trình duyệt lỗi
             * sẽ không cập nhật trạng thái = 0 được
             * khi đó sẽ cập nhật lại timestamp 
             * */ 
            elseif($exists->trangthai) {
                $exists->timestamp = time();
                $exists->save();

                // xóa CTHD cũ
                CTHD::where('id_hd', $exists->id)->delete();

                // tạo CTHD mới
                $this->createQueueDetail($exists->id, $id_tk, $checkoutList);
            }

            $isQueue = $this->isNeedToQueue($id_tk, $checkoutList);
            $isQueue['queue'] = $exists;
            
            return $isQueue;
        }
    }

    // tạo CTHD
    public function createQueueDetail($id_hd, $id_tk, $checkoutList) {
        foreach($checkoutList as $id_sp) {
            // sl của sp trong giỏ hàng
            $qtyInCart = GIOHANG::where('id_tk', $id_tk)->where('id_sp', $id_sp)->first()->sl;

            CTHD::create([
                'id_hd' => $id_hd,
                'id_sp' => $id_sp,
                'sl' => $qtyInCart
            ]);
        }
    }
    
    // xóa hàng đợi
    public function AjaxRemoveQueue(Request $request){
        if($request->ajax()){
            $id_tk = $request->id_tk;

            $queue = HANGDOI::where('id_tk', $id_tk)->first();

            $this->removeQueue($queue->id);
        }
    }

    public function removeQueue($id_hd) {
        // xóa CTHD
        CTHD::where('id_hd', $id_hd)->delete();

        // xóa hàng đợi
        HANGDOI::destroy($id_hd);

        // làm mới id tăng tự động
        if(!HANGDOI::count()){
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            HANGDOI::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    // cập nhật trạng thái hàng đợi
    public function AjaxUpdateQueueStatus(Request $request){
        if($request->ajax()){
            HANGDOI::where('id_tk', $request->id_tk)
                    ->update([
                        'timestamp' => time(),
                        'trangthai' => 0,
                    ]);
        }
    }

    // khôi phục hàng đợi khi làm mới trang
    public function AjaxRecoverQueueStatus(Request $request){
        if($request->ajax()){
            $queue = HANGDOI::where('id_tk', $request->id_tk)->first();

            // người dùng đã chuyển sang app thanh toán
            if($queue->nentang === 'app') {
                return ['status' => 'another platform'];
            }

            $queue->timestamp = time();
            $queue->trangthai = 1;
            $queue->save();

            return ['status' => 'success'];
        }
    }

    // lấy danh sách chi nhánh theo id_tt
    public function AjaxGetBranchList(Request $request) {
        if($request->ajax()) {
            $branchList = CHINHANH::where('id_tt', $request->id_tt)->get();

            return $branchList;
        }
    }

    // lấy danh sách chi nhánh với slton kho
    public function AjaxGetBranchWithQtyInStock(Request $request) {
        if($request->ajax()) {
            $lst_result = [];

            // danh sách chi nhánh theo tỉnh thành
            $branchList = CHINHANH::where('id_tt', $request->id_tt)->get();

            foreach($branchList as $branch) {
                // slton kho của sản phẩm tại chi nhánh
                $qtyInStock = KHO::where('id_cn', $branch->id)
                                    ->where('id_sp', $request->id_sp)
                                    ->first()->slton;

                if($qtyInStock > 0) {
                    array_push($lst_result, $branch);
                }
            }

            return $lst_result;
        }
    }

    // lấy tổng sl mẫu sp theo dung lượng
    public function AjaxGetTotalQtyProByCap(Request $request) {
        if($request->ajax()) {
            $response = [
                'totalQty' => 0
            ];

            // tổng số lượng sản phẩm
            $allProducts = $this->getAllProductByCapacity(true);
            foreach($allProducts as $product) {
                if($this->isShow($product)) {
                    $response['totalQty']++;
                }
            }

            return $response;
        }
    }

    /*==========================================================================================================
                                                function                                                            
    ============================================================================================================*/

    public function print($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    // có hiển thị mẫu sp theo dung lượng lên view không
    public function isShow($product) {
        $bool = false;

        // đang kinh doanh
        if($product['modelStatus']) {
            // hàng sắp về
            if($product['comingSoon']) {
                $bool = true;
            }
            // kiểm tra slton kho
            elseif($product['inStocks']) {
                $bool = true;
            }
        }

        return $bool;
    }

    // kiểm tra trạng thái mẫu sp theo dung lượng
    public function getProByCapStatus($id_sp_list)
    {
        $bool = false;

        foreach($id_sp_list as $id_sp) {
            if(SANPHAM::find($id_sp)->trangthai === 1) {
                $bool = true;
                break;
            }
        }

        return $bool;
    }

    // kiểm tra mẫu sp theo dung lượng có trong kho không
    public function isProByCapInStocks($id_sp_list, $checkAllInStock = false)
    {
        $bool = false;

        // không kiểm tra slton của mẫu sp
        if($checkAllInStock === false) {
            foreach($id_sp_list as $id_sp) {
                $warehouse = KHO::where('id_sp', $id_sp)->first();
                // có trong kho
                if($warehouse) {
                    $bool = true;
                    break;
                }
            }
        } else {
            foreach($id_sp_list as $id_sp) {
                $warehouse = KHO::where('id_sp', $id_sp);
                if($warehouse->sum('slton') > 0) {
                    $bool = true;
                    break;
                }
            }
        }

        return $bool;
    }

    // lấy sản phẩm có khuyến mãi cao
    public function getHotSales($rank = 1)
    {
        $lst_product = [];

        // sắp xếp sp theo % khuyến mãi giảm dần
        $allProducts = $this->getAllProductByCapacity();
        $sort = $this->sortProductByPromotion($allProducts, 'desc');

        // mảng id_km
        $arrPromotionId = [];
        foreach(KHUYENMAI::orderBy('chietkhau', 'desc')->limit($rank)->select('id')->get() as $promotion){
            array_push($arrPromotionId, $promotion->id);
        }        
        
        foreach($sort as $product) {
            $id_sp_list = $this->getListIdSameCapacity($product['id']);
            // đang kinh doanh và có khuyến mãi
            if($this->isShow($product) && in_array($product['id_km'], $arrPromotionId)) {
                array_push($lst_product, $product);
            }
        }

        return $lst_product;
    }

    // lấy sản phẩm nổi bật
    public function getFeatured($max = 10)
    {
        $lst_featured = [];
        $lst_model = MAUSP::orderBy('id', 'desc')->limit($max)->get();

        foreach($lst_model as $model){
            if(count($lst_featured) === $max){
                break;
            }

            $products = $this->getProductByCapacity($model->id);
            
            foreach($products as $product){
                if(count($lst_featured) === $max){
                    break;
                }

                if($this->isShow($product)) {
                    array_push($lst_featured, $product);
                }
            }
        }

        return $lst_featured;
    }

    // lấy sao đánh giá và số lượt đánh giá theo mẫu và dung lượng
    public function getStarRatingByCapacity($lst_id)
    {
        // lấy đánh giá của mẫu sp theo dung lượng
        $starRating = [
            'total-rating' => 0,
            'rating' => [
                '5' => 0,
                '4' => 0,
                '3' => 0,
                '2' => 0,
                '1' => 0,
            ],
        ];

        $lst_tempEvaluate = [];

        // mảng đánh giá trong khoảng của id_sp theo thứ tự trong db
        $lst_temp = DANHGIASP::where('id_sp', '>=', $lst_id[0])->where('id_sp', '<=', $lst_id[count($lst_id) - 1])->get();
        $i = 0;

        foreach($lst_temp as $key){
            $imageEvaluate = $this->getEvaluateDetail($key['id']);

            $lst_tempEvaluate[$i] = [
                'id' => $key['id'],
                'taikhoan' => $this->getAccountById($key['id_tk']),
                'noidung' => $key['noidung'],
                'thoigian' => $key['thoigian'],
                'danhgia' => $key['danhgia'],
            ];
            $i++;
        }

        $idx = 0;

        // gộp và lấy đánh giá
        /*mô tả: vòng for chạy từ dòng đầu tiên, nếu các dòng tiếp theo thuộc về 1 đánh giá
                thì gôm chúng lại, lấy màu sắc của chúng cộng vào dòng đầu tiên. ta được 1 đánh giá hoàn chỉnh*/
        for($i = 0; $i < count($lst_tempEvaluate); $i++){
            // dòng đầu tiên
            $first = $lst_tempEvaluate[$i];
            // mảng các dòng giống nhau
            $temp = [];
            // số dòng giống nhau
            $step = 0;
            // for so sánh dòng đầu tiên với các dòng còn lại
            for($j = $i + 1; $j < count($lst_tempEvaluate); $j++){
                // giống nhau
                if($first['taikhoan']['id'] == $lst_tempEvaluate[$j]['taikhoan']['id'] && $first['noidung'] == $lst_tempEvaluate[$j]['noidung'] && $first['thoigian'] == $lst_tempEvaluate[$j]['thoigian']){
                    // thêm vào mảng các dòng giống nhau                   
                    array_push($temp, $lst_tempEvaluate[$j]);
                    // tăng số dòng giống nhau
                    $step++;
                }
                // nếu khác nhau thì thoát vòng lặp
                else {
                    break;
                }
            }

            if(!empty($temp)){
                // đi đến vị trí của dòng mới
                $i += $step;
            }

            // sao đánh giá
            if($lst_tempEvaluate[$i]['danhgia'] == 1){
                $starRating['rating']['1']++;
            } elseif($lst_tempEvaluate[$i]['danhgia'] == 2){
                $starRating['rating']['2']++;
            } elseif($lst_tempEvaluate[$i]['danhgia'] == 3){
                $starRating['rating']['3']++;
            } elseif($lst_tempEvaluate[$i]['danhgia'] == 4){
                $starRating['rating']['4']++;
            } else {
                $starRating['rating']['5']++;
            }

            $idx++;

            $starRating['total-rating']++;
        }

        if($starRating['total-rating'] == 0){
            $starRating['total-star'] = 0;    
        } else {
            $_5s = 5 * $starRating['rating']['5'];
            $_4s = 4 * $starRating['rating']['4'];
            $_3s = 3 * $starRating['rating']['3'];
            $_2s = 2 * $starRating['rating']['2'];
            $_1s = 1 * $starRating['rating']['1'];
    
            $starRating['total-star'] = ($_5s + $_4s + $_3s + $_2s + $_1s) / $starRating['total-rating'];
        }
        
        return $starRating;
    }

    // lấy tất cả sản phẩm theo dung lượng
    public function getAllProductByCapacity($skipInvalidProduct = false)
    {
        $lst_product = [];

        $models = MAUSP::all();

        // chỉ lấy các mẫu sp theo dung lượng được phép hiển thị
        if($skipInvalidProduct === true) {
            foreach($models as $model){
                $products = $this->getProductByCapacity($model->id);
                foreach($products as $product) {
                    if(count($product) !== 0 && $this->isShow($product)) {
                        array_push($lst_product,$product);
                    }
                }
            }
        } else {
            foreach($models as $model){
                $products = $this->getProductByCapacity($model->id);
                foreach($products as $product) {
                    if(count($product) !== 0) {
                        array_push($lst_product,$product);
                    }
                }
            }
        }

        return $lst_product;
    }

    // lấy mẫu sp theo dung lượng
    public function getProductByCapacity($id_msp, $id_sp = null)
    {
        // nếu lấy sản phẩm theo id chỉ định
        if($id_sp !== null) {
            $product = SANPHAM::find($id_sp);

            // kiểm tra cùng dung lượng có khác ram không
            $ramQty = SANPHAM::where('tensp', $product->tensp)
                                ->groupByRaw('ram')
                                ->get()
                                ->count();

            // cùng dung lượng nhưng khác ram
            if($ramQty > 1) {
                $tensp = $product->tensp.' ('.$product->ram.'/'.$product->dungluong.')';
                $tensp_url = str_replace(' ', '-', $product->tensp.'-'.$product->ram.'-'.$product->dungluong);
            } else {
                $tensp = $product->tensp.' '.$product->dungluong;
                $tensp_url = str_replace(' ', '-', $product->tensp.'-'.$product->dungluong);
            }

            $mausac_url = str_replace(' ', '-', strtolower($this->unaccent($product->mausac)));
            $promotion = $this->promotionCheck($product->id) ?
                SANPHAM::find($product->id)->khuyenmai->chietkhau : 0;
            $giakhuyenmai = $product->gia - ($product->gia * $promotion);
            $starRating = $this->getStarRatingByCapacity($this->getListIdSameCapacity($product->id));
            // hàng sắp về
            $id_sp_list = $this->getListIdSameCapacity($product->id);
            $comingSoon = !$this->isProByCapInStocks($id_sp_list);
            // có hàng trong kho không
            $inStocks = $this->isProByCapInStocks($id_sp_list, true);
            // trạng thái mẫu sp theo dung lượng
            $status = $this->getProByCapStatus($id_sp_list);

            return [
                'id' => $product->id,
                'id_msp' => $product->id_msp,
                'tensp' => $tensp,
                'tensp_url' => $tensp_url,
                'hinhanh' => $product->hinhanh,
                'mausac' => $product->mausac,
                'mausac_url' => $mausac_url,
                'ram' => $product->ram,
                'dungluong' => $product->dungluong,
                'gia' => $product->gia,
                'id_km' => $product->id_km,
                'khuyenmai' => $promotion,
                'giakhuyenmai' => $giakhuyenmai,
                'danhgia' => [
                    'qty' => $starRating['total-rating'],
                    'star' => $starRating['total-star'],
                ],
                'cauhinh' => $product->cauhinh,
                'comingSoon' => $comingSoon,
                'inStocks' => $inStocks,
                'trangthai' => $product->trangthai,
                'modelStatus' => $status,
            ];
        } else {
            // danh sách sản phẩm theo mẫu sp
            $phoneByModel = SANPHAM::where('id_msp', $id_msp)->get();

            // danh sách dung lượng khác nhau
            $capacityDistinct = SANPHAM::where('id_msp', $id_msp)->select('dungluong')->distinct()->get();
            // danh sách ram khác nhau
            $ramDistinct = SANPHAM::where('id_msp', $id_msp)->select('ram')->distinct()->get();

            $lst_temp = [];
            $i = 0;

            // lọc sản phẩm theo ram, dunglượng khác nhau
            foreach($capacityDistinct as $capacity){
                foreach($ramDistinct as $ram){
                    $keyName = $capacity->dungluong.'_'.$ram->ram;
                    foreach($phoneByModel as $phone){
                        if($phone->ram === $ram->ram && $phone->dungluong === $capacity->dungluong){
                            $lst_temp[$keyName][$i] = $phone->id;
                            $i++;
                        }
                    }
                    $i = 0;
                }
            }
        }

        $length = count($lst_temp);

        // mẫu sp không có sản phẩm
        if($length === 0) {
            return [];
        }

        $lst_product = [];

        // Kiểm tra cùng dung lượng nhưng khác ram
        if($length > 1){
            $ram_1 = explode('_', array_keys($lst_temp)[0])[1];
            $ram_2 = explode('_', array_keys($lst_temp)[1])[1];

            // nếu có
            if(strcmp($ram_1, $ram_2) !== 0){
                for($i = 0; $i < count($lst_temp); $i++){
                    $key = $lst_temp[array_keys($lst_temp)[$i]];
                    
                    // random id_sp
                    $rand_id_sp = mt_rand(0, count($key) - 1);

                    $product = SANPHAM::find($key[$rand_id_sp]);

                    $tensp = $product->tensp.' ('.$product->ram.'/'.$product->dungluong.')';
                    $tensp_url = str_replace(' ', '-', $product->tensp.'-'.$product->ram.'-'.$product->dungluong);
                    $mausac_url = str_replace(' ', '-', strtolower($this->unaccent($product->mausac)));
                    $promotion = $this->promotionCheck($product->id) ?
                        SANPHAM::find($product->id)->khuyenmai->chietkhau : 0;
                    $giakhuyenmai = $product->gia - ($product->gia * $promotion);
                    $starRating = $this->getStarRatingByCapacity($this->getListIdSameCapacity($product->id));
                    // hàng sắp về
                    $id_sp_list = $this->getListIdSameCapacity($product->id);
                    $comingSoon = !$this->isProByCapInStocks($id_sp_list);
                    // có hàng trong kho không
                    $inStocks = $this->isProByCapInStocks($id_sp_list, true);
                    // trạng thái mẫu sp theo dung lượng
                    $status = $this->getProByCapStatus($id_sp_list);

                    array_push($lst_product, [
                        'id' => $product->id,
                        'id_msp' => $product->id_msp,
                        'tensp' => $tensp,
                        'tensp_url' => $tensp_url,
                        'hinhanh' => $product->hinhanh,
                        'mausac' => $product->mausac,
                        'mausac_url' => $mausac_url,
                        'ram' => $product->ram,
                        'dungluong' => $product->dungluong,
                        'gia' => $product->gia,
                        'id_km' => $product->id_km,
                        'khuyenmai' => $promotion,
                        'giakhuyenmai' => $giakhuyenmai,
                        'danhgia' => [
                            'qty' => $starRating['total-rating'],
                            'star' => $starRating['total-star'],
                        ],
                        'cauhinh' => $product->cauhinh,
                        'comingSoon' => $comingSoon,
                        'inStocks' => $inStocks,
                        'trangthai' => $product->trangthai,
                        'modelStatus' => $status,
                    ]);
                }

                return $lst_product;
            }
        }

        for($i = 0; $i < count($lst_temp); $i++){
            $key = $lst_temp[array_keys($lst_temp)[$i]];

            // random id_sp
            $rand_id_sp = mt_rand(0, count($key) -1);

            $product = SANPHAM::find($key[$rand_id_sp]);

            $tensp = $product->tensp.' '.$product->dungluong;
            $tensp_url = str_replace(' ', '-', $product->tensp.'-'.$product->dungluong);
            $mausac_url = str_replace(' ', '-', strtolower($this->unaccent($product->mausac)));
            $promotion = $this->promotionCheck($product->id) ?
                SANPHAM::find($product->id)->khuyenmai->chietkhau : 0;
            $giakhuyenmai = $product->gia - ($product->gia * $promotion);
            $starRating = $this->getStarRatingByCapacity($this->getListIdSameCapacity($product->id));
            // hàng sắp về
            $id_sp_list = $this->getListIdSameCapacity($product->id);
            $comingSoon = !$this->isProByCapInStocks($id_sp_list);
            // có hàng trong kho không
            $inStocks = $this->isProByCapInStocks($id_sp_list, true);
            // trạng thái mẫu sp theo dung lượng
            $status = $this->getProByCapStatus($id_sp_list);

            array_push($lst_product, [
                'id' => $product->id,
                'id_msp' => $product->id_msp,
                'tensp' => $tensp,
                'tensp_url' => $tensp_url,
                'hinhanh' => $product->hinhanh,
                'mausac' => $product->mausac,
                'mausac_url' => $mausac_url,
                'ram' => $product->ram,
                'dungluong' => $product->dungluong,
                'gia' => $product->gia,
                'id_km' => $product->id_km,
                'khuyenmai' => $promotion,
                'giakhuyenmai' => $giakhuyenmai,
                'danhgia' => [
                    'qty' => $starRating['total-rating'],
                    'star' => $starRating['total-star'],
                ],
                'cauhinh' => $product->cauhinh,
                'comingSoon' => $comingSoon,
                'inStocks' => $inStocks,
                'trangthai' => $product->trangthai,
                'modelStatus' => $status,
            ]);
        }

        return $lst_product;
    }

    // lấy danh sách id_sp cùng mẫu cùng dung lượng
    public function getListIdSameCapacity($id_sp)
    {
        $product = SANPHAM::find($id_sp);
        $capacity = $product->dungluong;
        $ram = $product->ram;
        $id_msp = $product->id_msp;

        $lst_id = [];

        foreach(SANPHAM::where('id_msp', $id_msp)->where('dungluong', $capacity)->where('ram', $ram)->get() as $key){
            array_push($lst_id, $key->id);
        }

        return $lst_id;
    }

    // lấy danh sách id_sp cùng mẫu
    public function getListIdByModel($id_msp)
    {
        $lst_result = [];
        $temp = SANPHAM::select('id')->distinct()->where('id_msp', $id_msp)->get();
        foreach($temp as $key){
            array_push($lst_result, $key->id);
        }

        return $lst_result;
    }

    // lấy danh sách id_sp cùng ncc
    public function getListIdBySupplier($id_ncc)
    {
        $lst_result = [];
        foreach(MAUSP::where('id_ncc', $id_ncc)->get() as $model){
            foreach(SANPHAM::select('id')->distinct()->where('id_msp', $model->id)->get() as $product){
                array_push($lst_result, $product->id);
            }
        }

        return $lst_result;
    }

    // lấy sp theo id_sp
    public function getProductById($id_sp)
    {
        $id_msp = SANPHAM::find($id_sp)->id_msp;
        return $this->getProductByCapacity($id_msp, $id_sp);
    }

    // lấy thông tin sản phẩm cần thiết để so sánh
    public function getProductInformation($id_sp)
    {
        $productInfo = [
            'sanpham' => $this->getProductById($id_sp),
            'variation' => [
                'color' => []
            ],
            'cauhinh' => $this->getSpecifications($id_sp),
        ];
        
        $id_sp_list = $this->getListIdSameCapacity($id_sp);

        $productInfo['sanpham']['qtyInStock'] = $this->isProByCapInStocks($id_sp_list, true);

        // lấy màu sắc biến thể
        foreach($id_sp_list as $id_sp){
            $product = SANPHAM::find($id_sp);
            array_push($productInfo['variation']['color'], [
                'hinhanh' => $product->hinhanh,
                'mausac' => $product->mausac,
            ]);
        }

        return $productInfo;
    }

    // kiểm tra còn hạn khuyến mãi không
    public function promotionCheck($id_sp)
    {
        // không có khuyến mãi
        if(!SANPHAM::find($id_sp)->khuyenmai){
            return false;
        }

        $warranty = strtotime(str_replace('/', '-', SANPHAM::find($id_sp)->khuyenmai->ngayketthuc));
        $today = time();

        return $warranty >= $today ? true : false;
    }

    // sắp xếp danh sách sp theo khuyến mãi
    public function sortProductByPromotion($lst, $type = 'asc')
    {
        $length = count($lst);
        
        if($type == 'asc'){
            for($i = 0; $i < $length - 1; $i++){
                for($j = $i+1; $j < $length; $j++){
                    if($lst[$i]['khuyenmai'] > $lst[$j]['khuyenmai']){
                        $temp = $lst[$i];
                        $lst[$i] = $lst[$j];
                        $lst[$j] = $temp;
                    }
                }
            }
        } else {
            for($i = 0; $i < $length - 1; $i++){
                for($j = $i+1; $j < $length; $j++){
                    if($lst[$i]['khuyenmai'] < $lst[$j]['khuyenmai']){
                        $temp = $lst[$i];
                        $lst[$i] = $lst[$j];
                        $lst[$j] = $temp;
                    }
                }
            }
        }
        

        return $lst;
    }

    // sắp xếp theo giá giảm dần
    public function sortPrice($lst_product, $type = 'asc')
    {
        $length = count($lst_product);

        if($type === 'asc'){
            for($i = 0; $i < $length - 1; $i++){
                for($j = $i+1; $j < $length; $j++){
                    if($lst_product[$i]['gia'] > $lst_product[$j]['gia']){
                        $temp = $lst_product[$i];
                        $lst_product[$i] = $lst_product[$j];
                        $lst_product[$j] = $temp;
                    }
                }
            }
        } else {
            for($i = 0; $i < $length - 1; $i++){
                for($j = $i+1; $j < $length; $j++){
                    if($lst_product[$i]['gia'] < $lst_product[$j]['gia']){
                        $temp = $lst_product[$i];
                        $lst_product[$i] = $lst_product[$j];
                        $lst_product[$j] = $temp;
                    }
                }
            }
        }
        
        return $lst_product;
    }

    // lấy các loại ram hiện có
    public function getRamAllProduct()
    {
        $lst_ram = [];
        foreach(SANPHAM::select('ram')->distinct()->get() as $ram){
            array_push($lst_ram, $ram->ram);
        }

        return $lst_ram;
    }

    // lấy các loại dung lượng hiện có
    public function getCapacityAllProduct()
    {
        $lst_capacity = [];

        foreach(SANPHAM::select('dungluong')->distinct()->get() as $dungluong){
            array_push($lst_capacity, $dungluong->dungluong);
        }

        return $lst_capacity;
    }

    // lấy tên điện thoại từ chuỗi
    public function getProductIdByName($str)
    {
        $strList = explode('-', $str);

        $count = 0;
        $keywordList = ['GB', 'gb', 'TB', 'tb'];

        foreach($strList as $key){
            if(in_array($key, $keywordList)) {
                $count++;
            }
        }

        // không hợp lệ
        if($count === 0) {
            return false;
        }

        $data = [
            'tensp' => '',
            'ram' => '',
            'dungluong' => $strList[count($strList) - 2].' '.end($strList),
        ];

        // không có ram
        if($count === 1){
            for($i = 0; $i < 2; $i++){
                unset($strList[array_key_last($strList)]);
            }
        }
        // có ram
        else {
            $data['ram'] = $strList[count($strList) - 4].' '.$strList[count($strList) - 3];

            for($i = 0; $i < 4; $i++){
                unset($strList[array_key_last($strList)]);
            }
        }

        $name = '';
        foreach($strList as $key){
            $name .= $key.' ';
        }

        $data['tensp'] = trim($name);

        // lấy sản phẩm
        $query = SANPHAM::where('tensp', 'like', $data['tensp'])
                            ->where('dungluong', 'like', $data['dungluong']);

        if($data['ram']) {
            $query->where('ram', 'like', $data['ram']);
        }

        // sản phẩm không hợp lệ
        if(!$query->first()) {
            return false;
        }

        // lấy id_sp ngẫu nhiên
        $id_sp = $query->inRandomOrder()->first()->id;

        return $id_sp;
    }

    // lấy nhà cung cấp theo id_msp
    public function getSupplierByModelId($id_msp)
    {
        $temp = MAUSP::find($id_msp)->nhacungcap;

        $supplier = [];

        foreach($temp as $key){
            $supplier = [
                'id' => $temp['id'],
                'tenncc' => $temp['tenncc'],
                'brand' => explode(' ', $temp['tenncc'])[0],
                'anhdaidien' => $temp['anhdaidien'],
                'diachi' => $temp['diachi'],
                'sdt' => $temp['diachi'],
                'email' => $temp['email'],
                'trangthai' => $temp['trangthai'],
            ];
        }

        return $supplier;
    }

    // lấy thông tin khuyến mãi của sản phẩm
    public function getPromotionById($id)
    {
        $temp = KHUYENMAI::where('id', $id)->first();

        $promotion = [
            'id' => $temp->id,
            'tenkm' => $temp->tenkm,
            'noidung' => $temp->noidung,
            'chietkhau' => $temp->chietkhau,
            'ngaybatdau' => $temp->ngaybatdau,
            'ngayketthuc' => $temp->ngayketthuc,
            'trangthaikhuyenmai' => true,
            'trangthai' => $temp->trangthai,
        ];

        // hết hạn
        if(strtotime(str_replace('/', '-', $temp->ngayketthuc)) < time()){
            $promotion['trangthaikhuyenmai'] = false;
        }

        return $promotion;
    }

    // đọc file json thông số kỹ thuật
    public function getSpecifications($id_sp)
    {
        $fileName = SANPHAM::where('id', $id_sp)->first()->cauhinh;
        return json_decode(File::get(public_path('/json/' . $fileName)), true);
    }

    // lấy tài khoản theo id_tk
    public function getAccountById($id_tk)
    {
        $temp = TAIKHOAN::where('id', $id_tk)->first();
        
        $account = [
            'id' => $temp->id,
            //'sdt' => $temp->sdt,
            //'password' => $temp->password,
            //'email' => $temp->email,
            'hoten' => $temp->hoten,
            'anhdaidien' => $temp->htdn == 'normal' ? 'images/user/'.$temp->anhdaidien : $temp->anhdaidien,
            //'loaitk' => $temp->loaitk,
            //'htdn' => $temp->htdn,
            //'trangthai' => $temp->trangthai,
        ];

        return $account;
    }

    // lấy chi nhánh có hàng theo id_sp
    public function getBranchByProductId($id_sp)
    {
        $lst_branch = [];
        $i = 0;

        foreach(SANPHAM::find($id_sp)->kho as $key){
            if($key->pivot->slton != 0){
                $lst_branch[$i] = CHINHANH::where('id', $key->pivot->id_cn)->first();
                $i++;
            }
        }

        return $lst_branch;
    }

    // lấy ngẫu nhiên điện thoại cùng nhà cung cấp
    public function getRandomProductBySupplierId($id_sp, $id_ncc, $qty = 5)
    {
        $lst_product = [];

        $lst_id_msp = [];
        foreach(MAUSP::where('id_ncc', $id_ncc)->select('id')->get() as $model){
            if(SANPHAM::where('id_msp', $model->id)->first()){
                array_push($lst_id_msp, $model->id);
            }
        }

        // hãng có sl sp ít hơn sl cần hiển thị
        if(count($lst_id_msp) < $qty) {
            for($i = 0; $i < count($lst_id_msp); $i++){
                $phonesByCapacity = $this->getProductByCapacity($lst_id_msp[$i]);
                foreach($phonesByCapacity as $phone) {
                    if(count($phone) !== 0 && $this->isShow($phone)) {
                        array_push($lst_product, $phone);
                    }
                }
            }
        }
        // lấy random sp không trùng nhau và không trùng với sp đang xem tại trang chi  tiết
        else {
            $distinctList = [];
            // danh sách id_sp mẫu sp cùng dung lượng
            $id_sp_list = $this->getListIdSameCapacity($id_sp);

            while(count($lst_product) < $qty) {
                $rand_id_msp = array_rand($lst_id_msp);

                if(!in_array($rand_id_msp, $distinctList)) {
                    array_push($distinctList, $rand_id_msp);

                    $phonesByCapacity = $this->getProductByCapacity($lst_id_msp[$rand_id_msp]);
                    $phone = $phonesByCapacity[mt_rand(0 , count($phonesByCapacity) - 1)];

                    if($this->isShow($phone) && !in_array($phone['id'], $id_sp_list)) {
                        array_push($lst_product, $phone);
                    }
                }
            }
        }

        return $lst_product;
    }

    // lấy tất cả điện thoại cùng nhà cung cấp
    public function getAllProductBySupplierId($id_ncc)
    {
        $lst_product = [];

        foreach(NHACUNGCAP::find($id_ncc)->mausp as $model){
            $products = $this->getProductByCapacity($model->id);

            foreach($products as $product){
                if($this->isShow($product)) {
                    array_push($lst_product, $product);
                }
            }
        }

        return $lst_product;
    }

    // lấy ngẫu nhiên điện thoại tương tự trong tầm giá
    public function getProductByPriceRange($id_sp)
    {
        $phone = SANPHAM::find($id_sp);

        $id_msp = $phone->id_msp;

        // danh sách mẫu sp theo dung lượng không trùng với mẫu đang xem
        $lst_modelByCap = [];
        foreach(MAUSP::all() as $model){
            if($model->id != $id_msp){
                if(SANPHAM::where('id_msp', $model->id)->first()){
                    $phoneByCapacity = $this->getProductByCapacity($model->id);
    
                    foreach($phoneByCapacity as $key){
                        array_push($lst_modelByCap, $key);
                    }
                }
            }
            
        }

        // danh sách sản phẩm trong tầm giá:  1tr < giá sp < 1tr
        $lst_product = [];
        $higher = $phone->gia + 1000000;
        $lower = $phone->gia - 1000000;
        
        foreach($lst_modelByCap as $model){
            if($model['gia'] >= $lower && $model['gia'] <= $higher){
                array_push($lst_product, $model);
            }
        }

        return $lst_product;
    }

    // lấy mảng số ngẫu nhiên không trùng khớp
    public function getUniqueRandomNumber($min, $max, $qty)
    {
        $lst_rand = [];
        
        for($i = 0; $i < $qty; $i++){
            $rand = mt_rand($min, $max);

            if(count($lst_rand) != 0){
                while(1){
                    // nếu bị trùng thì random lại
                    if(in_array($rand, $lst_rand)){
                        $rand = mt_rand($min, $max);
                    } else {
                        $lst_rand[$i] = $rand;
                        break;
                    }
                }
            } else {
                $lst_rand[$i] = $rand;
            }
        }

        return $lst_rand;
    }

    // lấy đánh giá của mẫu sp cùng dung lượng
    public function getEvaluateByCapacity($lst_id_sp, $id_tk = null)
    {
        // lấy đánh giá của mẫu sp theo dung lượng
        $lst_evaluate = [
            'evaluate' => [],
            'total-rating' => 0,
            'rating' => [
                '5' => 0,
                '4' => 0,
                '3' => 0,
                '2' => 0,
                '1' => 0,
            ],
            'total-star' => 0,
        ];

        $lst_tempEvaluate = [];
        $evaluatesInRangeID_SP = [];

        // mảng đánh giá trong khoảng của id_sp
        $allEvaluates = DANHGIASP::all();
        foreach($allEvaluates as $evaluate) {
            if(in_array($evaluate->id_sp, $lst_id_sp)) {
                array_push($lst_tempEvaluate, [
                    'id' => $evaluate->id,
                    'taikhoan' => $this->getAccountById($evaluate->id_tk),
                    'sanpham' => [
                        'mausac' => $this->getProductById($evaluate->id_sp)['mausac'],
                    ],
                    'noidung' => $evaluate->noidung,
                    'hinhanh' => $this->getEvaluateDetail($evaluate->id),
                    'thoigian' => $evaluate->thoigian,
                    'soluotthich' => $evaluate->soluotthich,
                    'danhgia' => $evaluate->danhgia,
                    'chinhsua' => $evaluate->chinhsua
                ]);
            }
        }

        // foreach($evaluatesInRangeID_SP as $evaluate){
        //     // hình ảnh của đánh giá
        //     $imageEvaluate = $this->getEvaluateDetail($evaluate->id);

        //     array_push($lst_tempEvaluate, [
        //         'id' => $evaluate->id,
        //         'taikhoan' => $this->getAccountById($evaluate->id_tk),
        //         'sanpham' => [
        //             'mausac' => $this->getProductById($evaluate->id_sp)['mausac'],
        //         ],
        //         'noidung' => $evaluate->noidung,
        //         'hinhanh' => $imageEvaluate,
        //         'thoigian' => $evaluate->thoigian,
        //         'soluotthich' => $evaluate->soluotthich,
        //         'danhgia' => $evaluate->danhgia,
        //         'chinhsua' => $evaluate->chinhsua
        //     ]);
        // }

        $idx = 0;

        // gộp và lấy đánh giá
        /*mô tả: vòng for chạy từ dòng đầu tiên, nếu các dòng tiếp theo thuộc về 1 đánh giá
                thì gôm chúng lại, lấy màu sắc của chúng cộng vào dòng đầu tiên. ta được 1 đánh giá hoàn chỉnh*/
        for($i = 0; $i < count($lst_tempEvaluate); $i++){
            // dòng đầu tiên
            $first = $lst_tempEvaluate[$i];
            // mảng các dòng giống nhau
            $temp = [];
            // số dòng giống nhau
            $step = 0;
            // for so sánh dòng đầu tiên với các dòng còn lại
            for($j = $i + 1; $j < count($lst_tempEvaluate); $j++){
                // giống nhau
                if($first['taikhoan']['id'] == $lst_tempEvaluate[$j]['taikhoan']['id'] &&
                    $first['noidung'] == $lst_tempEvaluate[$j]['noidung'] &&
                    $first['thoigian'] == $lst_tempEvaluate[$j]['thoigian']){
                    // thêm vào mảng các dòng giống nhau                   
                    array_push($temp, $lst_tempEvaluate[$j]);
                    // tăng số dòng giống nhau
                    $step++;
                }
                // nếu khác nhau thì thoát vòng lặp
                else {
                    break;
                }
            }

            // nếu có dòng giống nhau thì cộng màu vào dòng đầu tiên
            if(!empty($temp)){
                // đẩy dòng đầu tiên vào mảng chính
                $lst_evaluate['evaluate'][$idx] = $first;
                // chỉnh thời gian: d/m/Y h:i
                $time = $lst_evaluate['evaluate'][$idx]['thoigian'];
                $lst_evaluate['evaluate'][$idx]['thoigian'] = substr_replace($time, '', strlen($time) - 3);
                // cộng màu
                foreach($temp as $key){
                    $lst_evaluate['evaluate'][$idx]['sanpham']['mausac'] .= ', '. $key['sanpham']['mausac'];
                }

                // đi đến vị trí của dòng mới
                $i += $step;
            }
            // không có dòng nào giống nhau: đánh giá khác
            else {
                $lst_evaluate['evaluate'][$idx] = $first;
                // chỉnh thời gian: d/m/Y h:i
                $time = $lst_evaluate['evaluate'][$idx]['thoigian'];
                $lst_evaluate['evaluate'][$idx]['thoigian'] = substr_replace($time, '', strlen($time) - 3);
            }

            //kiểm tra tài khoản đang đăng nhập có thích bình luận hay không
            if($id_tk){
                LUOTTHICH::where('id_tk', $id_tk)->where('id_dg', $lst_evaluate['evaluate'][$idx]['id'])->first() ? 
                $lst_evaluate['evaluate'][$idx]['liked'] = true : $lst_evaluate['evaluate'][$idx]['liked'] = false;
            } else {
                $lst_evaluate['evaluate'][$idx]['liked'] = false;
            }

            // sao đánh giá
            if($lst_tempEvaluate[$i]['danhgia'] == 1){
                $lst_evaluate['rating']['1']++;
            } elseif($lst_tempEvaluate[$i]['danhgia'] == 2){
                $lst_evaluate['rating']['2']++;
            } elseif($lst_tempEvaluate[$i]['danhgia'] == 3){
                $lst_evaluate['rating']['3']++;
            } elseif($lst_tempEvaluate[$i]['danhgia'] == 4){
                $lst_evaluate['rating']['4']++;
            } else {
                $lst_evaluate['rating']['5']++;
            }

            // danh sách phản hồi
            $id_dg = $lst_evaluate['evaluate'][$idx]['id'];

            // phản hồi đầu tiên
            $firstReply = PHANHOI::where('id_dg', $id_dg)->orderBy('id', 'desc')->first();
            $lst_evaluate['evaluate'][$idx]['phanhoi-qty'] = 0;
            $lst_evaluate['evaluate'][$idx]['phanhoi-first'] = null;

            if($firstReply){
                $reply_id_tk = $firstReply->id_tk;
                $firstReply->thoigian = substr_replace($firstReply->thoigian, '', strlen($firstReply->thoigian) - 3);
                $firstReply->taikhoan = $this->getAccountById($reply_id_tk);
    
                $lst_evaluate['evaluate'][$idx]['phanhoi-qty'] = PHANHOI::where('id_dg', $id_dg)->count();
                $lst_evaluate['evaluate'][$idx]['phanhoi-first'] = $firstReply;
            }

            $idx++;

            $lst_evaluate['total-rating']++;
        }

        if($lst_evaluate['total-rating'] == 0){
            $lst_evaluate['total-star'] = 0;    
        } else {
            $_5s = 5 * $lst_evaluate['rating']['5'];
            $_4s = 4 * $lst_evaluate['rating']['4'];
            $_3s = 3 * $lst_evaluate['rating']['3'];
            $_2s = 2 * $lst_evaluate['rating']['2'];
            $_1s = 1 * $lst_evaluate['rating']['1'];
    
            $lst_evaluate['total-star'] = ($_5s + $_4s + $_3s + $_2s + $_1s) / $lst_evaluate['total-rating'];
        }

        // sắp xếp đánh giá mới nhất ở trên đầu
        $lst_evaluate['evaluate'] = array_reverse($lst_evaluate['evaluate'], false);
        
        return $lst_evaluate;
    }

    // lấy hình ảnh đánh giá theo id_dg
    public function getEvaluateDetail($id_dg)
    {
        $lst_imageEvaluate = [];

        $evaluateDetail = DANHGIASP::find($id_dg)->ctdg;

        foreach(DANHGIASP::find($id_dg)->ctdg as $key){
            $data = [
                'id' => $key['id'],
                'id_dg' => $key['id_dg'],
                'hinhanh' => $key['hinhanh'],
            ];

            array_push($lst_imageEvaluate, $data);
        }

        return $lst_imageEvaluate;
    }

    // lấy các đánh giá đã thích theo id_tk
    public function getEvaluateLiked($id_tk)
    {
        return TAIKHOAN::find($id_tk)->luotthich;
    }

    // lấy phản hồi đánh giá theo id_dg
    public function getReply($id_dg)
    {
        $lst_reply = [];
        foreach(PHANHOI::where('id_dg', $id_dg)->orderBy('id', 'desc')->get() as $i => $key){
            $data = [
                'id' => $key['id'],
                'taikhoan' => $this->getAccountById($key['id_tk']),
                'id_dg' => $id_dg,
                'noidung' => $key['noidung'],
                'thoigian' => substr_replace($key['thoigian'], '', strlen($key['thoigian']) - 3),
                'trangthai' => $key['trangthai'],
            ];

            array_push($lst_reply, $data);
        }

        return $lst_reply;
    }

    // lấy địa chỉ mặc định
    public function getAddressDefault($id_tk)
    {
        return TAIKHOAN_DIACHI::where('id_tk', $id_tk)->where('macdinh', 1)->first() == null ? null : TAIKHOAN_DIACHI::where('id_tk', $id_tk)->where('macdinh', 1)->first();
    }

    // hàm loại bỏ ký tự có dấu
    public function unaccent($str) {

        $array = [
            'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
            'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
            'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
            'đ' => 'd',
            'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
            'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
            'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
            'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
            'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
            'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
            'A' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A',
            'Â' => 'A', 'Ấ' => 'A', 'Ầ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A',
            'Ă' => 'A', 'Ắ' => 'A', 'Ằ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A',
            'Đ' => 'D',
            'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E',
            'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O',
            'Ô' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O',
            'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U',
            'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U',
            'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y'
        ];

        $str = str_replace(array_keys($array), array_values($array), $str);
        return $str;
    }

    // cập nhật session user
    public function userSessionUpdate()
    {
        $user = TAIKHOAN::where('id', session('user')->id)->first();
        Session::forget('user');
        session(['user' => $user]);
    }

    // lấy giỏ hàng của tài khoản
    public function getCart($id_tk)
    {
        $cart = [
            'cart' => [],
            'qty' => 0,
            'total' => 0,
        ];

        $i = 0;

        // giỏ hàng rỗng
        if(count(TAIKHOAN::find($id_tk)->giohang) == 0){
            return $cart;
        }

        // giỏ hàng của người dùng
        foreach(TAIKHOAN::find($id_tk)->giohang as $key){
            $product = $this->getProductById($key->pivot->id_sp);
            $thanhtien = intval($product['giakhuyenmai'] * $key->pivot->sl);

            $item = [
                'id' => $key->pivot->id,
                'sanpham' => $product,
                'sl' => $key->pivot->sl,
                'thanhtien' => $thanhtien,
                'hethang' => false,
            ];

            // kiểm tra slton của sản phẩm
            $qtyInStock = KHO::where('id_sp', $key->pivot->id_sp)->sum('slton');

            // đánh dấu sp hết hàng
            if(!$qtyInStock){
                $item['hethang'] = true;
            } else {
                $cart['total'] += $item['thanhtien'];
            }

            array_push($cart['cart'], $item);
            
            $cart['qty']++;
        }

        return $cart;
    }

    // lấy đơn hàng theo id
    public function getOrderById($id_dh)
    {
        $data = [
            'order' => [],
            'detail' => [],
        ];

        // đơn hàng
        $data['order'] = DONHANG::find($id_dh);

        // địa chỉ giao hàng
        $data['order']->hinhthuc == 'Giao hàng tận nơi' ? $data['order']->diachigiaohang = DONHANG_DIACHI::find($data['order']->id_dh_dc) : null;

        // chi nhánh
        $data['order']->hinhthuc == 'Nhận tại cửa hàng' ? $data['order']->chinhanh = CHINHANH::find($data['order']->id_cn) : null;

        // voucher
        $data['order']->voucher ? VOUCHER::find($data['order']->voucher) : null;
        
        // chi tiết đơn hàng
        $data['detail'] = $this->getOrderDetail($id_dh);

        return $data;
    }

    // lấy chi tiết đơn hàng theo id đơn hàng
    public function getOrderDetail($id_dh)
    {
        $lst_detail = [];
        $i = 0;
        foreach(DONHANG::find($id_dh)->ctdh as $key){
            $lst_detail[$i] = [
                'id_dh' => $key->pivot->id_dh,
                'sanpham' => $this->getProductById($key->pivot->id_sp),
                'gia' => $key->pivot->gia,
                'sl' => $key->pivot->sl,
                'giamgia' => $key->pivot->giamgia,
                'thanhtien' => $key->pivot->thanhtien,
            ];

            $i++;
        }

        return $lst_detail;
    }

    // lấy định dạng hình
    public function getImageFormat($base64)
    {
        $formatBase64 = explode(';', $base64)[0];
        return substr($formatBase64, 11, strlen($formatBase64));
    }

    // lưu hình ảnh
    public function saveImage($url, $base64)
    {
        $image = base64_decode($base64);
        file_put_contents($url, $image);
    }
}
