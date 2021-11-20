@extends("user.layout")
@section("title")Thanh toán | LDMobile @stop
@section("content")
@section("breadcrumb")
    <a href="{{route('user/thanh-toan')}}" class="bc-item active">Thanh toán</a>
@stop
@include("user.content.section.sec-thanh-dieu-huong")

<section class='pt-50 pb-50'>
    <div class='container'>
        <div class='row'>
            <div class='col-lg-8 col-md-12'>
                <h3>Thông tin mua hàng</h3>
                <hr>
                <div class='pb-20'>
                    {{-- cách thức nhận hàng --}}
                    <div class='row'>
                        <div class='col-md-10 col-sm-12 mb-20'>
                            <label for="CachThucNhanHang" class='form-label fw-600'>Chọn cách thức nhận hàng</label>
                            <div id='CachThucNhanHang' class='d-flex'>
                                <div class='mr-20'>
                                    <input type="radio" name='receive-method' id="TaiNha" value='Giao hàng tận nơi' checked>
                                    <label for="TaiNha">Giao hàng tận nơi</label>
                                </div>
                                <div>
                                    <input type="radio" name='receive-method' id='TaiCuaHang' value='Nhận tại cửa hàng'>
                                    <label for="TaiCuaHang">Nhận tại cửa hàng</label>
                                </div>
                            </div>

                            {{-- giao hàng tận nơi --}}
                            <div data-flag="{{$defaultAdr != null ? "1" : "0"}}" class='atHome p-20 mt-10'>
                                <div id="delivery-address">
                                    @if ($defaultAdr != null)
                                        <div id="address-{{$defaultAdr->id}}" data-default="true" class="col-md-12 white-bg p-20 border">
                                            {{-- họ tên --}}
                                            <div class="d-flex justify-content-between pb-10">
                                                <div class="d-flex">
                                                    <b id="adr-fullname-{{$defaultAdr->id}}" class="text-uppercase">{{ $defaultAdr->hoten}}</b>
                                                </div>
                                                <div class="d-flex">
                                                    <div id='btn-change-address-delivery' class="pointer-cs main-color-text">Thay đổi</div>
                                                </div>
                                            </div>
                                
                                            {{-- địa chỉ --}}
                                            <div class="d-flex mb-5">
                                                <div class="gray-1">Địa chỉ:</div>
                                                <div class="ml-5 black">{{ $defaultAdr->diachi.', '.$defaultAdr->phuongxa.', '.$defaultAdr->quanhuyen.', '.$defaultAdr->tinhthanh }}</div>
                                            </div>
                                
                                            {{-- SĐT --}}
                                            <div class="d-flex">
                                                <div class="gray-1">Điện thoại:</div>
                                                <div id="adr-tel-{{$defaultAdr->id}}" class="ml-5 black">{{ $defaultAdr->sdt}}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center white-bg p-20 border">
                                            <div class="d-flex justify-content-center">
                                                Bạn chưa có địa chỉ giao hàng.
                                                <div id="new-address-show" data-default="true" type="button" class="main-color-text ml-5">Thêm địa chỉ</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- nhận tại cửa hàng --}}
                            <div class='atStore p-20 mt-10'>
                                <div class='row'>
                                    <div class='col-lg-6 col-12 mb-10'>
                                        <select id="at-store-select">
                                            @foreach ($lstArea as $area)
                                                <option value="{{$area->id}}">{{$area->tentt}}</option>
                                            @endforeach
                                        </select>
                                        <div class="list-branch mt-10">
                                            @foreach ($lstBranch as $branch)
                                                <div class="single-branch pointer-cs" data-area='{{$branch->id_tt}}'>
                                                    <input type="radio" name='branch' id='{{'branch-'.$branch->id}}' value='{{$branch->id}}'>
    
                                                    <label for="{{'branch-'.$branch->id}}" class="branch-text">
                                                        <div class="d-flex flex-column">
                                                            <div class="d-flex">
                                                                <i class="fas fa-store mr-10"></i>
                                                                {{$branch->diachi}}
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="info-qty-in-stock">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h3>Phương thức thanh toán</h3>
                <hr>
                <div class='pb-20'>
                    {{-- phương thức thanh toán --}}
                    <div class='mb-3 d-flex align-items-center'>
                        <input type="radio" name='payment-method' id="cash" checked value="cash">
                        <label for="cash">Thanh toán khi nhận hàng</label>
                    </div>
                    <div class='mb-3'>
                        <input type="radio" name='payment-method' id="zalopay" value='zalopay'>
                        <label for="zalopay">Thanh toán online</label>
                        <div class="ml-20 mt-5">
                            <img src="images/logo/zalopay-logo.png" alt="" class="ml-10 w-10">
                            <span class="fz-14 ml-10 mr-10">Cổng thanh toán ZaloPay</span>
                            <img src="images/icon/atm-card-icon.png" alt="" class='checkout-payment-icon'>
                            <img src="images/icon/visa-card-icon.png" alt="" class='checkout-payment-icon'>
                            <img src="images/icon/master-card-icon.png" alt="" class='checkout-payment-icon'>
                            <img src="images/icon/jcb-card-icon.png" alt="" class='checkout-payment-icon'>
                        </div>
                    </div>
                </div>
                <hr>
                <div class='col-md-8 mx-auto pt-20 pb-20'>
                    <form id='checkout-form' action={{route('user/checkout')}} method="POST">
                        @csrf
                        <input type="hidden" name="paymentMethod" id="paymentMethod">
                        <input type="hidden" name="receciveMethod" id="receciveMethod">
                        <input type="hidden" name="id_tk_dc" id="id_tk_dc" value="{{$defaultAdr != null ? $defaultAdr->id : null}}">
                        <input type="hidden" name="id_cn" id="id_cn">
                        <input type="hidden" name="cartTotal" id="cartTotal">
                        <input type="hidden" name="id_vc" id="id_vc" value="{{session('voucher') ? session('voucher')->id : null}}">
                        <input type="hidden" name="id_sp_list" id="id_sp_list">

                        <div id='btn-confirm-checkout' type="button" class="checkout-btn w-100">ĐẶT HÀNG</div>
                        <div class="text-center pt-5">(Vui lòng kiểm tra lại đơn hàng trước khi đặt mua)</div>
                    </form>
                </div>
            </div>

            <div class='col-lg-4 col-md-10'>
                {{-- thời gian thanh toán --}}
                <div class="countdown-checkout">
                    <div class="countdown-text">Thanh toán kết thúc sau</div>
                    <div class="countdown-number">
                        <div class="minute-number"></div>
                        <div class="second-number"></div>
                    </div>
                </div>
                {{-- giỏ hàng --}}
                <div class='pt-20 pb-20'>
                    <div class='checkout-cart-box box-shadow'>
                        <div class='d-flex justify-content-between white-bg p-10'>
                            <div class="fw-600 fz-20">Giỏ hàng</div>
                        <div href="#collapse-cart" class='checkout-btn-collapse-cart' data-bs-toggle="collapse"
                                role="button" aria-expanded="true"
                                aria-controls="collapseExample"><i class="far fa-chevron-up"></i></div>
                        </div>
                        <div class="collapse show" id="collapse-cart">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- modal chọn khuyến mãi --}}
@include("user.content.modal.voucher-modal")

{{-- modal thêm/sửa địa chỉ --}}
@include("user.content.modal.dia-chi-modal")

{{-- modal xóa --}}
@include("user.content.modal.xoa-modal")
@stop
