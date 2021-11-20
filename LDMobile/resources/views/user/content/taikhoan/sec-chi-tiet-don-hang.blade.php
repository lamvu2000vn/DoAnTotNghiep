<div class='row'>
    <div class='col-md-3'>
        @section("acc-order-active") account-sidebar-active @stop
        @include("user.content.taikhoan.sec-thanh-chuc-nang")
    </div>
    <div class='col-md-9'>
        <div class="mb-5">
            <div class="account-head-title fz-22">
                <div class="fw-600">Chi tiết đơn hàng #{{$order['order']->id}}</div>
    
                @if ($order['order']->trangthaidonhang != 'Đã hủy')
                    <div class='account-deliver-success'>{{$order['order']->trangthaidonhang}}</div>
                @else
                    <div class='account-deliver-fail'>Đã hủy</div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-end mb-30">
            <span>Ngày mua: <b>{{$order['order']->thoigian}}</b></span>
        </div>

        {{-- hủy đơn hàng --}}
        @if ($order['order']->trangthaidonhang === 'Đã tiếp nhận')
            <div class="d-flex justify-content-center mb-50">
                <div id="cancel-order-btn" data-id="{{$order['order']->id}}" class="checkout-btn w-50">hủy đơn hàng</div>
            </div>
        @endif

        <div class='mb-20'>
            <div class='row'>
                @if ($order['order']->hinhthuc === 'Giao hàng tận nơi')
                    <div class='col-lg-6 mb-20'>
                        <div class='fw-600 pb-10'>Địa chỉ người nhận</div>
                        <div id="HTNH-div">
                            <div class='box-shadow p-20'>
                                <div class="d-flex flex-column fz-14">
                                    <b class='text-uppercase pb-5'>{{$order['order']->diachigiaohang->hoten}}</b>
                                    <div class="mb-5">
                                        <div class="adr-content">
                                            {{$order['order']->diachigiaohang->diachi.', '.$order['order']->diachigiaohang->phuongxa.', '.$order['order']->diachigiaohang->quanhuyen.', '.$order['order']->diachigiaohang->tinhthanh}}
                                        </div>
                                    </div>
                                    <div class="adr-tel">
                                        {{$order['order']->diachigiaohang->sdt}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class='col-lg-6 mb-20'>
                        <div class='fw-600 pb-10'>Nhận tại cửa hàng</div>
                        <div id="HTNH-div">
                            <div class='box-shadow p-20'>
                                <div class="d-flex flex-column fz-14">
                                    <div class="mb-5">
                                        <div class="adr-content">
                                            {{$order['order']->chinhanh->diachi}}
                                        </div>
                                    </div>
                                    <div class="adr-tel">
                                        {{$order['order']->chinhanh->sdt}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- hình thức thanh toán --}}
                <div class='col-lg-6'>
                    <div class='fw-600 pb-10'>Phương thức thanh toán</div>
                    <div id="PTTT-div">
                        <div class='box-shadow p-20 h-100'>
                            <div class="black">{{$order['order']->pttt}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- danh sách sản phẩm --}}
        <div class='box-shadow'>
            <table class='table'>
                <thead>
                    <tr>
                        <th><div class='pt-10 pb-10'>Sản phẩm</div></th>
                        <th><div class='pt-10 pb-10'>Giá</div></th>
                        <th><div class='pt-10 pb-10'>Số lượng</div></th>
                        <th><div class='pt-10 pb-10'>Giảm giá</div></th>
                        <th><div class='pt-10 pb-10'>Tạm tính</div></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $provisional = 0 ?>
                    @foreach ($order['detail'] as $key)
                        <tr>
                            {{-- sản phẩm --}}
                            <td class='w-40 vertical-center'>
                                <div class='d-flex flex-row pt-10 pb-10'>
                                    <img src="{{$url_phone.$key['sanpham']['hinhanh']}}" alt="" width="80px" class="mr-5">
                                    <div class='d-flex flex-column'>
                                        <a href='{{route('user/chi-tiet', ['name' => $key['sanpham']['tensp_url']])}}?mausac={{$key['sanpham']['mausac_url']}}' class="cart-phone-name">
                                            {{$key['sanpham']['tensp']}}
                                        </a>
                                        <span>Màu sắc: {{$key['sanpham']['mausac']}}</span>
                                    </div>
                                </div>
                            </td>
                            {{-- giá --}}
                            <td class="vertical-center">{{number_format($key['sanpham']['gia'], 0, '', '.')}}<sup>đ</sup></td>
                            {{-- số lượng --}}
                            <td class="vertical-center">{{$key['sl']}}</td>
                            {{-- giảm giá --}}
                            <td class="vertical-center">
                                {{$key['sanpham']['khuyenmai'] != 0 ? '-'.$key['sanpham']['khuyenmai']*100 .'%' : '0'}}
                            </td>
                            {{-- tạm tính --}}
                            <td class="vertical-center">{{number_format($key['sanpham']['giakhuyenmai']*$key['sl'], 0, '', '.')}}<sup>đ</sup></td>
                            <?php $provisional += $key['sanpham']['giakhuyenmai']*$key['sl'] ?>
                        </tr>
                    @endforeach
                    {{-- mã giảm giá --}}
                    @if ($order['order']->id_vc)
                        <?php $voucher = $order['order']->voucher ?>
                        <tr>
                            <td colspan="5" class="p-0">
                                <div class='d-flex'>
                                    <div class="w-20 bg-gray-4 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-ticket-alt mr-10"></i>Mã giảm giá
                                    </div>
                                    
                                    <div class="w-30 p-10">
                                        @include("user.content.components.voucher.normal-small-voucher")
                                    </div>
                                </div>
                            </td>
                        </tr>    
                    @endif
                    
                    {{-- tính tiền --}}
                    <tr>
                        <td class="vertical-center">
                            <div class='p-20'>
                                <div class='d-flex justify-content-between pb-10'>
                                    <div class="gray-1">Tạm tính:</div>
                                    <div>{{number_format($provisional, 0, '', '.')}}<sup>đ</sup></div>
                                </div>
                                @if ($order['order']->id_vc)
                                    <div class='d-flex justify-content-between pb-10'>
                                        <div class="gray-1">Mã giảm giá:</div>
                                        <div class="main-color-text">-{{$order['order']->voucher->chietkhau*100}}%</div>
                                    </div>    
                                @endif
                                
                                <div class='d-flex justify-content-between'>
                                    <div class="gray-1">Tổng tiền:</div>
                                    <div class="red fz-20 fw-600">{{number_format($order['order']->tongtien, 0, '', '.')}}<sup>đ</sup></div>
                                </div>
                            </div>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                    {{-- quay về --}}
                    <tr>
                        <td colspan="5">
                            <div class='p-10'>
                                <a href="{{route('user/tai-khoan-don-hang')}}"><i class="fas fa-chevron-left mr-10"></i>Quay về</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@include("user.content.modal.xoa-modal")