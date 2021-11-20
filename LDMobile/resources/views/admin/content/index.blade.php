@extends("admin.layout")
@section("sidebar-dashboard") sidebar-link-selected @stop
@section("content-title") Bảng điều khiển @stop
@section("content")

{{-- tiêu đề thống kê tháng --}}
<div class="card-element pt-10 pb-10 mb-20">
    <div class="statistics-title fz-20">Thống kê tháng {{$currentMonth}}</div>
</div>

{{-- thống kê nhanh --}}
<div class="row mb-30">
    <div class="col-lg-4 mb-20">
        <div class="quick-stats-card red-bg">
            <div>
                <div class="fz-26 fw-600">{{$totalBillInMonth}}</div>
                <div>Tổng số đơn hàng</div>
            </div>
            <i class="fas fa-shopping-cart fz-40"></i>
        </div>
    </div>
    <div class="col-lg-4 mb-20">
        <div class="quick-stats-card success-bg">
            <div>
                <div class="fz-26 fw-600">{{ number_format($totalMoneyInMonth, 0, '', '.') }}<sup>đ</sup></div>
                <div>Doanh thu</div>
            </div>
            <i class="fas fa-hand-holding-usd fz-40"></i>
        </div>
    </div>
    <div class="col-lg-4 mb-20">
        <div class="quick-stats-card info-bg">
            <div>
                <div class="fz-26 fw-600">{{$totalAccountInMonth}}</div>
                <div>Lượt đăng ký thành viên</div>
            </div>
            <i class="fas fa-user fz-40"></i>
        </div>
    </div>
</div>

{{-- best sellers & số lượt đánh giá & truy cập --}}
<div class="row mb-30">
    {{-- BXH 5 sp bán chạy --}}
    <div class="col-lg-8 col-sm-12 mb-20">
        <div class="card-element">
            {{-- title --}}
            <div class="pt-20 pb-20">
                <div class="statistics-title fz-20">Best sellers</div>
            </div>
            <hr class="m-0">
            {{-- list best sellers --}}
            <?php $i = 1; $size = count($bestSellers) ?>
            @if( $size == 0)
            <div class="pt-50 fz-20 text-center" style="padding-bottom: 20%">Không có dữ liệu</div>
            @else
            @foreach ($bestSellers as $product)
                <div class="best-sellers">
                    <div class="d-flex align-items-center">
                        @if ($i == 1)
                            <div class="rank-number red">{{$i}}</div>
                        @elseif($i == 2)
                            <div class="rank-number yellow">{{$i}}</div>
                        @elseif($i == 3)
                            <div class="rank-number success-color">{{$i}}</div>
                        @else
                            <div class="rank-number gray-1">
                                {{$i}}
                            </div>
                        @endif
                        <div class="d-flex ml-40">
                            <img src="{{$url_phone.$product->hinhanh}}" alt="best seller product" width="70px">
                            <div class="ml-10">
                                <div class="d-flex align-items-center fw-600">
                                    {{$product->tensp.' '.$product->mausac}}
                                </div>
                                <div class="fz-14">Ram: {{$product->ram}}</div>
                                <div class="fz-14">Dung lượng: {{$product->dungluong}}</div>
                            </div>
                        </div>
                    </div>
                    <div style="color: green">Đã bán: {{$product->total}} chiếc<i class="fas fa-trophy-alt ml-10 yellow"></i></div>
                </div>
            <?php $i++; ?>
            @endforeach
            @endif
        </div>
    </div>
    {{-- số lượt đánh giá & số lượt truy cập web & app --}}
    <div class="col-lg-4 col-sm-12 mb-20">
        {{-- số lượt đánh giá --}}
        <div class="card-element d-flex justify-content-between mb-30">
            <div class="p-20">
                <div class="fz-22 fw-600 black">{{$totalReviewInMonth}}</div>
                <div class="text-color">Lượt đánh giá trong tháng</div>
            </div>
            <div class="icon-right-stats yellow-bg">
                <i class="fas fa-star fz-40 white"></i>
            </div>
        </div>
        {{-- số lượt truy cập web --}}
        <div class="card-element d-flex justify-content-between mb-30">
            <div class="p-20">
                <div class="fz-22 fw-600 black">{{$accessTimesOnWeb}}</div>
                <div class="text-color">Lượt truy cập trên Web trong tháng</div>
            </div>
            <div class="icon-right-stats blue-bg">
                <i class="fas fa-globe fz-40 white"></i>
            </div>
        </div>
        {{-- số lượt truy cập app --}}
        <div class="card-element d-flex justify-content-between mb-30">
            <div class="p-20">
                <div class="fz-22 fw-600 black">{{$accessTimesOnApp}}</div>
                <div class="text-color">Lượt truy cập trên App trong tháng</div>
            </div>
            <div class="icon-right-stats success-bg">
                <i class="fab fa-android fz-40 white"></i>
            </div>
            
        </div>
    </div>
</div>

{{-- trạng thái đơn hàng --}}
<div class="row mb-50">
    <div class="col-lg-12">
        <div class="card-element">
            <div class="row">
                <input type="hidden" id="total-order" value="{{$lst_orderStatus['total']}}">
                <div class="col-lg-3">
                    <div class="p-20">
                        <div>Đơn hàng tiếp nhận</div>
                        <div class="fz-26 fw-600 mb-10">{{$lst_orderStatus['received']}}</div>
                        <div id="received-order" data-qty="{{$lst_orderStatus['received']}}" class="order-progress-bar">
                            <div class="received-progress-bar"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="p-20">
                        <div>Đơn hàng xác nhận</div>
                        <div class="fz-26 fw-600 mb-10">{{$lst_orderStatus['confirmed']}}</div>
                        <div id="confirmed-order" data-qty="{{$lst_orderStatus['confirmed']}}" class="order-progress-bar">
                            <div class="confirmed-progress-bar"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="p-20">
                        <div>Đơn hàng thành công</div>
                        <div class="fz-26 fw-600 mb-10">{{$lst_orderStatus['success']}}</div>
                        <div id="successfull-order" data-qty="{{$lst_orderStatus['success']}}" class="order-progress-bar">
                            <div class="success-progress-bar"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="p-20">
                        <div>Đơn hàng đã hủy</div>
                        <div class="fz-26 fw-600 mb-10">{{$lst_orderStatus['cancelled']}}</div>
                        <div id="cancelled-order" data-qty="{{$lst_orderStatus['cancelled']}}" class="order-progress-bar">
                            <div class="cancelled-progress-bar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- biểu đồ doanh thu & hãng --}}
<?php $currentYear = date('Y') ?>
<div class="row mb-50">
    <div class="col-lg-6 mb-20">
        <div class="card-element">
            <div class="pt-20 pb-20">
                <div class="statistics-title d-flex align-items-center fz-20">
                    Biểu đồ thống kê doanh thu năm
                    <select id="sales-year" class="ml-10" style="width: auto">
                        @for ($i = 2018; $i < 2022; $i++)
                            @if ($i == $currentYear)
                                <option value="{{$i}}" selected>{{$i}}</option>
                            @else
                                <option value="{{$i}}">{{$i}}</option>
                            @endif
                        @endfor
                    </select>
                </div>
            </div>
            <hr class="m-0">
            <div class="d-flex align-items-center justify-content-center p-10" style="height: 385px">
                <input type="hidden" id="sales-data" value="{{$salesOfYear}}">
                <canvas id="sales-chart"></canvas>
            </div>            
        </div>
    </div>
    <div class="col-lg-6 mb-20">
        <div class="card-element">
            <div class="pt-20 pb-20">
                <div class="statistics-title d-flex align-items-center fz-20">
                    Biểu đồ thống kê hãng bán chạy năm
                    <select id="branch-year" class="ml-10" style="width: auto">
                        @for ($i = 2018; $i < 2022; $i++)
                            @if ($i == $currentYear)
                                <option value="{{$i}}" selected>{{$i}}</option>
                            @else
                                <option value="{{$i}}">{{$i}}</option>
                            @endif
                        @endfor
                    </select>
                </div>
            </div>
            <hr class="m-0">
            <div class="d-flex justify-content-center align-items-center p-20" style="height: 385px">
                <input type="hidden" id="donut-data" value="{{json_encode($suppplierOfYear)}}">
                @if(count($suppplierOfYear)==0) <div class="pt-50 fz-20 text-center" id="no-data" style="padding-bottom: 35%">Không có dữ liệu</div>@endif
                <div id="branch-chart"></div>
            </div>
        </div>
    </div>
</div>

@stop