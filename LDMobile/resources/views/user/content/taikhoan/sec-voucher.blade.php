<div class='row'>
    <div class='col-md-3'>
        @section("acc-voucher-active") account-sidebar-active @stop
        @include("user.content.taikhoan.sec-thanh-chuc-nang")
    </div>
    <div class='col-md-9'>
        @if (count($voucherList) != 0)
            <div class="account-head-title">
                <div class="fw-600 fz-22">Mã giảm giá của tôi</div>
            </div>
            <div class="row">
                @foreach ($voucherList as $key)
                    <?php
                        $sl = $key->sl;
                        $voucher = $key->voucher;
                        $id = $key->id 
                    ?>

                    {{-- mã giảm giá còn hsd --}}
                    @if($key->trangthai)
                        {{-- mã giảm giá --}}
                        <div class='col-lg-6 col-12 mb-20'>
                            @include("user.content.components.voucher.normal-big-voucher")
                        </div>
                    {{-- mã giảm giá hết hsd --}}
                    @else
                        {{-- mã giảm giá --}}
                        <div data-id="{{$key->id}}" class='expired-voucher col-lg-6 col-12 mb-20'>
                            @include("user.content.components.voucher.expired-voucher")
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="p-70 text-center box-shadow">Bạn chưa có voucher nào. <a href="{{route('user/dien-thoai')}}" class="ml-5">Mua hàng</a></div>
        @endif
    </div>
</div>