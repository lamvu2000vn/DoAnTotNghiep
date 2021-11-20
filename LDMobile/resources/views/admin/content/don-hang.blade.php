@extends("admin.layout")
@section("sidebar-order") sidebar-link-selected @stop
@section("content-title") Đơn hàng @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-end align-items-center mb-20">
        {{-- search --}}
        <div class='relative mr-10'>
            <div class="head-input-grp">
                <input type="text" id="search" placeholder="Tìm kiếm">
                <span class='input-icon-right'><i class="fal fa-search"></i></span>
            </div>
        </div>
        {{-- filter --}}
        <div class="relative">
            <div id="filter-donhang" class="filter-sort-btn"><i class="far fa-filter mr-5"></i>Bộ lọc</div>
            <div class="filter-badge"></div>
            <div class="filter-div" style="width: 750px">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="mb-10 fw-600">Phương thức thanh toán</div>
                        <div class="mb-5">
                            <input type="checkbox" name="filter" data-object="paymentMethod" id="cash" value="Thanh toán khi nhận hàng">
                            <label for="cash">Thanh toán khi nhận hàng</label>
                        </div>
                        <div class="mb-5">
                            <input type="checkbox" name="filter" data-object="paymentMethod" id="zalo-pay" value="Thanh toán ZaloPay">
                            <label for="zalo-pay">Thanh toán ZaloPay</label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-10 fw-600">Hình thức nhận hàng</div>
                        <div class="mb-5">
                            <input type="checkbox" name="filter" data-object="receiveMethod" id="at-home" value="Giao hàng tận nơi">
                            <label for="at-home">Giao hàng tận nơi</label>
                        </div>
                        <div class="mb-5">
                            <input type="checkbox" name="filter" data-object="receiveMethod" id="at-store" value="Nhận tại cửa hàng">
                            <label for="at-store">Nhận tại cửa hàng</label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-10 fw-600">Trạng thái đơn hàng</div>
                        <div class="mb-5">
                            <input type="checkbox" name="filter" data-object="status" id="received" value="Đã tiếp nhận">
                            <label for="received">Đã tiếp nhận</label>
                        </div>
                        <div class="mb-5">
                            <input type="checkbox" name="filter" data-object="status" id="comfirmed" value="Đã xác nhận">
                            <label for="comfirmed">Đã xác nhận</label>
                        </div>
                        <div class="mb-5">
                            <input type="checkbox" name="filter" data-object="status" id="delivery" value="Đang giao hàng">
                            <label for="delivery">Đang giao hàng</label>
                        </div>
                        <div class="mb-5">
                            <input type="checkbox" name="filter" data-object="status" id="success" value="Thành công">
                            <label for="success">Thành công</label>
                        </div>
                        <div class="mb-5">
                            <input type="checkbox" name="filter" data-object="status" id="cancelled" value="Đã hủy">
                            <label for="cancelled">Đã hủy</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- sort --}}
        <div class="relative">
            <div id="sort-donhang" class="filter-sort-btn ml-10"><i class="far fa-sort mr-5"></i>Sắp xếp</div>
            <div class="sort-badge"></div>
            <div class="sort-div">
                <div class="mb-5">
                    <input type="radio" name="sort" id="date-desc" value="date-desc" checked>
                    <label for="date-desc">Thời gian mới nhất</label>
                </div>
                <div class="mb-5">
                    <input type="radio" name="sort" id="date-asc" value="date-asc">
                    <label for="date-asc">Thời gian cũ nhất</label>
                </div>        
                <div class="mb-5">
                    <input type="radio" name="sort" id="total-asc" value="total-asc">
                    <label for="total-asc">Tổng tiền tăng dần</label>
                </div>
                <div class="mb-5">
                    <input type="radio" name="sort" id="total-desc" value="total-desc">
                    <label for="total-desc">Tổng tiền giảm dần</label>
                </div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Thời gian</th>
                <th>Tài khoản</th>
                <th>Phương thức thanh toán</th>
                <th>Hình thức nhận hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái đơn hàng</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_data">
            @foreach ($lst_order as $key)
                <tr data-id="{{$key->id}}">
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->id}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->thoigian}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->taikhoan->hoten}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->pttt}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->hinhthuc}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{number_format($key->tongtien, 0, '', '.')}}<sup>đ</sup></div>
                    </td>
                    <td class="vertical-center">
                        <div data-id="{{$key->id}}" class="trangthaidonhang pt-10 pb-10">{{$key->trangthaidonhang}}</div>
                    </td>
                    {{-- nút --}}
                    <td class="vertical-center w-5">
                        <div class="d-flex justify-content-start">
                            @if ($key->trangthaidonhang != 'Thành công' && $key->trangthaidonhang != 'Đã hủy')
                                @if ($key->trangthaidonhang == 'Đã tiếp nhận')
                                    <div data-id="{{$key->id}}" class="confirm-btn"><i class="fas fa-file-check"></i></div>    
                                @elseif ($key->trangthaidonhang == 'Đã xác nhận')
                                    <div data-id="{{$key->id}}" class="success-btn"><i class="fas fa-box-check"></i></div>
                                @endif
                            @endif
                            <div data-id="{{$key->id}}" class="info-btn"><i class="fas fa-info"></i></div>
                            @if ($key->trangthaidonhang != 'Đã hủy' && $key->trangthaidonhang != 'Thành công')
                                <div data-id="{{$key->id}}" class="delete-btn"><i class="fas fa-trash"></i></div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div id="loadmore" class="text-center"><div class="spinner-border loadmore" role="status"></div></div>
</div>

{{-- modal thêm|sửa --}}
<div class="modal fade" id="modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="modal-title" class="fw-600 fz-22"></div>
            </div>
            <div id="order-modal" class="modal-body p-40">
                <div class="d-flex justify-content-end mt-50">
                    <div class="checkout-btn" data-bs-dismiss="modal">Đóng</div>
                </div>
            </div>
        </div>
    </div>
</div>

@include("user.content.modal.xoa-modal")

<div id="toast"></div>

@stop