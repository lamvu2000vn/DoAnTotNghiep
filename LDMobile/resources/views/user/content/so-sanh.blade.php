@extends("user.layout")
@section("title")So sánh | LDMobile @stop
@section("content")

<div class='container'>
    <div class="row">
        <div id="compare-title" class='compare-title'>So sánh điện thoại</div>
        
        <section class='pb-100'>
            <table class="table">
                <tbody>
                    <tr>
                        <td id="blank-td" class='w-25'></td>
                        {{-- sản phẩm hiện tại --}}
                        <td id="currentProduct" class='w-25'>
                            <?php $product = $currentProduct['sanpham'] ?>
                            <div class='d-flex flex-column pb-20'>
                                {{-- tên --}}
                                <div class="compare-product-name">
                                    <a href="{{route('user/chi-tiet', ['name' => $product['tensp_url']])}}">{{ $product['tensp']}}</a>
                                </div>
                                {{-- hình --}}
                                <div class="relative">
                                    {{-- ngừng kinh doanh --}}
                                    @if (!$product['modelStatus'])
                                        <div class="stop-business-badge"></div>
                                    {{-- hàng sắp về --}}
                                    @elseif ($product['comingSoon'])
                                        <div class="coming-soon">HÀNG SẮP VỀ</div>
                                    {{-- tạm hết hàng --}}
                                    @elseif (!$product['qtyInStock'])
                                        <div class="out-of-stock-badge"></div>
                                    @endif
                                    <img src="{{ $url_phone.$product['hinhanh'] }}" alt="" class='w-80 center-img pt-20 pb-10'>
                                </div>
                                {{-- giá & đánh giá --}}
                                <div class='pt-10 pb-10'>
                                    <b class="red">{{ number_format($product['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></b>
                                    @if ($product['khuyenmai'])
                                        <span class='text-strike ml-10'>{{ number_format($product['gia'], 0, '', '.')}}<sup>đ</sup></span>
                                        <span class="red ml-10">-{{$product['khuyenmai']*100}}%</span>
                                    @endif
                                </div>
                                <div>
                                    @if ($product['danhgia']['qty'] != 0)
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if($product['danhgia']['star'] >= $i)
                                            <i class="fas fa-star checked"></i>
                                            @else
                                            <i class="fas fa-star uncheck"></i>
                                            @endif
                                        @endfor
                                        <span class='ml-10'>{{ $product['danhgia']['qty'] }} đánh giá</span>
                                    @else
                                        <i class="fas fa-star uncheck"></i>
                                        <i class="fas fa-star uncheck"></i>
                                        <i class="fas fa-star uncheck"></i>
                                        <i class="fas fa-star uncheck"></i>
                                        <i class="fas fa-star uncheck"></i>
                                    @endif
                                </div>
                                <hr>
                                {{-- màu sắc --}}
                                <div class="d-flex flex-wrap">
                                    @foreach($currentProduct['variation']['color'] as $key)
                                    <div class="w-20 mb-5">
                                        <img src="{{ $url_phone.$key['hinhanh'] }}" alt="" >
                                        <div class='text-center mt-5 fz-14'>{{ $key['mausac'] }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        {{-- sản phẩm so sánh --}}
                        <td id="compareProduct" class='w-25'>
                            <?php $product = $compareProduct['sanpham'] ?>
                            <div class='d-flex flex-column pb-20'>
                                {{-- tên --}}
                                <div class="compare-product-name">
                                    <a href="{{route('user/chi-tiet', ['name' => $product['tensp_url']])}}">{{ $product['tensp']}}</a>
                                    <div type='button' data-order="2" class="delete-compare-btn"><i class="fal fa-times-circle"></i></div>
                                </div>
                                {{-- hình --}}
                                <div class="relative">
                                    {{-- ngừng kinh doanh --}}
                                    @if (!$product['modelStatus'])
                                        <div class="stop-business-badge"></div>
                                    {{-- hàng sắp về --}}
                                    @elseif ($product['comingSoon'])
                                        <div class="coming-soon">HÀNG SẮP VỀ</div>
                                    {{-- tạm hết hàng --}}
                                    @elseif (!$product['qtyInStock'])
                                        <div class="out-of-stock-badge"></div>
                                    @endif
                                    <img src="{{ $url_phone.$product['hinhanh'] }}" alt="" class='w-80 center-img pt-20 pb-10'>
                                </div>
                                {{-- giá & đánh giá --}}
                                <div class='pt-10 pb-10'>
                                    <b class="red">{{ number_format($product['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></b>
                                    @if ($product['khuyenmai'])
                                        <span class='text-strike ml-10'>{{ number_format($product['gia'], 0, '', '.') }}<sup>đ</sup></span>
                                        <span class="red ml-10">-{{$product['khuyenmai']*100}}%</span>
                                    @endif
                                </div>
                                {{-- đánh giá --}}
                                <div>
                                    @if ($product['danhgia']['qty'] != 0)
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if($product['danhgia']['star'] >= $i)
                                            <i class="fas fa-star checked"></i>
                                            @else
                                            <i class="fas fa-star uncheck"></i>
                                            @endif
                                        @endfor
                                        <span class='ml-10'>{{ $product['danhgia']['qty'] }} đánh giá</span>
                                    @else
                                        <i class="fas fa-star uncheck"></i>
                                        <i class="fas fa-star uncheck"></i>
                                        <i class="fas fa-star uncheck"></i>
                                        <i class="fas fa-star uncheck"></i>
                                        <i class="fas fa-star uncheck"></i>
                                    @endif
                                </div>
                                <hr>
                                <div class="d-flex flex-wrap">
                                    @foreach($compareProduct['variation']['color'] as $key)
                                    <div class="w-20">
                                        <img src="{{ $url_phone.$key['hinhanh'] }}" alt="" >
                                        <div class='text-center mt-5 fz-14'>{{ $key['mausac']}}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        {{-- thêm điên thoại để so sánh --}}
                        <td class='thirdProduct w-25'>
                            @if (empty($thirdProduct))
                                <button type='button' data-order="3" class="compare-btn-add-phone mt-120">
                                    <img src="images/add-phone.png" alt="" class='w-30 center-img'>
                                    <div class='pt-20 fw-600'>Thêm điện thoại để so sánh</div>
                                </button>
                            @else
                                <?php $product = $thirdProduct['sanpham'] ?>
                                
                                <div class='d-flex flex-column pb-20'>
                                    {{-- tên --}}
                                    <div class="compare-product-name">
                                        <a href="{{route('user/chi-tiet', ['name' => $product['tensp_url']])}}">{{ $product['tensp']}}</a>
                                        <div type='button' data-order="3" class="delete-compare-btn"><i class="fal fa-times-circle"></i></div>
                                    </div>
                                    {{-- hình --}}
                                    <div class="relative">
                                        {{-- ngừng kinh doanh --}}
                                        @if (!$product['modelStatus'])
                                            <div class="stop-business-badge"></div>
                                        {{-- hàng sắp về --}}
                                        @elseif ($product['comingSoon'])
                                            <div class="coming-soon">HÀNG SẮP VỀ</div>
                                        {{-- tạm hết hàng --}}
                                        @elseif (!$product['qtyInStock'])
                                            <div class="out-of-stock-badge"></div>
                                        @endif
                                        <img src="{{ $url_phone.$product['hinhanh'] }}" alt="" class='w-80 center-img pt-20 pb-10'>
                                    </div>
                                    {{-- giá & đánh giá --}}
                                    <div class='pt-10 pb-10'>
                                        <b class="red">{{ number_format($product['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></b>
                                        @if ($product['khuyenmai'])
                                            <span class='text-strike ml-10'>{{ number_format($product['gia'], 0, '', '.') }}<sup>đ</sup></span>
                                            <span class="red ml-10">-{{$product['khuyenmai']*100}}%</span>
                                        @endif
                                    </div>
                                    {{-- đánh giá --}}
                                    <div>
                                        @if ($product['danhgia']['qty'] != 0)
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if($product['danhgia']['star'] >= $i)
                                                <i class="fas fa-star checked"></i>
                                                @else
                                                <i class="fas fa-star uncheck"></i>
                                                @endif
                                            @endfor
                                            <span class='ml-10'>{{ $product['danhgia']['qty'] }} đánh giá</span>
                                        @else
                                            <i class="fas fa-star uncheck"></i>
                                            <i class="fas fa-star uncheck"></i>
                                            <i class="fas fa-star uncheck"></i>
                                            <i class="fas fa-star uncheck"></i>
                                            <i class="fas fa-star uncheck"></i>
                                        @endif
                                    </div>
                                    <hr>
                                    <div class="d-flex flex-wrap">
                                        @foreach($thirdProduct['variation']['color'] as $key)
                                        <div class="w-20">
                                            <img src="{{ $url_phone.$key['hinhanh'] }}" alt="" >
                                            <div class='text-center mt-5 fz-14'>{{ $key['mausac']}}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                    {{-- so sánh thông số kỹ thuật --}}
                    <?php
                        $current = $currentProduct['cauhinh']['thong_so_ky_thuat'];
                        $compare = $compareProduct['cauhinh']['thong_so_ky_thuat'];
                        $third = empty($thirdProduct) ? [] : $thirdProduct['cauhinh']['thong_so_ky_thuat'];
                        $updating = 'Đang cập nhật';
                    ?>
                    <tr>
                        <td colspan="4" class='border p-0'>
                            <div class='detail-specifications-title-2'>cấu hính sản phẩm</div>
                        </td>
                    </tr>
                    <tr>
                        <td class='border fw-600'>Màn hình</td>
                        <td class='border'>
                            {{
                                ($current['man_hinh']['cong_nghe_mh'] ? $current['man_hinh']['cong_nghe_mh'] : $updating)
                                . ', ' .
                                ($current['man_hinh']['ty_le_mh'] ? $current['man_hinh']['ty_le_mh'] . '"' : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['man_hinh']['cong_nghe_mh'] ? $compare['man_hinh']['cong_nghe_mh'] : $updating)
                                . ', ' .
                                ($compare['man_hinh']['ty_le_mh'] ? $compare['man_hinh']['ty_le_mh'] .'"' : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? 
                                    ($third['man_hinh']['cong_nghe_mh'] ? $third['man_hinh']['cong_nghe_mh'] : $updating)
                                    . ', ' .
                                    ($third['man_hinh']['ty_le_mh'] ? $third['man_hinh']['ty_le_mh'] .'"' : $updating)
                                : ''
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td class='border fw-600'>Hệ điều hành</td>
                        <td class='border'>
                            {{
                                $current['HDH_CPU']['HDH'] ? $current['HDH_CPU']['HDH'] : $updating
                            }}
                        </td>
                        <td class='border'>
                            {{
                                $compare['HDH_CPU']['HDH'] ? $compare['HDH_CPU']['HDH'] : $updating
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['HDH_CPU']['HDH'] ? $third['HDH_CPU']['HDH'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td class='border fw-600'>Camera sau</td>
                        <td class='border'>
                            {{
                                $current['camera_sau']['do_phan_giai'] ? $current['camera_sau']['do_phan_giai'] : $updating
                            }}
                        </td>
                        <td class='border'>
                            {{
                                $compare['camera_sau']['do_phan_giai'] ? $compare['camera_sau']['do_phan_giai'] : $updating
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ?
                                    ($third['camera_sau']['do_phan_giai'] ? $third['camera_sau']['do_phan_giai'] : $updating)
                                : ''
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td class='border fw-600'>Camera trước</td>
                        <td class='border'>
                            {{
                                $current['camera_truoc']['do_phan_giai'] ? $current['camera_truoc']['do_phan_giai'] : $updating
                            }}
                        </td>
                        <td class='border'>
                            {{
                                $compare['camera_truoc']['do_phan_giai'] ? $compare['camera_truoc']['do_phan_giai'] : $updating
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ?
                                    ($third['camera_truoc']['do_phan_giai'] ? $third['camera_truoc']['do_phan_giai'] : $updating)
                                : ''
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td class='border fw-600'>CPU</td>
                        <td class='border'>
                            {{
                                $current['HDH_CPU']['CPU'] ? $current['HDH_CPU']['CPU'] : $updating
                            }}
                        </td>
                        <td class='border'>
                            {{
                                $compare['HDH_CPU']['CPU'] ? $compare['HDH_CPU']['CPU'] : $updating
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ?
                                    ($third['HDH_CPU']['CPU'] ? $third['HDH_CPU']['CPU'] : $updating)
                                : ''
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td class='border fw-600'>RAM</td>
                        <td class='border'>
                            {{
                                $current['luu_tru']['RAM'] ? $current['luu_tru']['RAM'] : $updating
                            }}
                        </td>
                        <td class='border'>
                            {{
                                $compare['luu_tru']['RAM'] ? $compare['luu_tru']['RAM'] : $updating
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ?
                                    ($third['luu_tru']['RAM'] ? $third['luu_tru']['RAM'] : $updating)
                                : ''
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td class='border fw-600'>Bộ nhớ trong</td>
                        <td class='border'>
                            {{
                                $current['luu_tru']['bo_nho_trong'] ? $current['luu_tru']['bo_nho_trong'] : $updating
                            }}
                        </td>
                        <td class='border'>
                            {{
                                $compare['luu_tru']['bo_nho_trong'] ? $compare['luu_tru']['bo_nho_trong'] : $updating
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ?
                                    ($third['luu_tru']['bo_nho_trong'] ? $third['luu_tru']['bo_nho_trong'] : $updating)
                                : ''
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td class='border fw-600'>SIM</td>
                        <td class='border'>
                            {{
                                ($current['ket_noi']['SIM'] ? $current['ket_noi']['SIM'] : $updating)
                                . ', ' .
                                ($current['ket_noi']['mang_mobile'] ? $current['ket_noi']['mang_mobile'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['ket_noi']['SIM'] ? $compare['ket_noi']['SIM'] : $updating)
                                . ', ' .
                                ($compare['ket_noi']['mang_mobile'] ? $compare['ket_noi']['mang_mobile'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['ket_noi']['SIM'] ? $third['ket_noi']['SIM'] : $updating)
                                . ', ' .
                                (($third['ket_noi']['mang_mobile'] ? $third['ket_noi']['mang_mobile'] : $updating)  ? ($third['ket_noi']['mang_mobile'] ? $third['ket_noi']['mang_mobile'] : $updating)  : $updating): ''
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td class='border fw-600'>Pin, sạc</td>
                        <td class='border'>
                            {{
                                ($current['pin']['loai'] ? $current['pin']['loai'] : $updating)
                                . ', ' .
                                ($current['pin']['dung_luong'] ? $current['pin']['dung_luong'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['pin']['loai'] ? $compare['pin']['loai'] : $updating)
                                . ', ' .
                                ($compare['pin']['dung_luong'] ? $compare['pin']['dung_luong'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['pin']['loai'] ? $third['pin']['loai'] : $updating)
                                . ', ' .
                                ($third['pin']['dung_luong'] ? $third['pin']['dung_luong'] : $updating): ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-btn-see-detail border'>
                        <td></td>
                        <td colspan="{{empty($third) ? '2' : '3'}}">
                            <div class='compare-btn-see-detail main-btn-2 p-10'>Xem so sánh cấu hình chi tiết<i class="fas fa-caret-down ml-10"></i></div>
                        </td>
                        @if(empty($third))
                        <td class="thirdProduct"></td>
                        @endif
                    </tr>
                    <tr class='compare-detail'>
                        <td colspan="4" class='border p-0'>
                            <div class='detail-specifications-title-2'>thiết kế & trọng lượng</div>
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Thiết kế</td>
                        <td class='border'>
                            {{
                                ($current['thiet_ke_trong_luong']['thiet_ke'] ? $current['thiet_ke_trong_luong']['thiet_ke'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['thiet_ke_trong_luong']['thiet_ke'] ? $compare['thiet_ke_trong_luong']['thiet_ke'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['thiet_ke_trong_luong']['thiet_ke'] ? $third['thiet_ke_trong_luong']['thiet_ke'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Chất liệu</td>
                        <td class='border'>
                            {{
                                ($current['thiet_ke_trong_luong']['chat_lieu'] ? $current['thiet_ke_trong_luong']['chat_lieu'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['thiet_ke_trong_luong']['chat_lieu'] ? $compare['thiet_ke_trong_luong']['chat_lieu'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['thiet_ke_trong_luong']['chat_lieu'] ? $third['thiet_ke_trong_luong']['chat_lieu'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Kích thước</td>
                        <td class='border'>
                            {{
                                ($current['thiet_ke_trong_luong']['kich_thuoc'] ? $current['thiet_ke_trong_luong']['kich_thuoc'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['thiet_ke_trong_luong']['kich_thuoc'] ? $compare['thiet_ke_trong_luong']['kich_thuoc'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['thiet_ke_trong_luong']['kich_thuoc'] ? $third['thiet_ke_trong_luong']['kich_thuoc'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Khối lượng</td>
                        <td class='border'>
                            {{
                                ($current['thiet_ke_trong_luong']['khoi_luong'] ? $current['thiet_ke_trong_luong']['khoi_luong'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['thiet_ke_trong_luong']['khoi_luong'] ? $compare['thiet_ke_trong_luong']['khoi_luong'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['thiet_ke_trong_luong']['khoi_luong'] ? $third['thiet_ke_trong_luong']['khoi_luong'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td colspan="4" class='border p-0'>
                            <div class='detail-specifications-title-2'>màn hình</div>
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Công nghệ màn hình</td>
                        <td class='border'>
                            {{
                                ($current['man_hinh']['cong_nghe_mh'] ? $current['man_hinh']['cong_nghe_mh'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['man_hinh']['cong_nghe_mh'] ? $compare['man_hinh']['cong_nghe_mh'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['man_hinh']['cong_nghe_mh'] ? $third['man_hinh']['cong_nghe_mh'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Độ phân giải</td>
                        <td class='border'>
                            {{
                                ($current['man_hinh']['do_phan_giai'] ? $current['man_hinh']['do_phan_giai'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['man_hinh']['do_phan_giai'] ? $compare['man_hinh']['do_phan_giai'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['man_hinh']['do_phan_giai'] ? $third['man_hinh']['do_phan_giai'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Kích thước màn hình</td>
                        <td class='border'>
                            {{
                                ($current['man_hinh']['ty_le_mh'] ? $current['man_hinh']['ty_le_mh'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['man_hinh']['ty_le_mh'] ? $compare['man_hinh']['ty_le_mh'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['man_hinh']['ty_le_mh'] ? $third['man_hinh']['ty_le_mh'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Mặt kính cảm ứng</td>
                        <td class='border'>
                            {{
                                ($current['man_hinh']['kinh_cam_ung'] ? $current['man_hinh']['kinh_cam_ung'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['man_hinh']['kinh_cam_ung'] ? $compare['man_hinh']['kinh_cam_ung'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['man_hinh']['kinh_cam_ung'] ? $third['man_hinh']['kinh_cam_ung'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td colspan="4" class='border p-0'>
                            <div class='detail-specifications-title-2'>Camrera sau</div>
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Độ phân giải</td>
                        <td class='border'>
                            {{
                                ($current['camera_sau']['do_phan_giai'] ? $current['camera_sau']['do_phan_giai'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['camera_sau']['do_phan_giai'] ? $compare['camera_sau']['do_phan_giai'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['camera_sau']['do_phan_giai'] ? $third['camera_sau']['do_phan_giai'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Quay phim</td>
                        <td class='border'>
                            @if ($current['camera_sau']['quay_phim'][0]['chat_luong'])
                                @foreach ($current['camera_sau']['quay_phim'] as $key)
                                    <div class="mb-5">{{ $key['chat_luong'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='border'>
                            @if ($compare['camera_sau']['quay_phim'][0]['chat_luong'])
                                @foreach ($compare['camera_sau']['quay_phim'] as $key)
                                    <div class="mb-5">{{ $key['chat_luong'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @if ($third['camera_sau']['quay_phim'][0]['chat_luong'])
                                    @foreach ($third['camera_sau']['quay_phim'] as $key)
                                        <div class="mb-5">{{ $key['chat_luong'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Đèn Flash</td>
                        <td class='border'>
                            {{
                                ($current['camera_sau']['den_flash'] ? $current['camera_sau']['den_flash'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['camera_sau']['den_flash'] ? $compare['camera_sau']['den_flash'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['camera_sau']['den_flash'] ? $third['camera_sau']['den_flash'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Tính năng</td>
                        <td class='border'>
                            @if ($current['camera_sau']['tinh_nang'][0]['name'])
                                @foreach ($current['camera_sau']['tinh_nang'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='border'>
                            @if ($compare['camera_sau']['tinh_nang'][0]['name'])
                                @foreach ($compare['camera_sau']['tinh_nang'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @if ($third['camera_sau']['tinh_nang'][0]['name'])
                                    @foreach ($third['camera_sau']['tinh_nang'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif    
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td colspan="4" class='border p-0'>
                            <div class='detail-specifications-title-2'>Camrera trước</div>
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Độ phân giải</td>
                        <td class='border'>
                            {{
                                ($current['camera_truoc']['do_phan_giai'] ? $current['camera_truoc']['do_phan_giai'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['camera_truoc']['do_phan_giai'] ? $compare['camera_truoc']['do_phan_giai'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ?
                                    ($third['camera_truoc']['do_phan_giai'] ? $third['camera_truoc']['do_phan_giai'] : $updating) 
                                : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Tính năng</td>
                        <td class='border'>
                            @if ($current['camera_truoc']['tinh_nang'][0]['name'])
                                @foreach ($current['camera_truoc']['tinh_nang'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='border'>
                            @if ($compare['camera_truoc']['tinh_nang'][0]['name'])
                                @foreach ($compare['camera_truoc']['tinh_nang'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @if ($third['camera_truoc']['tinh_nang'][0]['name'])
                                    @foreach ($third['camera_truoc']['tinh_nang'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td colspan="4" class='border p-0'>
                            <div class='detail-specifications-title-2'>hệ điều hành & cpu</div>
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Hệ điều hành</td>
                        <td class='border'>
                            {{
                                ($current['HDH_CPU']['HDH'] ? $current['HDH_CPU']['HDH'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['HDH_CPU']['HDH'] ? $compare['HDH_CPU']['HDH'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['HDH_CPU']['HDH'] ? $third['HDH_CPU']['HDH'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Chip xử lý (CPU)</td>
                        <td class='border'>
                            {{
                                ($current['HDH_CPU']['CPU'] ? $current['HDH_CPU']['CPU'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['HDH_CPU']['CPU'] ? $compare['HDH_CPU']['CPU'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['HDH_CPU']['CPU'] ? $third['HDH_CPU']['CPU'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Tốc độ CPU</td>
                        <td class='border'>
                            {{
                                ($current['HDH_CPU']['CPU_speed'] ? $current['HDH_CPU']['CPU_speed'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['HDH_CPU']['CPU_speed'] ? $compare['HDH_CPU']['CPU_speed'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['HDH_CPU']['CPU_speed'] ? $third['HDH_CPU']['CPU_speed'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Chip đồ họa (GPU)</td>
                        <td class='border'>
                            {{
                                ($current['HDH_CPU']['GPU'] ? $current['HDH_CPU']['GPU'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['HDH_CPU']['GPU'] ? $compare['HDH_CPU']['GPU'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['HDH_CPU']['GPU'] ? $third['HDH_CPU']['GPU'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td colspan="4" class=' border p-0'>
                            <div class='detail-specifications-title-2'>bộ nhớ & lưu trữ</div>
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>RAM</td>
                        <td class='border'>
                            {{
                                ($current['luu_tru']['RAM'] ? $current['luu_tru']['RAM'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['luu_tru']['RAM'] ? $compare['luu_tru']['RAM'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['luu_tru']['RAM'] ? $third['luu_tru']['RAM'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Bộ nhớ trong</td>
                        <td class='border'>
                            {{
                                ($current['luu_tru']['bo_nho_trong'] ? $current['luu_tru']['bo_nho_trong'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['luu_tru']['bo_nho_trong'] ? $compare['luu_tru']['bo_nho_trong'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['luu_tru']['bo_nho_trong'] ? $third['luu_tru']['bo_nho_trong'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Bộ nhớ còn lại (Khả dụng)</td>
                        <td class='border'>
                            {{
                                ($current['luu_tru']['bo_nho_con_lai'] ? $current['luu_tru']['bo_nho_con_lai'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['luu_tru']['bo_nho_con_lai'] ? $compare['luu_tru']['bo_nho_con_lai'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['luu_tru']['bo_nho_con_lai'] ? $third['luu_tru']['bo_nho_con_lai'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Thẻ nhớ</td>
                        <td class='border'>
                            {{
                                ($current['luu_tru']['the_nho'] ? $current['luu_tru']['the_nho'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['luu_tru']['the_nho'] ? $compare['luu_tru']['the_nho'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['luu_tru']['the_nho'] ? $third['luu_tru']['the_nho'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td colspan="4" class='border p-0'>
                            <div class='detail-specifications-title-2'>kết nối</div>
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Mạng di động</td>
                        <td class='border'>
                            {{
                                ($current['ket_noi']['mang_mobile'] ? $current['ket_noi']['mang_mobile'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['ket_noi']['mang_mobile'] ? $compare['ket_noi']['mang_mobile'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['ket_noi']['mang_mobile'] ? $third['ket_noi']['mang_mobile'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>SIM</td>
                        <td class='border'>
                            {{
                                ($current['ket_noi']['SIM'] ? $current['ket_noi']['SIM'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['ket_noi']['SIM'] ? $compare['ket_noi']['SIM'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['ket_noi']['SIM'] ? $third['ket_noi']['SIM'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Wifi</td>
                        <td class='border'>
                            @if ($current['ket_noi']['wifi'][0]['name'])
                                @foreach ($current['ket_noi']['wifi'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='border'>
                            @if ($compare['ket_noi']['wifi'][0]['name'])
                                @foreach ($compare['ket_noi']['wifi'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @if ($third['ket_noi']['wifi'][0]['name'])
                                    @foreach ($third['ket_noi']['wifi'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif    
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>GPS</td>
                        <td class='border'>
                            @if ($current['ket_noi']['GPS'][0]['name'])
                                @foreach ($current['ket_noi']['GPS'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='border'>
                            @if ($compare['ket_noi']['GPS'][0]['name'])
                                @foreach ($compare['ket_noi']['GPS'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @if ($third['ket_noi']['GPS'][0]['name'])
                                    @foreach ($third['ket_noi']['GPS'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif    
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Bluetooth</td>
                        <td class='border'>
                            @if ($current['ket_noi']['bluetooth'][0]['name'])
                                @foreach ($current['ket_noi']['bluetooth'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='border'>
                            @if ($compare['ket_noi']['bluetooth'][0]['name'])
                                @foreach ($compare['ket_noi']['bluetooth'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @if ($third['ket_noi']['bluetooth'][0]['name'])
                                    @foreach ($third['ket_noi']['bluetooth'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif    
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Cổng kết nối/sạc</td>
                        <td class='border'>
                            {{
                                ($current['ket_noi']['cong_sac'] ? $current['ket_noi']['cong_sac'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['ket_noi']['cong_sac'] ? $compare['ket_noi']['cong_sac'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? $third['ket_noi']['cong_sac'] : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Jack tai nghe</td>
                        <td class='border'>
                            {{
                                ($current['ket_noi']['jack_tai_nghe'] ? $current['ket_noi']['jack_tai_nghe'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['ket_noi']['jack_tai_nghe'] ? $compare['ket_noi']['jack_tai_nghe'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['ket_noi']['jack_tai_nghe'] ? $third['ket_noi']['jack_tai_nghe'] :$updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Kết nối khác</td>
                        <td class='border'>
                            @foreach ($current['ket_noi']['ket_noi_khac'] as $key)
                                <div class="mb-5">{{ $key['name'] }}</div>
                            @endforeach
                        </td>
                        <td class='border'>
                            @foreach ($compare['ket_noi']['ket_noi_khac'] as $key)
                                <div class="mb-5">{{ $key['name'] }}</div>
                            @endforeach
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @foreach ($third['ket_noi']['ket_noi_khac'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td colspan="4" class='border p-0'>
                            <div class='detail-specifications-title-2'>pin & sạc</div>
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Loại pin</td>
                        <td class='border'>
                            {{
                                ($current['pin']['loai'] ? $current['pin']['loai'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['pin']['loai'] ? $compare['pin']['loai'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['pin']['loai'] ? $third['pin']['loai'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Dung lượng pin</td>
                        <td class='border'>
                            {{
                                ($current['pin']['dung_luong'] ? $current['pin']['dung_luong'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['pin']['dung_luong'] ? $compare['pin']['dung_luong'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['pin']['dung_luong'] ? $third['pin']['dung_luong'] : $updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Công nghệ pin</td>
                        <td class='border'>
                            @if ($current['pin']['cong_nghe'][0]['name'])
                                @foreach ($current['pin']['cong_nghe'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='border'>
                            @if ($compare['pin']['cong_nghe'][0]['name'])
                                @foreach ($compare['pin']['cong_nghe'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @if ($third['pin']['cong_nghe'][0]['name'])    
                                    @foreach ($third['pin']['cong_nghe'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td colspan="4" class='border p-0'>
                            <div class='detail-specifications-title-2'>tiện ích</div>
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Bảo mật nâng cao</td>
                        <td class='border'>
                            @if ($current['tien_ich']['bao_mat'][0]['name'])
                                @foreach ($current['tien_ich']['bao_mat'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='border'>
                            @if ($compare['tien_ich']['bao_mat'][0]['name'])
                                @foreach ($compare['tien_ich']['bao_mat'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @if ($third['tien_ich']['bao_mat'][0]['name'])    
                                    @foreach ($third['tien_ich']['bao_mat'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Tính năng đặc biệt</td>
                        <td class='border'>
                            @if ($current['tien_ich']['tinh_nang_khac'][0]['name'])
                                @foreach ($current['tien_ich']['tinh_nang_khac'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='border'>
                            @if ($compare['tien_ich']['tinh_nang_khac'][0]['name'])
                                @foreach ($compare['tien_ich']['tinh_nang_khac'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @if ($third['tien_ich']['tinh_nang_khac'][0]['name'])    
                                    @foreach ($third['tien_ich']['tinh_nang_khac'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Ghi âm</td>
                        <td class='border'>
                            {{
                                ($current['tien_ich']['ghi_am'] ? $current['tien_ich']['ghi_am'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($compare['tien_ich']['ghi_am'] ? $compare['tien_ich']['ghi_am'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? ($third['tien_ich']['ghi_am'] ? $third['tien_ich']['ghi_am'] :$updating) : ''
                            }}
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Xem phim</td>
                        <td class='border'>
                            @if ($current['tien_ich']['xem_phim'][0]['name'])
                                @foreach ($current['tien_ich']['xem_phim'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='border'>
                            @if ($compare['tien_ich']['xem_phim'][0]['name'])
                                @foreach ($compare['tien_ich']['xem_phim'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @if ($third['tien_ich']['xem_phim'][0]['name'])
                                    @foreach ($third['tien_ich']['xem_phim'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif    
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Nghe nhạc</td>
                        <td class='border'>
                            @if ($current['tien_ich']['nghe_nhac'][0]['name'])
                                @foreach ($current['tien_ich']['nghe_nhac'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='border'>
                            @if ($compare['tien_ich']['nghe_nhac'][0]['name'])
                                @foreach ($compare['tien_ich']['nghe_nhac'] as $key)
                                    <div class="mb-5">{{ $key['name'] }}</div>
                                @endforeach
                            @else
                                {{$updating}}
                            @endif
                        </td>
                        <td class='thirdProduct border'>
                            @if (!empty($third))
                                @if ($third['tien_ich']['nghe_nhac'][0]['name'])
                                    @foreach ($third['tien_ich']['nghe_nhac'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif    
                            @endif
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td colspan="4" class='border p-0'>
                            <div class='detail-specifications-title-2'>thông tin khác</div>
                        </td>
                    </tr>
                    <tr class='compare-detail'>
                        <td class='border fw-600'>Thời điểm ra mắt</td>
                        <td class='border'>
                            {{
                                ($currentProduct['cauhinh']['thong_tin_khac']['thoi_diem_ra_mat'] ? $currentProduct['cauhinh']['thong_tin_khac']['thoi_diem_ra_mat'] : $updating)
                            }}
                        </td>
                        <td class='border'>
                            {{
                                ($currentProduct['cauhinh']['thong_tin_khac']['thoi_diem_ra_mat'] ? $currentProduct['cauhinh']['thong_tin_khac']['thoi_diem_ra_mat'] : $updating)
                            }}
                        </td>
                        <td class='thirdProduct border'>
                            {{
                                !empty($third) ? 
                                ($thirdProduct['cauhinh']['thong_tin_khac']['thoi_diem_ra_mat'] ? $thirdProduct['cauhinh']['thong_tin_khac']['thoi_diem_ra_mat'] : $updating)
                                : ''
                            }}
                        </td>
                    </tr>
                    <tr class='border'>
                        <td></td>
                        <td>
                            <?php $product = $currentProduct['sanpham'] ?>
                            @if ($product['modelStatus'] && !$product['comingSoon'] && $product['qtyInStock'])
                                <div data-id="{{$product['id']}}"class="compare-add-card main-btn w-100 fw-600 p-10"><i class="fas fa-cart-plus mr-10"></i>Thêm vào giỏ hàng</div>
                            @endif
                        </td>
                        <td>
                            <?php $product = $compareProduct['sanpham'] ?>
                            @if ($product['modelStatus'] && !$product['comingSoon'] && $product['qtyInStock'])
                                <div data-id="{{$product['id']}}"class="compare-add-card main-btn w-100 fw-600 p-10"><i class="fas fa-cart-plus mr-10"></i>Thêm vào giỏ hàng</div>
                            @endif
                        </td>
                        @if (!empty($third))
                            <td>
                            <?php $product = $thirdProduct['sanpham'] ?>
                            @if ($product['modelStatus'] && !$product['comingSoon'] && $product['qtyInStock'])
                                <div data-id="{{$product['id']}}"class="compare-add-card main-btn w-100 fw-600 p-10"><i class="fas fa-cart-plus mr-10"></i>Thêm vào giỏ hàng</div>
                            @endif
                            </td>
                        @else
                            <td class="thirdProduct"></td>
                        @endif
        
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
</div>

{{-- modal thêm sản phẩm so sánh --}}
<div class="modal fade" id="compare-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class='fz-22 fw-600'>Chọn điện thoại để so sánh</div>
                <div type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></div>
            </div>
            <div class="modal-body p-20">
                <div class='pt-10 pb-10'>
                    <input type="text" id='compare-search-phone' placeholder="Nhập tên điện thoại muốn so sánh">
                    <div class='compare-list-search-phone'>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal chọn màu sản phẩm --}}
@include("user.content.modal.chon-mau-sac-modal");

@include('user.content.section.sec-logo')

@stop