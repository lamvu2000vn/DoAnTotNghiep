<div class='row'>
    <div class='col-md-3'>
        @section("acc-favorite-active") account-sidebar-active @stop
        @include("user.content.taikhoan.sec-thanh-chuc-nang")
    </div>
    <div class='col-md-9'>
        {{-- favorite header --}}
        <div class="account-head-title">
            <div class="fw-600 fz-22">Danh sách yêu thích</div>
            {{-- nút 3 chấm --}}
            <div class='account-btn-option'>
                <i class="far fa-ellipsis-v fz-28"></i>
                <div class='account-option-div'>
                    <div id='fav-btn-delete-all' class='account-single-option'>Bỏ thích tất cả</div>
                </div>
            </div>
        </div>

        {{-- danh sách yêu thích --}}
        @if (count($favoriteList) != 0)
            <div id="lst_favorite">
                @foreach ($favoriteList as $key)
                    <div id="favorite-{{$key['id']}}" class="single-favorite box-shadow mb-30">
                        <div class="d-flex justify-content-between">
                            {{-- điện thoại --}}
                            <div class="favorite-product">
                                <div class="relative mr-10">
                                    @if (!$key['sanpham']['modelStatus'])
                                        <div class="stop-business-badge"></div>
                                    @elseif ($key['sanpham']['comingSoon'])
                                        <div class="coming-soon">HÀNG SẮP VỀ</div>
                                    @elseif (!$key['sanpham']['inStocks'])
                                        <div class="out-of-stock-badge"></div>
                                    @endif
                                    <img src="{{$url_phone.$key['sanpham']['hinhanh']}}" alt="" class="favorite-img">
                                </div>

                                <div class='d-flex flex-column justify-content-between'>
                                    <div class="d-flex flex-column">
                                        {{-- tên sp --}}
                                        <a href="{{route('user/chi-tiet', ['name' => $key['sanpham']['tensp_url']])}}?mausac={{$key['sanpham']['mausac_url']}}" class="favorite-product-name">
                                            {{$key['sanpham']['tensp'] . ' - ' . $key['sanpham']['mausac']}}
    
                                        </a>
                                        <div class='d-flex mb-5'>
                                            @if ($key['sanpham']['danhgia']['qty'] != 0)
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if($key['sanpham']['danhgia']['star'] >= $i)
                                                    <i class="fas fa-star checked"></i>
                                                    @else
                                                    <i class="fas fa-star uncheck"></i>
                                                    @endif
                                                @endfor
                                                <span class='fz-14 ml-10'>{{ $key['sanpham']['danhgia']['qty'] . ' đánh giá'}}</span>
                                            @else
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star uncheck"></i>
                                                @endfor
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class='favorite-discount-price'>{{number_format($key['sanpham']['giakhuyenmai'], 0, '', '.')}}<sup>đ</sup></div>
                                        @if ($key['sanpham']['khuyenmai'] != 0)
                                            <div class="favorite-old-price">
                                                <span class='text-strike mr-10'>
                                                    {{number_format($key['sanpham']['gia'], 0, '', '.')}}<sup>đ</sup>
                                                </span>
                                                <span class="red">-{{$key['sanpham']['khuyenmai']*100}}%</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- nút xóa --}}
                            <div class="d-flex">
                                <div type="button" data-id="{{$key['id']}}" class="fav-btn-delete d-flex align-items-center h-100 p-10"><i class="fal fa-trash-alt fz-24"></i></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-70 box-shadow d-flex justify-content-center flex-wrap">Bạn chưa có sản phẩm nào. <a href="dienthoai" class="ml-5">Xem sản phẩm</a></div>
        @endif
    </div>
</div>

<div id="toast"></div>