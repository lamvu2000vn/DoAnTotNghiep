@extends("user.layout")
@section("title")Giỏ hàng | LDMobile @stop
@section("content")

@section("breadcrumb")
    <a href="{{route('user/gio-hang')}}" class="bc-item active">Giỏ hàng</a>
@stop
@include("user.content.section.sec-thanh-dieu-huong")

<section class='pt-50 pb-50'>
    <div class='container'>
        <div id="cart-container" class='row'>
            @if ($cart['qty'] != 0)
                <div class="cart-title">Giỏ hàng</div>
                <i class="mb-10">* Số lượng mua cho 1 sản phẩm tối đa là 5</i>
                <div class="col-lg-9 col-12">
                    <div class="cart-header">
                        <div class="w-5">
                            <div data-id="all" class="select-item-cart cus-checkbox"></div>
                        </div>
                        <div id="cart-header-qty" class="w-35">Chọn tất cả ({{$cart['qty']}} sản phẩm)</div>
                        <div class="w-25">Giá</div>
                        <div class="w-15">Số lượng</div>
                        <div class="w-15">Thành tiền</div>
                        <div class="w-5"><span class="relative remove-all-cart"><i class="fal fa-trash-alt"></i></span></div>
                    </div>

                    {{-- danh sách sản phẩm --}}
                    <div id="lst-cart-item" class="box-shadow mb-50">
                        @foreach ($cart['cart'] as $key)
                            <?php $product = $key['sanpham']; ?>

                            <div data-id="{{$product['id']}}" cart-id="{{$key['id']}}" class="cart-item-wrapper">
                                {{-- custom checkbox --}}
                                <div class="w-5">
                                    {{-- chỉ checked sản phẩm có thể thanh toán --}}
                                    @if (!$product['trangthai'] || $key['hethang'])
                                        <div data-id="{{$product['id']}}"
                                        class="select-item-cart cus-checkbox"></div>
                                    @else
                                        <div data-id="{{$product['id']}}"
                                        class="select-item-cart cus-checkbox cus-checkbox-checked"></div>                                        
                                    @endif
                                </div>
                                {{-- sản phẩm --}}
                                <div class="w-35 d-flex">
                                    <img src="{{$url_phone.$product['hinhanh']}}" alt="" class="w-30">
                                    <div class="ml-5">
                                        <a href="{{route('user/chi-tiet', ['name' => $product['tensp_url'], 'mausac' => $product['mausac_url']])}}" class="cart-phone-name">
                                            {{$product['tensp']}}
                                        </a>
                                        <div class="fz-14">Màu sắc: {{$product['mausac']}}</div>
                                    </div>
                                </div>
                                {{-- giá --}}
                                @if(!$product['trangthai'])
                                    <div class="w-55">
                                        <div data-id="{{$product['id']}}" class="out-of-stock">NGỪNG KINH DOANH</div>
                                    </div>
                                @else
                                    <div class="w-25 d-flex align-items-center">
                                        <div class="ml-10">
                                            <div class="fw-600">{{ number_format($product['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></div>
                                            {{-- hết hạn khuyến mãi --}}
                                            @if ($product['khuyenmai'] != 0)
                                                <div class="fz-14 text-strike gray-1">{{ number_format($product['gia'], 0, '', '.') }}<sup>đ</sup></div>    
                                            @endif
                                        </div>
                                    </div>
                                    @if($key['hethang'])
                                        <div class="w-30">
                                            <div data-id="{{$product['id']}}" class="out-of-stock">TẠM HẾT HÀNG</div>
                                        </div>
                                    @else
                                        {{-- số lượng --}}
                                        <div class="w-15 d-flex">
                                            @if (!$key['hethang'])
                                                <div class='cart-qty-input'>
                                                    <button type='button' data-id="{{$key['id']}}" data-component="cart" class='update-qty minus'><i class="fas fa-minus"></i></button>
                                                    <b data-id="{{$key['id']}}" class="qty-item">{{$key['sl']}}</b>
                                                    <button type='button' data-id="{{$key['id']}}" data-component="cart" class='update-qty plus'><i class="fas fa-plus"></i></button>
                                                </div>
                                            @endif
                                        </div>
                                        {{-- thành tiền --}}
                                        <div class="w-15">
                                            @if (!$key['hethang'])
                                                <div data-id="{{$key['id']}}" class="provisional_item red fw-600">{{ number_format($key['thanhtien'], 0, '', '.') }}<sup>đ</sup></div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                                {{-- xóa --}}
                                <div class="w-5">
                                    <div type="button"
                                        data-id="{{$key['id']}}"
                                        data-type="item" class="remove-cart-item fz-18"><i class="fal fa-trash-alt"></i></div>
                                </div>
                            </div>    
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-3 col-12">
                    {{-- mã khuyến mãi --}}
                    <div class="p-20 box-shadow mb-20">
                        <div class="fw-600 mb-20">Mã khuyến mãi</div>
                        
                        <div id="cart-voucher">
                            {{-- đã áp dụng mã khuyên mãi --}}
                            @if (session('voucher'))
                                <?php $voucher = session('voucher') ?>
                                {{-- mã --}}
                                @include("user.content.components.voucher.apply-small-voucher")
                            {{-- chưa áp dụng mã giảm giá --}}
                            @else
                                @include("user.content.components.voucher.choose-voucher-button")
                            @endif
                        </div>
                    </div>

                    {{-- tính tiền --}}
                    <div class="box-shadow mb-20">
                        <div class="p-20 border-bottom">
                            {{-- tạm tính --}}
                            <div id="provisional-text" class="d-flex justify-content-between">
                                <div class="gray-1">Tạm tính</div>
                                <div id="provisional" class="black"></div>
                            </div>
                            {{-- mã giảm giá --}}
                            @if (session('voucher'))
                                <div id="voucher-discount-text" class="d-flex justify-content-between mt-20">
                                    <div class="gray-1">Mã giảm giá</div>
                                    <div id="voucher" class="main-color-text">-{{session('voucher')->chietkhau*100}}%</div>
                                </div>
                            @endif
                        </div>
                        
                        {{-- tổng tiền --}}
                        <div class="p-20">
                            <div class="d-flex justify-content-between">
                                <div class="gray-1">Tổng tiền</div>
                                <span id="total" class="red fz-20 fw-600"></span>
                            </div>
                        </div>
                    </div>

                    {{-- thanh toán --}}
                    <div type="button" id="checkout-page" href="{{route('user/thanh-toan')}}" class="checkout-btn">Tiến hành đặt hàng</div>
                </div>
            @else
                <div class="col-lg-12">
                    <div class="box-shadow">
                        <div class="row">
                            <div class="col-lg-4 col-md-8 col-10 mx-auto">
                                <div class="pt-100 pb-100 text-center">
                                    <div class="fz-20 mb-40">Không có sản phẩm nào trong giỏ hàng.</div>
                                    <a href="{{route('user/dien-thoai')}}" class="main-btn">Tiếp tục mua hàng</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- modal hỏi bỏ qua sản phẩm đã hết hàng --}}
<div class="modal fade" id="skip-product-modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="btn-close" data-bs-dismiss="modal"></div>
                <div class="p-80">
                    <div class="mb-40 fz-18 text-center">Giỏ hàng của bạn có sản phẩm đã hết hàng. Bạn có muốn bỏ qua sản phẩm đó và thanh toán các sản phẩm còn lại không?</div>
                    <div class="d-flex justify-content-between">
                        <div class="cancel-btn w-49" data-bs-dismiss="modal">Đóng</div>
                        <div id="skip-product-btn" class="main-btn w-49">OK</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal chọn khuyến mãi --}}
@include("user.content.modal.voucher-modal")

{{-- modal xóa --}}
@include("user.content.modal.xoa-modal")
@stop