<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\DashboardController;

use App\Http\Controllers\user\IndexController;
use App\Http\Controllers\user\CartController;
use App\Http\Controllers\user\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web Routes for your application. These
| Routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post("signup", [UserController::class, "SignUp"])->name("user/signup");

Route::post("login", [UserController::class, "Login"])->name("user/login");

Route::get("logout", [UserController::class, "LogOut"])->name("user/logout")->middleware("PreventBackHistory");

Route::get("khoiphuctaikhoan", [UserController::class, "KhoiPhucTaiKhoan"])->name("user/khoi-phuc-tai-khoan");

Route::post("recover-account", [UserController::class, "RecoverAccount"])->name("user/recover-account");

Route::get("thongbao", [IndexController::class, "ThongBao"])->name("user/thongbao");

Route::group(["prefix" => "", "namespace" => "user", "middleware" => ["IsAdmin", "AccessTimes"]], function(){
    /*=======================================================================================================
                                                                Page
    =========================================================================================================*/
    
    Route::get("dangnhap", [UserController::class, "DangNhap"])->name("user/dang-nhap")->middleware("PreventBackHistory");
    
    Route::get("dangky", [UserController::class, "DangKy"])->name("user/dang-ky");

    Route::get("auth/facebook/redirect", [UserController::class, "FacebookRedirect"])->name("user/facebook-redirect");

    Route::get("auth/facebook/callback", [UserController::class, "FacebookCallback"]);

    Route::get("auth/google/redirect", [UserController::class, "GoogleRedirect"])->name("user/google-redirect");

    Route::get("auth/google/callback", [UserController::class, "GoogleCallback"]);

    Route::get("/",[IndexController::class, "Index"])->name("user/index");

    Route::get("dienthoai", [IndexController::class, "DienThoai"])->name("user/dien-thoai");

    Route::get("timkiem", [IndexController::class, "TimKiemDienThoai"])->name("user/tim-kiem");

    Route::get("dienthoai/{name}", [IndexController::class, "ChiTiet"])->name("user/chi-tiet");

    Route::get("sosanh/{str}", [IndexController::class, "SoSanh"])->name("user/so-sanh");

    Route::get("thanhcong", [CartController::class, "ThanhCong"])->name("user/thanhcong");

    Route::get("tracuu", [IndexController::class, "TraCuu"])->name("user/tra-cuu");

    Route::get("lienhe", [IndexController::class, "LienHe"])->name("user/lien-he");

    /*=======================================================================================================
                                                        Ajax
    =========================================================================================================*/

    Route::get("ajax-get-user-fullname", [IndexController::class, "AjaxGetUserFullname"]);

    Route::get("ajax-forget-login-status-session", [IndexController::class, "AjaxForgetLoginStatusSession"]);

    Route::post("ajax-search-phone", [IndexController::class, "AjaxSearchPhone"]);

    Route::post("ajax-filter-product", [IndexController::class, "AjaxFilterProduct"]);

    Route::post("ajax-choose-color", [IndexController::class, "AjaxChooseColor"]);

    Route::post("ajax-get-qty-in-stock", [IndexController::class, "AjaxGetQtyInStock"]);

    Route::post("ajax-add-delete-favorite", [UserController::class, "AjaxAddDeleteFavorite"]);

    Route::post("ajax-add-cart", [CartController::class, "AjaxAddCart"]);

    Route::post("ajax-update-cart", [CartController::class, "AjaxUpdateCart"]);

    Route::post("ajax-like-comment", [UserController::class, "AjaxLikeComment"]);

    Route::post("ajax-change-location", [IndexController::class, "AjaxChangeLocation"]);

    Route::post("ajax-check-imei", [IndexController::class, "AjaxCheckImei"]);

    Route::post("zalopay/callback", [IndexController::class, "ZaloPayCallback"]);

    Route::post("ajax-phone-number-is-exists", [UserController::class, "AjaxPhoneNumberIsExists"]);

    Route::get("test5", "IndexController@test5");

    Route::post("ajax-get-type-notification", [UserController::class, "AjaxGetTypeNotification"]);

    Route::post("ajax-load-more", [IndexController::class, "AjaxLoadMore"]);

    Route::post("ajax-get-product-by-brand", [IndexController::class, "AjaxGetProductByBrand"]);

    Route::get("ajax-reduce-warehouse-temporary", [IndexController::class, "AjaxReduceWarehouseTemporary"]);

    Route::get("ajax-backup-warehouse", [IndexController::class, "AjaxBackupWarehouse"]);

    Route::post("ajax-checkout-queue", [IndexController::class, "AjaxCheckoutQueue"]);

    route::post("ajax-remove-queue", [IndexController::class, "AjaxRemoveQueue"]);

    Route::post("ajax-bind-address", [IndexController::class, "AjaxBindAddress"]);

    Route::post("ajax-get-cart-by-id-sp-list", [CartController::class, "AjaxGetCartByIdProductList"]);

    Route::post("ajax-get-provisional-order", [CartController::class, "AjaxGetProvisionalOrder"]);

    Route::post("ajax-get-branch-list", [IndexController::class, "AjaxGetBranchList"]);

    Route::post("ajax-get-branch-with-qty-in-stock", [IndexController::class, "AjaxGetBranchWithQtyInStock"]);

    route::get("ajax-get-total-qty-pro-by-cap", [IndexController::class, "AjaxGetTotalQtyProByCap"]);

    Route::middleware("CheckLogin")->group(function(){
        /*=======================================================================================================
                                                        Page
        =========================================================================================================*/

        Route::get("giohang", [CartController::class, "GioHang"])->name("user/gio-hang");
        
        Route::get("thanhtoan", [CartController::class, "ThanhToan"])->name("user/thanh-toan");

        Route::get("diachigiaohang", [UserController::class, "DiaChiGiaoHang"]);

        Route::get("taikhoan", [UserController::class, "TaiKhoan"])->name("user/tai-khoan");

        Route::get("taikhoan/thongbao", [UserController::class, "ThongBao"])->name("user/tai-khoan-thong-bao");
            
        Route::get("taikhoan/donhang", [UserController::class, "DonHang"])->name("user/tai-khoan-don-hang");
    
        Route::get("taikhoan/diachi", [userController::class, "DiaChi"])->name("user/tai-khoan-dia-chi");
        
        Route::get("taikhoan/donhang/{id}", [UserController::class, "ChiTietDonHang"])->name("user/tai-khoan-chi-tiet-don-hang");
        
        Route::get("taikhoan/yeuthich", [UserController::class, "YeuThich"])->name("user/tai-khoan-yeu-thich");
        
        Route::get("taikhoan/voucher", [UserController::class, "Voucher"])->name("user/tai-khoan-voucher");

        /*=======================================================================================================
                                                        Form submit
        =========================================================================================================*/

        Route::post("change-address-delivery", [UserController::class, "ChangeAddressDelivery"])->name("user/change-address-delivery");
        
        Route::post("ajax-create-update-address", [UserController::class, "AjaxCreateUpdateAddress"]);

        Route::post("ajax-set-default-address", [UserController::class, "AjaxSetDefaultAddress"]);

        Route::post("ajax-delete-object", [UserController::class, "AjaxDeleteObject"]);

        Route::post("ajax-change-avatar", [UserController::class, "AjaxChangeAvatar"]);

        Route::post("checkout", [CartController::class, "Checkout"])->name("user/checkout");

        /*=======================================================================================================
                                                        Ajax
        =========================================================================================================*/

        Route::post("ajax-change-fullname", [UserController::class, "AjaxChangeFullname"]);

        Route::post("ajax-change-password", [UserController::class, "AjaxChangePassword"]);

        Route::post("ajax-check-noti", [UserController::class, "AjaxCheckNoti"]);

        Route::post("ajax-delete-noti", [UserController::class, "AjaxDeleteNoti"]);

        Route::get("ajax-check-all-noti", [UserController::class, "AjaxCheckAllNoti"]);

        Route::get("ajax-delete-all-noti", [UserController::class, "AjaxDeleteAllNoti"]);

        Route::post("ajax-delete-favorite", [UserController::class, "AjaxDeleteFavorite"]);

        Route::get("ajax-delete-all-favorite", [UserController::Class, "AjaxDeleteAllFavorite"]);

        Route::post("ajax-check-qty-in-stock-branch", [IndexController::class, "AjaxCheckQtyInStockBranch"]);

        Route::post("apply-voucher", [UserController::Class, "ApplyVoucher"]);

        Route::post("ajax-check-voucher-conditions", [UserController::class, "CheckVoucherConditions"]);

        Route::post("ajax-choose-phone-to-evaluate", [UserController::class, "AjaxChoosePhoneToEvaluate"]);

        Route::get("ketquathanhtoan", [CartController::class, "ketQuaThanhToan"]);

        Route::post("ajax-create-evaluate", [UserController::class, "AjaxCreateEvaluate"]);

        Route::post("ajax-upload-single-image-evaluate", [UserController::class, "AjaxUploadSingleImageEvaluate"]);

        Route::post("ajax-edit-evaluate", [UserController::class, "AjaxEditEvaluate"]);

        Route::post("ajax-reply", [UserController::class, "AjaxReply"]);

        Route::post("ajax-get-all-reply", [IndexController::class, "AjaxGetAllReply"]);

        Route::post("ajax-update-queue-status", [IndexController::class, "AjaxUpdateQueueStatus"]);
        
        Route::post("ajax-recover-queue-status", [IndexController::class, "AjaxRecoverQueueStatus"]);

        Route::post("ajax-delete-expired-voucher", [UserController::class, "AjaxDeleteExpiredVoucher"]);

        Route::get("ajax-remove-voucher", [UserController::class, "AjaxRemoveVoucher"]);

        Route::get("ajax-is-applied-voucher", [UserController::class, "AjaxIsAppliedVoucher"]);

        Route::get("ajax-is-expired-voucher", [UserController::class, "AjaxIsExpiredVoucher"]);

        Route::post("ajax-check-satisfied-voucher", [UserController::class, "AjaxCheckSatisfiedVoucher"]);
    });
});

Route::group(["prefix" => "admin", "namespace" => "admin", "middleware" => "AdminLogin"], function() {
    Route::get("/", [DashboardController::class, "index"])->name("admin/dashboard");
    Route::resource("hinhanh", HinhAnhController::class);
    Route::resource("banner", BannerController::class);
    Route::resource("slideshow", SlideshowController::class);
    Route::resource("mausanpham", MauSanPhamController::class);
    Route::resource("sanpham", SanPhamController::class);
    Route::resource("nhacungcap", NhaCungCapController::class);
    Route::resource("danhgia", DanhGiaController::class);
    Route::resource("khuyenmai", KhuyenMaiController::class);
    Route::resource("donhang", DonHangController::class);
    Route::resource("baohanh", BaoHanhController::class);
    Route::resource("taikhoan", TaiKhoanController::class);
    Route::resource("slideshow-msp", SlideshowMSPController::class);
    Route::resource("kho", KhoController::class);
    Route::resource("chinhanh", ChiNhanhController::class);
    Route::resource("tinhthanh", TinhThanhController::class);
    Route::resource("voucher", VoucherController::class);
    Route::resource("imei", ImeiController::class);

    Route::resource("giohang", GioHangController::class);
    Route::resource("spyeuthich", SPYeuThichController::class);
    Route::resource("luotthich", LuotThichController::class);
    Route::resource("phanhoi", PhanHoiController::class);
    Route::resource("taikhoandiachi", TaiKhoanDiaChiController::class);
    Route::resource("thongbao", ThongBaoController::class);
    Route::resource("taikhoanvoucher", TaiKhoanVoucherController::class);
    Route::resource("chitietdanhgia", CTDGController::class);
    /*=======================================================================================================
                                                        Ajax
    =========================================================================================================*/

    Route::post("ajax-load-more", [DashboardController::class, "AjaxLoadMore"]);

    Route::post("ajax-get-sales-of-year", [DashboardController::class, "AjaxGetSalesOfYear"]);
    Route::post("ajax-get-supplier-of-year", [DashboardController::class, "AjaxGetSupplierOfYear"]);
    /*=======================================================================================================
                                                        Mẫu sp
    =========================================================================================================*/
    
    Route::post("mausanpham/ajax-get-mausp", [App\Http\Controllers\admin\MauSanPhamController::class, "AjaxGetMausp"]);

    Route::post("mausanpham/ajax-restore", [App\Http\Controllers\admin\MauSanPhamController::class, "AjaxRestore"]);
    
    Route::post("mausanpham/ajax-search", [App\Http\Controllers\admin\MauSanPhamController::class, "AjaxSearch"]);

    Route::post("mausanpham/ajax-filter", [App\Http\Controllers\admin\MauSanPhamController::class, "AjaxFilter"]);

    /*=======================================================================================================
                                                        Khuyến mãi
    =========================================================================================================*/

    Route::post("khuyenmai/ajax-get-khuyenmai", [App\Http\Controllers\admin\KhuyenMaiController::class, "AjaxGetKhuyenMai"]);

    Route::post("khuyenmai/ajax-search", [App\Http\Controllers\admin\KhuyenMaiController::class, "AjaxSearch"]);

    /*=======================================================================================================
                                                        Sản phẩm
    =========================================================================================================*/

    Route::post("sanpham/ajax-get-specifications-list", [App\Http\Controllers\admin\SanPhamController::class, "AjaxGetSpecificationsList"]);

    Route::post("sanpham/ajax-get-sanpham", [App\Http\Controllers\admin\SanPhamController::class, "AjaxGetSanPham"]);

    Route::post("sanpham/ajax-restore", [App\Http\Controllers\admin\SanPhamController::class, "AjaxRestore"]);

    Route::post("sanpham/ajax-search", [App\Http\Controllers\admin\SanPhamController::class, "AjaxSearch"]);

    Route::post("sanpham/ajax-get-model-list", [App\Http\Controllers\admin\SanPhamController::class, "AjaxGetModelList"]);

    Route::post("sanpham/ajax-filtersort", [App\Http\Controllers\admin\SanPhamController::class, "AjaxFilterSort"]);

    Route::post("sanpham/ajax-get-model-image", [App\Http\Controllers\admin\SanPhamController::class, "AjaxGetModelImage"]);

    /*=======================================================================================================
                                                        Nhà cung cấp
    =========================================================================================================*/

    Route::post("nhacungcap/ajax-get-ncc", [App\Http\Controllers\admin\NhaCungCapController::class, "AjaxGetNCC"]);

    Route::post("nhacungcap/ajax-restore", [App\Http\Controllers\admin\NhaCungCapController::class, "AjaxRetore"]);

    Route::post("nhacungcap/ajax-search", [App\Http\Controllers\admin\NhaCungCapController::class, "AjaxSearch"]);

    /*=======================================================================================================
                                                        Slideshow msp
    =========================================================================================================*/

    Route::post("slideshow-msp/ajax-get-slideshow-msp", [App\Http\Controllers\admin\SlideshowMSPController::class, "AjaxGetSlideshowMSP"]);

    Route::POST("slideshow-msp/ajax-get-model-havenot-slideshow", [App\Http\Controllers\admin\SlideshowMSPController::class, "AjaxGetModelHaveNotSlideshow"]);

    Route::post("slideshow-msp/ajax-search", [App\Http\Controllers\admin\SlideshowMSPController::class, "AjaxSearch"]);

    Route::post("slideshow-msp/ajax-add-single-file", [App\Http\Controllers\admin\SlideshowMSPController::class, "AjaxAddSingleFile"]);

    Route::post("slideshow-msp/ajax-update-single-file", [App\Http\Controllers\admin\SlideshowMSPController::class, "AjaxUpdateSingleFile"]);

    /*=======================================================================================================
                                                        Hình ảnh
    =========================================================================================================*/

    Route::post("hinhanh/ajax-get-model-havenot-image", [App\Http\Controllers\admin\HinhAnhController::class, "AjaxGetModelHaveNotImage"]);

    Route::post("hinhanh/ajax-get-hinhanh", [App\Http\Controllers\admin\HinhAnhController::class, "AjaxGetHinhAnh"]);

    Route::post("hinhanh/ajax-search", [App\Http\Controllers\admin\HinhAnhController::class, "AjaxSearch"]);

    Route::post("hinhanh/ajax-add-single-file", [App\Http\Controllers\admin\HinhAnhController::class, "AjaxAddSingleFile"]);

    Route::post("hinhanh/ajax-update-single-file", [App\Http\Controllers\admin\HinhAnhController::class, "AjaxUpdateSingleFile"]);

    /*=======================================================================================================
                                                        Kho
    =========================================================================================================*/

    Route::post("kho/ajax-get-kho", [App\Http\Controllers\admin\KhoController::class, "AjaxGetKho"]);

    Route::post("kho/ajax-get-product-isnot-in-stock", [App\Http\Controllers\admin\KhoController::class, "AjaxGetProductIsNotInStock"]);

    Route::post("kho/ajax-get-product-by-id", [App\Http\Controllers\admin\KhoController::class, "AjaxGetProductById"]);

    Route::post("kho/ajax-search", [App\Http\Controllers\admin\KhoController::class, "AjaxSearch"]);

    Route::post("kho/ajax-filter", [App\Http\Controllers\admin\KhoController::class, "AjaxFilter"]);

    /*=======================================================================================================
                                                        Chi nhánh
    =========================================================================================================*/

    Route::post("chinhanh/ajax-get-chinhanh", [App\Http\Controllers\admin\ChiNhanhController::class, "AjaxGetChiNhanh"]);

    Route::post("chinhanh/ajax-restore", [App\Http\Controllers\admin\ChiNhanhController::class, "AjaxRestore"]);

    Route::post("chinhanh/ajax-search", [App\Http\Controllers\admin\ChiNhanhController::class, "AjaxSearch"]);

    /*=======================================================================================================
                                                        Tỉnh thành
    =========================================================================================================*/

    Route::post("tinhthanh/ajax-get-tinhthanh", [App\Http\Controllers\admin\TinhThanhController::class ,"AjaxGetTinhThanh"]);

    Route::post("tinhthanh/ajax-search", [App\Http\Controllers\admin\TinhThanhController::class ,"AjaxSearch"]);

    /*=======================================================================================================
                                                        Voucher
    =========================================================================================================*/

    Route::post("voucher/ajax-get-voucher", [App\Http\Controllers\admin\VoucherController::class, "AjaxGetVoucher"]);

    Route::post("voucher/ajax-search", [App\Http\Controllers\admin\VoucherController::class, "AjaxSearch"]);

    /*=======================================================================================================
                                                        Đơn hàng
    =========================================================================================================*/

    Route::post("donhang/ajax-get-donhang", [App\Http\Controllers\admin\DonHangController::class, "AjaxGetDonHang"]);

    Route::post("donhang/ajax-order-confirmation", [App\Http\Controllers\admin\DonHangController::class, "AjaxOrderConfirmation"]);

    Route::post("donhang/ajax-successful-order", [App\Http\Controllers\admin\DonHangController::class, "AjaxSuccessfulOrder"]);

    Route::post("donhang/ajax-search", [App\Http\Controllers\admin\DonHangController::class, "AjaxSearch"]);

    Route::post("donhang/ajax-filter-sort", [App\Http\Controllers\admin\DonHangController::class, "AjaxFilterSort"]);

    /*=======================================================================================================
                                                        Bảo hành
    =========================================================================================================*/

    Route::post("baohanh/ajax-get-baohanh", [App\Http\Controllers\admin\BaoHanhController::class, "AjaxGetBaoHanh"]);

    Route::post("baohanh/ajax-search", [App\Http\Controllers\admin\BaoHanhController::class, "AjaxSearch"]);

    /*=======================================================================================================
                                                        Slideshow
    =========================================================================================================*/

    Route::post("slideshow/ajax-get-slideshow", [App\Http\Controllers\admin\SlideshowController::class, "AjaxGetslideshow"]);

    /*=======================================================================================================
                                                        Banner
    =========================================================================================================*/

    Route::post("banner/ajax-get-banner", [App\Http\Controllers\admin\BannerController::class, "AjaxGetBanner"]);

    /*=======================================================================================================
                                                        IMEI
    =========================================================================================================*/

    Route::post("imei/ajax-search", [App\Http\Controllers\admin\ImeiController::class, "AjaxSearch"]);
    Route::post("ajax-get-hinhanh", [DashboardController::class, "AjaxGetHinhAnh"]);

    Route::get("checkPhone", [App\Http\Controllers\admin\TaiKhoanController::class, "checkPhone"]);
    Route::get("searchAccount", [App\Http\Controllers\admin\TaiKhoanController::class, "searchName"]);
    Route::get("filterAccount", [App\Http\Controllers\admin\TaiKhoanController::class, "filterAccount"]);
    Route::get("filterNotification", [App\Http\Controllers\admin\ThongBaoController::class, "filterNotification"]);
    Route::get("searchReview", [App\Http\Controllers\admin\DANHGIAController::class, "searchReview"]);
    Route::get("filterReview", [App\Http\Controllers\admin\DANHGIAController::class, "filterReview"]);
    Route::get("searchAccountAddress", [App\Http\Controllers\admin\TaiKhoanDiaChiController::class, "searchAccountAddress"]);
    Route::get("searchAccountVoucher", [App\Http\Controllers\admin\TaiKhoanVoucherController::class, "searchAccountVoucher"]);
    Route::get("searchCart", [App\Http\Controllers\admin\GioHangController::class, "searchCart"]);
    Route::get("searchWishList", [App\Http\Controllers\admin\SPYeuThichController::class, "searchWishList"]);
    Route::get("searchNotification", [App\Http\Controllers\admin\ThongBaoController::class, "searchNotification"]);
});



/*
GET	    /product	        		index	product.index
GET	    /product/create	    		create	product.create
POST	/product					store	product.store
GET		/product/{product}			show	product.show
GET		/product/{product}/edit		edit	product.edit
PUT/PATCH	/product/{product}		update	product.update
DELETE	/ product/{product}			destroy	product.destroy
*/

