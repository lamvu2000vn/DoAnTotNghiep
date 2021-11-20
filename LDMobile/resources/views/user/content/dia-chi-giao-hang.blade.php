<!DOCTYPE html>
<html lang="en">
    @section("title")Địa chỉ giao hàng | LDMobile @stop
    @include("user.header.head")
<body class="address-delivery-bg">
    <div class="address-delivery-header">
        <div class="container">
            <img src="images/logo/LDMobile-logo.png" alt="logo" width="80px">
        </div>
    </div>

    {{-- session --}}
    @if (session('toast_message'))
        <div id="toast-message" data-message="{{session('toast_message')}}"></div>
    @endif
    @if (session('user'))
        <?php $user = session('user') ?>
        <input type="hidden" id="session-user" data-id="{{$user->id}}">
    @endif

    <div class="loader"><div class="loader-bg"></div><div class="loader-img"><img src="images/logo/LDMobile-logo.png" alt=""></div><div class="spinner-border" role="status"></div></div>

    <div class="container">
        <div class="pt-20 pb-20">
            <h3>Địa chỉ giao hàng</h3>
            <div class="fw-600 mb-20">Chọn địa chỉ giao hàng có sẵn bên dưới</div>

            <div class="row">
                <div class="col-lg-8 col-12">
                    <div class="row">
                        {{-- địa chỉ mặc định --}}
                        <?php $default = $addressList['default'] ?>
                        <div class="col-12">
                            <div id="address-{{$default['id']}}" data-default="true" class="white-bg p-20 border-success mb-30">
                                <div class="d-flex justify-content-between pb-10">
                                    <div class="d-flex">
                                        <b id="adr-fullname-{{$default['id']}}" class="text-uppercase">{{ $default['hoten'] }}</b>
                                        <div class="d-flex align-items-center success-color ml-15"><i class="far fa-check-circle mr-5"></i>Đang sử dụng</div>
                                    </div>
                                </div>
        
                                <div class="mb-5">
                                    <div class="adr-content">
                                        {{$default['diachi'].', '.$default['phuongxa'].', '.$default['quanhuyen'].', '.$default['tinhthanh']}}
                                    </div>
                                </div>
                                <div class="mb-20">
                                    <div id="adr-tel-{{$default['id']}}" class="adr-tel">{{$default['sdt']}}</div>
                                </div>

                                {{-- button --}}
                                <div class="d-flex">
                                    <form id="change-address-delivery-form" action="{{route('user/change-address-delivery')}}" method="POST">
                                        @csrf
                                        <input type="hidden" id="address_id" name="address_id">
                                    </form>
                                    <div data-id="{{$default['id']}}" class="choose-address-delivery main-btn p-10">Giao đến địa chỉ này</div>
                                    <div data-id="{{$default['id']}}" class="btn-edit-address cancel-btn p-10 ml-10">Sửa</div>
                                </div>
                            </div>
                        </div>
                        {{-- địa chỉ khác --}}
                        @foreach ($addressList['another'] as $key)
                            @if($key['macdinh'] == 0)
                                <div class="col-12">
                                    <div id="address-{{$key['id']}}" data-default="false" class="white-bg p-20 border mb-30">
                                        <div class="d-flex justify-content-between pb-10">
                                            <div class="d-flex">
                                                <b id="adr-fullname-{{$key['id']}}" class="text-uppercase">{{ $key['hoten'] }}</b>
                                            </div>
                                        </div>
                
                                        <div class="mb-5">
                                            <div class="adr-content">
                                                {{$key['diachi'].', '.$key['phuongxa'].', '.$key['quanhuyen'].', '.$key['tinhthanh']}}
                                            </div>
                                        </div>
                                        <div class="mb-20">
                                            <div id="adr-tel-{{$key['id']}}" class="adr-tel">{{$key['sdt']}}</div>
                                        </div>

                                        {{-- button --}}
                                        <div class="d-flex">
                                            <div data-id="{{$key['id']}}" class="choose-address-delivery main-btn p-10 mr-10">Giao đến địa chỉ này</div>
                                            <div data-id="{{$key['id']}}" data-diachi="{{$key['diachi']}}"
                                                data-phuongxa="{{$key['phuongxa']}}" data-quanhuyen="{{$key['quanhuyen']}}"
                                                data-tinhthanh="{{$key['tinhthanh']}}" class="btn-edit-address cancel-btn p-10 mr-10">Sửa</div>
                                            <div data-id="{{$key['id']}}" class="btn-delete-address checkout-btn p-10">Xóa</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    {{-- thời gian thanh toán --}}
                    <div class="countdown-checkout">
                        <div class="countdown-text">Thanh toán kết thúc sau</div>
                        <div class="countdown-number">
                            <div class="minute-number"></div>
                            <div class="second-number"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- thêm địa chỉ giao hàng mới --}}
            <div class="d-flex">
                Bạn muốn giao hàng đến địa chỉ khác?
                <div id='new-address-show' class="pointer-cs main-color-text ml-10">Thêm địa chỉ giao hàng mới</div>
            </div>
        </div>
    </div>

    <div id="toast"></div>

    @include("user.content.modal.dia-chi-modal")
    @include("user.content.modal.xoa-modal")

    @include("user.footer.footer-link")
</body>
</html>