<div class='row'>
    <div class='col-md-3'>
        @section("acc-order-active") account-sidebar-active @stop
        @include("user.content.taikhoan.sec-thanh-chuc-nang")
    </div>
    <div class='col-md-9'>
        @if ($processing || $complete)
            {{-- đơn hàng đang xử lý --}}
            @if ($processing)
                <table class='table box-shadow mb-50'>
                    <thead>
                        <tr>
                            <th><div class="pt-10 pb-10">Mã đơn hàng</div></th>
                            <th><div class="pt-10 pb-10">Thời gian</div></th>
                            <th><div class="pt-10 pb-10">Sản phẩm</div></th>
                            <th><div class="pt-10 pb-10">Tổng tiền</div></th>
                            <th><div class="pt-10 pb-10">Trạng thái đơn hàng</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($processing as $key)
                            <?php $order = $key['order']; $detail = $key['detail'] ?>
                            @include("user.content.components.donhang.don-hang")
                        @endforeach
                    </tbody>
                </table>
            @endif

            {{-- đơn hàng đã xử lý --}}
            @if ($complete)
                <table class='table box-shadow'>
                    <thead>
                        <tr>
                            <th><div class="pt-10 pb-10">Mã đơn hàng</div></th>
                            <th><div class="pt-10 pb-10">Thời gian</div></th>
                            <th><div class="pt-10 pb-10">Sản phẩm</div></th>
                            <th><div class="pt-10 pb-10">Tổng tiền</div></th>
                            <th><div class="pt-10 pb-10">Trạng thái đơn hàng</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($complete as $key)
                            <?php $order = $key['order']; $detail = $key['detail']; ?>
                            @include("user.content.components.donhang.don-hang")
                        @endforeach
                    </tbody>
                </table>
            @endif
        @else
            <div class="p-70 box-shadow text-center">
                Bạn chưa có đơn hàng nào. <a href="{{route('user/dien-thoai')}}" class="ml-5">Mua hàng</a>
            </div>
        @endif
    </div>
</div>

{{-- modal xem thêm đơn hàng --}}
<div class="modal fade" id="view-more-order" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div type='button' class="btn-close" data-bs-dismiss='modal'></div>
                <div class="d-flex flex-column">
                    <div class="fz-20 mb-5">Đơn hàng <b>123</b></div>
                    <div class="fz-14">Ngày mua: 12/12/2021</div>
                </div>
            </div>
            <div class="modal-body p-0">
                {{-- danh sách điện thoại --}}
                <div class="list-phone-order">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Điện thoại</th>
                                <th>Số lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 5; $i++)
                            <tr>
                                <td class="vertical-center text-center"><?php echo $i ?></td>
                                <td>{{-- điện thoại --}}
                                    <div class="p-10">
                                        <div class="d-flex">
                                            <img src="images/phone/iphone_12_black.jpg" alt="" width="100px">
                                            <div class="ml-10">
                                                <a href="#" class="fw-600 mb-5 black">iPhone 12 PRO MAX</a>
                                                <div class="fz-14">Dung lượng: 128GB</div>
                                                <div class="fz-14">Màu sắc: Đen</div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="vertical-center text-center fw-600">x1</td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>