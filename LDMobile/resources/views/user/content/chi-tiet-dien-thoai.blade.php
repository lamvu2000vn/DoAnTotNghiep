@extends("user.layout")
@section("title") {{$phone['tensp']}} | LDMobile @stop
@section("content")

@section("breadcrumb")
    <a href="{{route('user/dien-thoai')}}" class="bc-item">Điện thoại</a>
    <div class="bc-divider"><i class="fas fa-chevron-right"></i></div>
    <a href="{{route('user/chi-tiet', ['name' => $phone['tensp_url']])}}" class="bc-item active">{{$phone['tensp']}}</a>
@stop
@include("user.content.section.sec-thanh-dieu-huong")

<section class='pt-10 pb-50'>
    <div class='container'>
        {{-- tên điện thoại, sao, lượt đánh giá --}}
        <div class='d-flex flex-row align-items-center justify-content-between'>
            <div class="d-flex align-items-center flex-wrap">
                <div class='detail-product-name'>{{ $phone['tensp'] }}</div>
                <div>
                    @if ($starRating['total-rating'] != 0)
                        @for ($i = 1; $i <= 5; $i++)
                            @if($starRating['total-star'] >= $i)
                            <i class="fas fa-star checked"></i>
                            @else
                            <i class="fas fa-star uncheck"></i>
                            @endif
                        @endfor
                        <span class='ml-10'>{{ $phone['danhgia']['qty'].' đánh giá' }}</span>
                    @else
                        @for ($i = 0; $i < 5; $i++)
                            <i class="fas fa-star uncheck"></i>
                        @endfor
                    @endif
                </div>
            </div>
            {{-- nút yêu thích --}}
            <div data-name="{{$phone['tensp']}}" class="favorite-tag">
                <i class="far fa-heart"></i>
            </div>
        </div><hr>

        {{-- điện thoại --}}
        <div class='row'>
            {{-- hình ảnh --}}
            <div class='col-lg-4 col-md-6 col-12 mb-20'>
                <div class='d-flex flex-column'>
                    {{-- ảnh sản phẩm --}}
                    <div class="main-img-wrapper">
                        <img id='main-img' src="{{ $url_phone.$phone['hinhanh'] }}" alt="product-image">
                    </div>
                    <div class="detail-another-color-label"></div>
                    {{-- ảnh khác --}}
                    <div class='detail-another-div'>
                        <div id='detail-carousel' class="owl-carousel">
                            @foreach($lst_variation['image'] as $key)
                                <img class="another-img" data-id="{{$key['id']}}" src="{{ $url_phone.$key['hinhanh'] }}">
                            @endforeach
                        </div>
                        @if (count($lst_variation['image']) > 4)
                            <div style='display: flex'>
                                <div id="prev-another-img" class='prev-owl-carousel btn-owl-left-style-2'><i class="far fa-chevron-left fz-20"></i></div>
                                <div id="next-another-img" class='next-owl-carousel btn-owl-right-style-2'><i class="far fa-chevron-right fz-20"></i></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- nếu mẫu sp theo dung lượng đang kinh doanh --}}
            @if ($phone['modelStatus'])
                <div class='col-lg-4 col-md-6 mb-20 fz-14'>
                    {{-- giá --}}
                    <div id="detail-price-component">
                        <div class='fw-600 red fz-26'>{{ number_format($phone['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></div>
                        {{-- khuyến mãi còn hạn --}}
                        @if (!empty($phone['khuyenmai']) && $phone['khuyenmai']['trangthaikhuyenmai'])
                            <div class="d-flex align-items-end ml-20">
                                <div class="fz-14">Giá niêm yết :</div>
                                <b class='ml-5 text-strike fz-16'>{{ number_format($phone['gia'], 0, '', '.') }}<sup>đ</sup></b>
                            </div>    
                        @endif
                    </div>
                    
                    {{-- dung lượng --}}
                    <div id="capacity-options" class="mb-10">
                        <div class='detail-title'>Dung lượng</div>
                        <div class='row row-cols-3'>
                            @foreach ($distinctCapacityList as $key)
                                <div class="col">
                                    @if ($key['dungluong'] === $phone['dungluong'])
                                        <div type='button' class='detail-option selected'>
                                            <div class="fw-600">{{ $key['dungluong'] }}</div>
                                            <div class="red fw-600 mt-5">{{ number_format($key['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></div>
                                        </div>
                                    @else
                                        <a href="{{route('user/chi-tiet', ['name' => $key['tensp_url']])}}" class='detail-option'>
                                            <div>{{ $key['dungluong'] }}</div>
                                            <div class="red fw-600 mt-5">{{ number_format($key['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></div>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ram --}}
                    @if (count($distinctRamList) > 1)
                        <div class="ram-options mb-10">
                            <div class='detail-title'>Ram</div>
                            <div class='row row-cols-3'>
                                @foreach ($distinctRamList as $key)
                                    <div class="col">
                                        @if ($key['ram'] === $phone['ram'])
                                            <div type='button' class='detail-option selected'>
                                                <div class="fw-600 mb-5">{{ $key['ram'] }}</div>
                                                <div class="red fw-600">{{ number_format($key['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></div>
                                            </div>
                                        @else
                                            <a href="{{route('user/chi-tiet', ['name' => $key['tensp_url']])}}" class='detail-option'>
                                                <div class="mb-5">{{ $key['ram'] }}</div>
                                                <div class="red fw-600">{{ number_format($key['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></div>
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
    
                    {{-- màu sắc --}}
                    <div id="color-options" class="mb-40">
                        <div class='detail-title'>Màu sắc</div>
                        <div class='row row-cols-3'>
                            @foreach ($lst_variation['color'] as $key)
                                <div class="col">
                                    <div type='button' class='color-option detail-option box-shadow {{$key['mausac'] == $phone['mausac'] ? 'selected' : ''}}' data-image="{{ $url_phone.$key['hinhanh'] }}"
                                        data-id="{{$key['id']}}" data-color="{{$key['mausac']}}" favorite="{{$key['yeuthich']}}">
                                        <div class="color-name {{$key['mausac'] == $phone['mausac'] ? 'fw-600' : ''}}">{{ $key['mausac'] }}</div>
                                        <div class="red fw-600 mt-5">{{ number_format($key['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
    
                    {{-- khuyến mãi còn hạn --}}
                    @if (!empty($phone['khuyenmai']) && $phone['khuyenmai']['trangthaikhuyenmai'])
                        <div class='detail-promotion mb-20'>
                            <div class='detail-promotion-text'><i class="fas fa-gift mr-5"></i>KHUYẾN MÃI</div>
                            <div class='detail-title'><i class="fas fa-check-circle mr-10 main-color-text"></i>{{ $phone['khuyenmai']['tenkm']}}</div>
                            <div class='detail-promotion-content'>
                                {{ $phone['khuyenmai']['noidung'] }}                           
                            </div>
                        </div>
                    @endif
    
                    {{-- đặt hàng trước --}}
                    @if ($phone['trangthai'] && ($phone['comingSoon'] || !$phone['inStocks']))
                        <div class="call-to-order-wrapped mb-20">
                            <div class="d-flex align-items-center mb-10">
                                <i class="fas fa-phone main-color-text mr-10"></i>
                                <div class="fw-600">Gọi đặt hàng trước</div>
                            </div>
                            <div class="ml-20">
                                Để biết thêm về cách thức đặt hàng, xin vui lòng liên hệ với chúng tôi.
                                <a href="lienhe" class="ml-5">liên hệ</a>
                            </div>
                        </div>
                    @endif
    
                    {{-- mua ngay --}}
                    <div type="button" data-id="{{$phone['id']}}" class='buy-now main-btn p-10 fz-20 fw-600'><i class="fas fa-cart-plus mr-10"></i>MUA NGAY</div>
                </div>

                {{-- chi nhánh --}}
                <div class='col-lg-4 col-md-8 mb-20'>
                    @include('user.content.components.chitiet.right-side')
                </div>
            @else
                <div class='col-lg-4 col-md-6 mb-20 fz-14'>
                    <div id="qty-in-stock-status" class="mb-10">SẢN PHẨM NGỪNG KINH DOANH</div>
                    <div>
                        Liên hệ
                        <b class="ml-5 mr-5">077 979 2000</b>hoặc
                        <b class="ml-5 mr-5">038 415 1501</b> Để được tư vấn
                    </div>
                    <hr>
                    @include('user.content.components.chitiet.right-side')
                </div>

                <div class='col-lg-4 col-md-8 mb-20'>    
                    @include('user.content.components.chitiet.thong-so-ky-thuat')
                </div>
            @endif
        </div> <hr>

        {{-- Bài giới thiệu & thông số --}}
        <div class='row'>
            {{-- bài giới thiệu --}}
            <div class='col-lg-8 col-12'>
                <div id="carouselExampleIndicators" class="carousel carousel-dark slide" data-bs-interval="false" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <iframe id="youtube-iframe" height="400" class="w-100" allowfullscreen
                                src="{{ 'https://www.youtube.com/embed/' . $phone['id_youtube'] }}">
                            </iframe>
                        </div>
                        @foreach($slide_model as $key)
                        <div class="carousel-item">
                            <img src="{{ $url_model_slide.$key['hinhanh'] }}" class="carousel-img" alt="...">
                        </div>
                        @endforeach
                    </div>
                    <div class="slideshow-btn-prev" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                        <i class="far fa-chevron-left"></i>
                    </div>
                    <div class='slideshow-btn-next' data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                        <i class="far fa-chevron-right"></i>
                    </div>
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                            aria-current="true"></button>
                        @for ($i = 1; $i < count($slide_model) + 1; $i++)
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="{{$i}}"></button>
                        @endfor
                    </div>
                </div>
            </div>
            
            {{-- thông số kỹ thuật --}}
            @if ($phone['modelStatus'])
                <div class='col-lg-4 col-12'>
                    <?php
                        $specifications = $phone['cauhinh']['thong_so_ky_thuat'];
                        $updating = 'Đang cập nhật';
                    ?>

                    @include('user.content.components.chitiet.thong-so-ky-thuat')
                </div>
            @endif
        </div>

        {{-- sản phẩm cùng hãng --}}
        <div class='row pt-50'>
            <div class='col-12'>
                <div class="detail-item">
                    <div class='detail-item-title'>Cùng thương hiệu {{ $supplier['brand'] }}</div>
                    <div class='relative'>
                        <div id='same-brand-pro-carousel' class="owl-carousel owl-theme m-0">
                            @foreach($lst_proSameBrand as $key)
                            <div class='detail-item-content'>
                                {{-- hình ảnh --}}
                                <div class="relative">
                                    @if ($key['comingSoon'])
                                        <div class="coming-soon">HÀNG SẮP VỀ</div>
                                    @endif
                                    <img src="{{ $url_phone.$key['hinhanh'] }}" alt="">
                                </div>
                    
                                {{-- tên điện thoại --}}
                                <div class='detail-item-phone-name'>
                                    <a href="{{route('user/chi-tiet', ['name' => $key['tensp_url']])}}">{{ $key['tensp'] }}</a>
                                </div>
                                {{-- giá --}}
                                <div class="d-flex justify-content-center">
                                    <div>
                                        <div class='d-flex flex-column fz-14'>
                                            <span class="red fw-600">{{ number_format($key['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></span>
                                            {{-- khuyến mãi hết hạn --}}
                                            @if($key['khuyenmai'] != 0)
                                                <div>
                                                    <span class='text-strike'>{{ number_format($key['gia'], 0, '', '.') }}<sup>đ</sup></span>
                                                    <span class='pl-5 pr-5'>|</span>
                                                    <span class='red'>{{ '-'.($key['khuyenmai']*100).'%' }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        {{-- đánh giá --}}
                                        <div class='pt-10'>
                                            @if ($key['danhgia']['qty'] != 0)
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if($key['danhgia']['star'] >= $i)
                                                    <i class="fas fa-star checked"></i>
                                                    @else
                                                    <i class="fas fa-star uncheck"></i>
                                                    @endif
                                                @endfor
                                                <span class='ml-10'>{{ $key['danhgia']['qty'] }} đánh giá</span>
                                            @else
                                                <i class="fas fa-star uncheck"></i>
                                                <i class="fas fa-star uncheck"></i>
                                                <i class="fas fa-star uncheck"></i>
                                                <i class="fas fa-star uncheck"></i>
                                                <i class="fas fa-star uncheck"></i>
                                            @endif
                                        </div>
                    
                                        {{-- so sánh --}}
                                        <div class='pt-10'>
                                            <div id="{{ 'brand_'.$key['tensp_url'] }}" type='button' class="compare-btn main-color-text">
                                                <i class="fal fa-plus-circle"></i>So sánh
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="d-flex">
                            <div id='prev-brand' class="prev-owl-carousel d-flex align-items-center btn-owl-left-style-1"><i class="fas fa-chevron-left fz-26"></i></div>
                            <div id='next-brand' class="next-owl-carousel d-flex align-items-center btn-owl-right-style-1"><i class="fas fa-chevron-right fz-26"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- sản phẩm tương tự --}}
        @if (count($lst_similarPro) != 0)
            <div class='row pt-50'>
                <div class='col-12'>
                    <div class="detail-item">
                        <div class='detail-item-title'>Sản phẩm tương tự</div>
                        <div class='relative'>
                            <div id='similar-pro-carousel' class="owl-carousel m-0">
                                @foreach($lst_similarPro as $key)
                                <div class='detail-item-content'>
                                    {{-- hình ảnh --}}
                                    <div class="relative">
                                        @if ($key['comingSoon'])
                                            <div class="coming-soon">HÀNG SẮP VỀ</div>
                                        @endif
                                        <img src="{{ $url_phone.$key['hinhanh'] }}" alt="">
                                    </div>
                                    {{-- tên điện thoại --}}
                                    <div class='detail-item-phone-name'>
                                        <a href="{{route('user/chi-tiet', ['name' => $key['tensp_url']])}}">{{ $key['tensp'] }}</a>
                                    </div>
                                    {{-- giá --}}
                                    <div class="d-flex justify-content-center">
                                        <div>
                                            <div class='d-flex flex-column fz-14'>
                                                <span class="red fw-600">{{ number_format($key['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></span>
                                                {{-- khuyến mãi hết hạn --}}
                                                @if($key['khuyenmai'] != 0)
                                                    <div>
                                                        <span class='text-strike'>{{ number_format($key['gia'], 0, '', '.') }}<sup>đ</sup></span>
                                                        <span class='pl-5 pr-5'>|</span>
                                                        <span class='red'>{{ '-'.($key['khuyenmai']*100).'%' }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- đánh giá --}}
                                            <div class='pt-10'>
                                                @if ($key['danhgia']['qty'] != 0)
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if($key['danhgia']['star'] >= $i)
                                                        <i class="fas fa-star checked"></i>
                                                        @else
                                                        <i class="fas fa-star uncheck"></i>
                                                        @endif
                                                    @endfor
                                                    <span class='ml-10'>{{$key['danhgia']['qty'].' đánh giá'}}</span>
                                                @else
                                                    <i class="fas fa-star uncheck"></i>
                                                    <i class="fas fa-star uncheck"></i>
                                                    <i class="fas fa-star uncheck"></i>
                                                    <i class="fas fa-star uncheck"></i>
                                                    <i class="fas fa-star uncheck"></i>
                                                @endif
                                            </div>
                                            {{-- so sánh --}}
                                            <div class='pt-10'>
                                                <div id="{{ 'brand_'.$key['tensp_url'] }}" type='button' class="compare-btn main-color-text">
                                                    <i class="fal fa-plus-circle"></i>So sánh
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="d-flex">
                                <div id='prev-similar' class="prev-owl-carousel d-flex align-items-center btn-owl-left-style-1"><i class="fas fa-chevron-left fz-26"></i></div>
                                <div id='next-similar' class="next-owl-carousel d-flex align-items-center btn-owl-right-style-1"><i class="fas fa-chevron-right fz-26"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- gửi đánh giá --}}
        <div id="evaluate-section" class='row pt-50 pb-50'>
            <div class="col-12">
                <div class='fz-20 fw-600 mb-10 p-0'>
                    @if ($starRating['total-rating'] != 0)
                        {{ $starRating['total-rating']}} đánh giá {{ $phone['tensp'] }}
                    @else
                        <div class='fz-20 fw-600'>Hãy là người đầu tiên đánh giá {{ $phone['tensp'] }}</div>
                    @endif
                </div>
            </div>
            
            <div class="col-12">
                <div class="d-flex flex-wrap">
                    {{-- nếu đã có đánh giá --}}
                    @if ($starRating['total-rating'] != 0)
                        <div class="star-rating-div">
                            <span class='detail-vote-avg'></span>
                            <i class="fas fa-star ml-5"></i>
                        </div>
                        <div class="detail-star-rating-div">
                            {{-- tổng số lương đánh giá --}}
                            <input type="hidden" id='total_rating' value='{{ $starRating['total-rating'] }}'>
                            {{-- sao đánh giá --}}
                            <div class="w-100">
                                @for ($i = 5; $i >= 1; $i--)
                                    <div class='d-flex justify-content-between p-5'>
                                        <div class='d-flex align-items-center w-5'>
                                            <span>{{ $i }}</span>
                                            <i class="fas fa-star checked ml-5"></i>
                                        </div>
                                        <div class='d-flex align-items-center w-60'>
                                            <div class='detail-progress-bar'>
                                                <div id='{{'percent-' . $i . '-star'}}'
                                                    data-id='{{ $starRating['rating'][$i] }}'>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='d-flex align-items-center w-20'>{{ $starRating['rating'][$i] }} đánh giá</div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endif
                    <div class="submit-evaluate">
                        {{-- chưa mua hàng --}}
                        @if (empty($haveNotEvaluated))
                            <div class='d-flex justify-content-center w-100 p-40'>Nhanh tay sở hữu sản phẩm.</div>
                        {{-- gửi đánh giá --}}
                        @else
                            <div class="w-100 p-20">
                                <div class='d-flex flex-column'>
                                    {{-- sao đánh giá --}}
                                    <div class="d-flex align-items-center">
                                        <span>Chọn đánh giá của bạn</span>
                                        <div class='d-flex align-items-center ml-10'>
                                            <i class="star-rating fas fa-star gray-2 fz-20 pr-5" data-id='1'></i>
                                            <i class="star-rating fas fa-star gray-2 fz-20 pr-5" data-id='2'></i>
                                            <i class="star-rating fas fa-star gray-2 fz-20 pr-5" data-id='3'></i>
                                            <i class="star-rating fas fa-star gray-2 fz-20 pr-5" data-id='4'></i>
                                            <i class="star-rating fas fa-star gray-2 fz-20 pr-5" data-id='5'></i>
                                            <input type="hidden" id='star_rating' name='star_rating' value='0'>
                                        </div>
                                    </div>
                                    {{-- sản phẩm đánh giá --}}
                                    <div id="phone-evaluate-div" class="mt-20">
                                        <div id="phone-evaluate-show" type="button" class="main-color-text"><i class="fal fa-mobile mr-10"></i>Chọn điện thoại muốn đánh giá</div>
                                    </div>
                                    <input type="hidden" id="lst_id" name="lst_id">
                
                                    {{-- đánh giá --}}
                                    <div class='pt-20'>
                                        <div class="fw-600 mb-5">Đánh giá</div>
                                        <textarea id="evaluate_content" name="evaluate_content" rows="3" maxlength="250"
                                            placeholder="Hãy chia sẽ cảm nhận của bạn về sản phẩm (Tối đa 250 ký tự)"></textarea>
                                        {{-- ảnh đính kèm & gửi đánh giá --}}
                                        <div class='d-flex justify-content-between align-items-center pt-10'>
                                            <div class="d-flex">
                                                <input type="hidden" class='qty-img-inp' value='0'>
                                                <input class='upload-evaluate-image none-dp' type="file" multiple accept="image/*">
                                                <div id='btn-photo-attached' class='pointer-cs'>
                                                    <i class="fas fa-camera"></i>
                                                    <span>Ảnh đính kèm</span>
                                                    <span class='qty-img'></span>
                                                </div>
                                            </div>
                                            <div id="send-evaluate-btn" class='main-btn p-10'>Gửi đánh giá</div>
                                        </div>
                                        <div class="evaluate-img-div"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- đánh giá sản phẩm và đã đăng nhập --}}
        <div id="comment-section">
            @if (session('user'))
                @if ($starRating['total-rating'] != 0)
                    <div id='list-comment' class="row">
                        <div class="col-lg-8 col-md-10-col-12">
                            {{-- đánh giá của người dùng --}}
                            @if (!empty($userEvaluate))
                                <div class="fw-600 fz-20 mb-10">Đánh giá của tôi</div>
                                <div class="user-evaluate">
                                    @foreach ($userEvaluate as $key)
                                        <div data-id="{{$key['id']}}" class="evaluate">
                                            <div class='d-flex flex-column mt-20 mb-20'>
                                                {{-- ảnh đại diện & tên & sao & ngày đăng --}}
                                                <div class='d-flex'>
                                                    <img src="{{$key['taikhoan']['anhdaidien']}}" class='evaluate-user-avt' alt="">
                                                    <div class='d-flex flex-column justify-content-between pl-20'>
                                                        <div class="d-flex align-items-center flex-wrap">
                                                            <b class="mr-10">{{ $key['taikhoan']['hoten'] }}</b>
                                                            <div class="success-color fz-14"><i class="fas fa-check-circle mr-5"></i>Đã mua hàng tại LDMobile</div>
                                                        </div>
                                                        
                                                        <div class='d-flex align-items-center flex-wrap'>
                                                            {{-- icon ngôi sao --}}
                                                            <div class="d-flex align-items center mr-10">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @if($key['danhgia'] >= $i)
                                                                    <i class="fas fa-star checked"></i>
                                                                    @else
                                                                    <i class="fas fa-star uncheck"></i>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                            {{-- màu sắc đánh giá --}}
                                                            <div class="gray-1">Màu sắc: {{ $key['sanpham']['mausac'] }}</div>
                                                        </div>
                                                        {{-- sao đánh giá --}}
                                                        <input type="hidden" id="evaluate-rating-{{$key['id']}}" value="{{$key['danhgia']}}">
                                                        <div class="d-flex align-items-center flex-wrap">
                                                            {{-- ngày đăng --}}
                                                            <div class="mr-10">{{ $key['thoigian']}}</div>
                                                            {{-- chỉnh sửa --}}
                                                            @if ($key['chinhsua'] === 1)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-circle mr-10 fz-6 gray-1"></i>
                                                                    <i class="gray-1 fw-600">Đã chỉnh sửa</i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- nội dung --}}
                                                <div class='evaluate-content-div pt-10'>{{ $key['noidung'] }}</div>
                                                <input type="hidden" id="evaluate-content-{{$key['id']}}" value="{{ $key['noidung'] }}">

                                                {{-- hình ảnh --}}
                                                @if (count($key['hinhanh']) != 0)
                                                    <div class='pt-10'>
                                                        @foreach ($key['hinhanh'] as $img)
                                                            <img type="button" data-id="{{$img['id']}}" data-evaluate="{{$key['id']}}"
                                                                data-name="{{$img['hinhanh']}}"
                                                                src="{{$url_evaluate.$img['hinhanh']}}" alt="" class='img-evaluate'>    
                                                        @endforeach
                                                    </div>    
                                                @endif

                                                {{-- like & reply & edit & delete --}}
                                                <div class='mt-20 mb-20 d-flex align-items-center'>
                                                    {{-- nút thích --}}
                                                    <div type="button" data-id="{{$key['id']}}" class='like-comment {{$key['liked'] ? 'liked-comment' : ''}}'>
                                                        <i id="like-icon" class="{{$key['liked'] ? "fas fa-thumbs-up" : "fal fa-thumbs-up"}} ml-5 mr-5"></i>Hữu ích
                                                        (<div data-id="{{$key['id']}}" class="qty-like-comment">{{ $key['soluotthich'] }}</div>)
                                                    </div>
                                                    {{-- trả lời --}}
                                                    <div data-id="{{$key['id']}}" class="reply-btn">
                                                        <i class="fas fa-reply mr-5"></i>Trả lời
                                                    </div>
                                                    {{-- chỉnh sửa & xóa --}}
                                                    <div class="d-flex ml-40">
                                                        <div data-id="{{$key['id']}}" class="edit-evaluate main-btn p-10 mr-10"><i class="fas fa-pen"></i></div>
                                                        <div data-id="{{$key['id']}}" class="delete-evaluate checkout-btn p-10"><i class="fal fa-trash-alt"></i></div>
                                                    </div>
                                                </div>
                                                {{-- trả lời --}}
                                                <div data-id="{{$key['id']}}" class="reply-div">
                                                    <div class="d-flex">
                                                        <img src="{{session('user')->htdn == 'normal' ? $url_user.session('user')->anhdaidien : session('user')->anhdaidien}}" alt="" width="40px" height="40px" class="circle-img">
                                                        <div class="d-flex flex-column ml-10">
                                                            <textarea data-id="{{$key['id']}}" name="reply-content" id="reply-content-{{$key['id']}}" 
                                                            cols="100" rows="1" placeholder="Nhập câu trả lời (Tối đa 250 ký tự)"
                                                            maxlength="250"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-5">
                                                        <div data-id="{{$key['id']}}" type="button" class="cancel-reply red mr-10"><i class="far fa-times-circle mr-5"></i>Hủy</div>
                                                        <div data-id="{{$key['id']}}" type="button" class="send-reply main-color-text"><i class="fas fa-reply mr-5"></i>Trả lời</div>
                                                    </div>
                                                </div>
                                                {{-- danh sách trả lời --}}
                                                @if ($key['phanhoi-qty'])
                                                    <div data-id="{{$key['id']}}" class="all-reply">
                                                        <div class="d-flex mb-20">
                                                            <img src="{{$key['phanhoi-first']['taikhoan']['anhdaidien']}}" alt="" width="40px" height="40px" class="circle-img">
                                                            <div class="reply-content-div ml-10">
                                                                {{-- họ tên & thời gian reply --}}
                                                                <div class="d-flex align-items-center">
                                                                    <b>{{$key['phanhoi-first']['taikhoan']['hoten']}}</b>
                                                                    <div class="ml-10 mr-10 fz-6 gray-1"><i class="fas fa-circle"></i></div>
                                                                    <div class="gray-1">{{$key['phanhoi-first']['thoigian']}}</div>
                                                                </div>
                                                                {{-- nội dung --}}
                                                                <div class="mt-5">{{$key['phanhoi-first']['noidung']}}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($key['phanhoi-qty'] > 1)
                                                        <div type="button" data-id="{{$key['id']}}" class="see-more-reply main-color-text fw-600"><i class="far fa-level-up mr-10" style="transform: rotate(90deg)"></i>Xem thêm {{$key['phanhoi-qty'] - 1}} câu trả lời</div>
                                                    @endif
                                                @endif
                                            </div>
                                            <hr>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- đánh giá khác --}}
                            @if (!empty($anotherEvaluate))
                                <div class="fw-600 fz-20 mb-10">Đánh giá khác</div>
                                <div class="another-evaluate">
                                    @foreach ($anotherEvaluate as $key)
                                    <div data-id="{{$key['id']}}" class="evaluate">
                                        <div class='d-flex flex-column mt-20 mb-20'>
                                            {{-- ảnh đại diện & tên & sao & ngày đăng --}}
                                            <div class='d-flex'>
                                                <img src="{{$key['taikhoan']['anhdaidien']}}" class='evaluate-user-avt' alt="">
                                                <div class='d-flex flex-column justify-content-between pl-20'>
                                                    <div class="d-flex align-items-center flex-wrap">
                                                        <b class="mr-10">{{ $key['taikhoan']['hoten'] }}</b>
                                                        <div class="success-color fz-14"><i class="fas fa-check-circle mr-5"></i>Đã mua hàng tại LDMobile</div>
                                                    </div>
                                                    
                                                    <div class='d-flex align-items-center flex-wrap'>
                                                        <div class="d-flex align-items center mr-20">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if($key['danhgia'] >= $i)
                                                                <i class="fas fa-star checked"></i>
                                                                @else
                                                                <i class="fas fa-star uncheck"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                        <div class="gray-1">Màu sắc: {{ $key['sanpham']['mausac'] }}</div>
                                                    </div>
                                                    {{-- sao đánh giá --}}
                                                    <input type="hidden" id="evaluate-rating-{{$key['id']}}" value="{{$key['danhgia']}}">
                                                    <div class="d-flex align-items-center flex-wrap">
                                                        {{-- ngày đăng --}}
                                                        <div class="mr-10">{{ $key['thoigian']}}</div>
                                                        {{-- chỉnh sửa --}}
                                                        @if ($key['chinhsua'] === 1)
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-circle mr-10 fz-6 gray-1"></i>
                                                                <i class="gray-1 fw-600">Đã chỉnh sửa</i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- nội dung --}}
                                            <div class='evaluate-content-div pt-10'>
                                                <div>{{ $key['noidung'] }}</div>
                                            </div>

                                            {{-- hình ảnh --}}
                                            @if (count($key['hinhanh']) != 0)
                                                <div class='pt-10'>
                                                    @foreach ($key['hinhanh'] as $img)
                                                        <img type="button" data-id="{{$img['id']}}" data-evaluate="{{$key['id']}}"
                                                            src="{{$url_evaluate.$img['hinhanh']}}" alt="" class='img-evaluate'>    
                                                    @endforeach
                                                </div>    
                                            @endif

                                            {{-- like --}}
                                            <div class='mt-20 mb-20 d-flex align-items-center'>
                                                {{-- nút thích --}}
                                                <div type="button" data-id="{{$key['id']}}" class='like-comment {{$key['liked'] ? 'liked-comment' : ''}}'>
                                                    <i id="like-icon" class="{{$key['liked'] ? "fas fa-thumbs-up" : "fal fa-thumbs-up"}} ml-5 mr-5"></i>Hữu ích
                                                    (<div data-id="{{$key['id']}}" class="qty-like-comment">{{ $key['soluotthich'] }}</div>)
                                                </div>
                                                {{-- trả lời --}}
                                                <div data-id="{{$key['id']}}" class="reply-btn">
                                                    <i class="fas fa-reply mr-5"></i>Trả lời
                                                </div>
                                            </div>
                                            {{-- trả lời --}}
                                            <div data-id="{{$key['id']}}" class="reply-div">
                                                <div class="d-flex">
                                                    <img src="{{session('user')->htdn == 'normal' ? $url_user.session('user')->anhdaidien : session('user')->anhdaidien}}" alt="" width="40px" height="40px" class="circle-img">
                                                    <div class="d-flex flex-column ml-10">
                                                        <textarea data-id="{{$key['id']}}" name="reply-content" id="reply-content-{{$key['id']}}" 
                                                        cols="100" rows="1" placeholder="Nhập câu trả lời (Tối đa 250 ký tự)"
                                                        maxlength="250"></textarea>
                                                    </div>
                                                    
                                                </div>
                                                <div class="d-flex justify-content-end mt-5">
                                                    <div data-id="{{$key['id']}}" type="button" class="cancel-reply red mr-10"><i class="far fa-times-circle mr-5"></i>Hủy</div>
                                                    <div data-id="{{$key['id']}}" type="button" class="send-reply main-color-text"><i class="fas fa-reply mr-5"></i>Trả lời</div>
                                                </div>
                                            </div>
                                            {{-- danh sách trả lời --}}
                                            @if ($key['phanhoi-qty'])
                                                <div data-id="{{$key['id']}}" class="all-reply">
                                                    <div class="d-flex mb-20">
                                                        <img src="{{$key['phanhoi-first']['taikhoan']['anhdaidien']}}" alt="" width="40px" height="40px" class="circle-img">
                                                        <div class="reply-content-div ml-10">
                                                            {{-- họ tên & thời gian reply --}}
                                                            <div class="d-flex align-items-center">
                                                                <b>{{$key['phanhoi-first']['taikhoan']['hoten']}}</b>
                                                                <div class="ml-10 mr-10 fz-6 gray-1"><i class="fas fa-circle"></i></div>
                                                                <div class="gray-1">{{$key['phanhoi-first']['thoigian']}}</div>
                                                            </div>
                                                            {{-- nội dung --}}
                                                            <div class="mt-5">{{$key['phanhoi-first']['noidung']}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($key['phanhoi-qty'] > 1)
                                                    <div type="button" data-id="{{$key['id']}}" class="see-more-reply main-color-text fw-600"><i class="far fa-level-up mr-10" style="transform: rotate(90deg)"></i>Xem thêm {{$key['phanhoi-qty'] - 1}} câu trả lời</div>
                                                @endif
                                            @endif
                                        </div>
                                        <hr>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="p-50 text-center border">Vui lòng đăng nhập để xem đánh giá.</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- xem ảnh đánh giá --}}
<div class="see-review-image-card">
    <div class="see-review-image">
        <div class="review-image-header">
            {{-- nút đóng --}}
            <div class="close-see-review-image"><i class="fas fa-times mr-10"></i>Đóng</div>
        </div>
        {{-- ảnh đang xem và 2 nút prev, next --}}
        <div class="review-image-body">
            <div class="prev-see-review-image"><i class="fas fa-chevron-left"></i></div>
            <img id="review-image-main" src="images/No-image.jpg" alt="review image main">
            <div class="next-see-review-image"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="review-image-footer">
            {{-- ảnh khác --}}
            <div id="another-review-image"></div>
        </div>
    </div>
</div>

{{-- modal kiểm tra còn hàng --}}
<div class="modal fade" id="check-qty-in-stock-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div type="button" class="btn-close" data-bs-dismiss="modal"></div>
                <div class="check-qty-in-stock-container">
                    {{-- tên điện thoại --}}
                    <div id="check-qty-in-stock-phone-name" class="fw-600 fz-22"></div>
                    <hr>
                    {{-- chọn màu cần kiểm tra --}}
                    <div class="fw-600 mb-10">Chọn màu cần kiểm tra</div>
                    <div id="check-qty-in-stock-lst-color"></div>

                    {{-- chọn chi nhánh --}}
                    <div id="check-qty-in-stock-branch">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="mb-10">
                                    <select id="check-qty-in-stock-select">
                                        @foreach ($lst_area as $area)
                                            <option value="{{$area->id}}">{{$area->tentt}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- danh sách chi nhánh --}}
                            <div class="col-lg-8">
                                <div class="list-branch"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal thông số kỹ thuật --}}
<div class="modal fade" id="specifications-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
            <div class="fz-20">Thông số kỹ thuật <b>{{$phone['tensp']}}</b></div>
            <div type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></div>
        </div>
        <div class="modal-body">
            <div class='col-lg-10 col-12 mx-auto'>
                {{-- thông số --}}
                <table class='table'>
                    <tbody class="fz-14">
                        {{-- thiết kế & trọng lượng --}}
                        <tr>
                            <td colspan="2" class="p-0">
                                <div class='detail-specifications-title'>thiết kế & trọng lượng</div>
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Thiết kế</td>
                            <td>
                                {{
                                    $specifications['thiet_ke_trong_luong']['thiet_ke'] ? $specifications['thiet_ke_trong_luong']['thiet_ke'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Chất liệu</td>
                            <td>
                                {{
                                    $specifications['thiet_ke_trong_luong']['chat_lieu'] ? $specifications['thiet_ke_trong_luong']['chat_lieu'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Kích thước</td>
                            <td>
                                {{
                                    $specifications['thiet_ke_trong_luong']['kich_thuoc'] ? $specifications['thiet_ke_trong_luong']['kich_thuoc'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Khối lượng</td>
                            <td>
                                {{
                                    $specifications['thiet_ke_trong_luong']['khoi_luong'] ? $specifications['thiet_ke_trong_luong']['khoi_luong'] : $updating
                                }}
                            </td>
                        </tr>

                        {{-- màn hình --}}
                        <tr>
                            <td colspan="2" class="p-0">
                                <div class='detail-specifications-title'>màn hình</div>
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Công nghệ màn hình</td>
                            <td>
                                {{
                                    $specifications['man_hinh']['cong_nghe_mh'] ? $specifications['man_hinh']['cong_nghe_mh'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Độ phân giải</td>
                            <td>
                                {{
                                    $specifications['man_hinh']['do_phan_giai'] ? $specifications['man_hinh']['do_phan_giai'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Mặt kính cảm ứng</td>
                            <td>
                                {{
                                    $specifications['man_hinh']['kinh_cam_ung'] ? $specifications['man_hinh']['kinh_cam_ung'] : $updating
                                }}
                            </td>
                        </tr>

                        {{-- Camera sau --}}
                        <tr>
                            <td colspan="2" class="p-0">
                                <div class='detail-specifications-title'>camera sau</div>
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Độ phân giải</td>
                            <td>
                                {{
                                    $specifications['camera_sau']['do_phan_giai'] ? $specifications['camera_sau']['do_phan_giai'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Quay phim</td>
                            <td>
                                @if ($specifications['camera_sau']['quay_phim'][0]['chat_luong'])
                                    @foreach ($specifications['camera_sau']['quay_phim'] as $key)
                                        <div class="mb-5">{{ $key['chat_luong'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Đèn Flash</td>
                            <td>
                                {{
                                    $specifications['camera_sau']['den_flash'] ? $specifications['camera_sau']['den_flash'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Tính năng</td>
                            <td>
                                @if ($specifications['camera_sau']['tinh_nang'][0]['name'])
                                    @foreach ($specifications['camera_sau']['tinh_nang'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>

                        {{-- Camera trước --}}
                        <tr>
                            <td colspan="2" class="p-0">
                                <div class='detail-specifications-title'>camera trước</div>
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Độ phân giải</td>
                            <td>
                                {{
                                    $specifications['camera_truoc']['do_phan_giai'] ? $specifications['camera_truoc']['do_phan_giai'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Tính năng</td>
                            <td>
                                @if ($specifications['camera_truoc']['tinh_nang'][0]['name'])
                                    @foreach ($specifications['camera_truoc']['tinh_nang'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>

                        {{-- hệ điều hành & CPU --}}
                        <tr>
                            <td colspan="2" class="p-0">
                                <div class='detail-specifications-title'>hệ điều hành & cpu</div>
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Hệ điều hành</td>
                            <td>
                                {{
                                    $specifications['HDH_CPU']['HDH'] ? $specifications['HDH_CPU']['HDH'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Chip xử lý (CPU)</td>
                            <td>
                                {{
                                    $specifications['HDH_CPU']['CPU'] ? $specifications['HDH_CPU']['CPU'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Tốc độ CPU</td>
                            <td>
                                {{
                                    $specifications['HDH_CPU']['CPU_speed'] ? $specifications['HDH_CPU']['CPU_speed'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Chip đồ họa (GPU)</td>
                            <td>
                                {{
                                    $specifications['HDH_CPU']['GPU'] ? $specifications['HDH_CPU']['GPU'] : $updating
                                }}
                            </td>
                        </tr>
                        
                        {{-- bộ nhớ & lưu trữ --}}
                        <tr>
                            <td colspan="2" class="p-0">
                                <div class='detail-specifications-title'>bộ nhớ & lưu trữ</div>
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>RAM</td>
                            <td>
                                {{
                                    $specifications['luu_tru']['RAM'] ? $specifications['luu_tru']['RAM'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Bộ nhớ trong</td>
                            <td>
                                {{
                                    $specifications['luu_tru']['bo_nho_trong'] ? $specifications['luu_tru']['bo_nho_trong'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Bộ nhớ còn lại (khả dụng) khoảng</td>
                            <td>
                                {{
                                    $specifications['luu_tru']['bo_nho_con_lai'] ? $specifications['luu_tru']['bo_nho_con_lai'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Thẻ nhớ</td>
                            <td>
                                {{
                                    $specifications['luu_tru']['the_nho'] ? $specifications['luu_tru']['the_nho'] : $updating
                                }}
                            </td>
                        </tr>

                        {{-- kết nối --}}
                        <tr>
                            <td colspan="2" class="p-0">
                                <div class='detail-specifications-title'>kết nối</div>
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Mạng di động</td>
                            <td>
                                {{
                                    $specifications['ket_noi']['mang_mobile'] ? $specifications['ket_noi']['mang_mobile'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>SIM</td>
                            <td>
                                {{
                                    $specifications['ket_noi']['SIM'] ? $specifications['ket_noi']['SIM'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Wifi</td>
                            <td>
                                @if ($specifications['ket_noi']['wifi'][0]['name'])
                                    @foreach ($specifications['ket_noi']['wifi'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>GPS</td>
                            <td>
                                @if ($specifications['ket_noi']['GPS'][0]['name'])
                                    @foreach ($specifications['ket_noi']['GPS'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Bluetooth</td>
                            <td>
                                @if ($specifications['ket_noi']['bluetooth'][0]['name'])
                                    @foreach ($specifications['ket_noi']['bluetooth'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Cổng kết nối/sạc</td>
                            <td>
                                {{
                                    $specifications['ket_noi']['cong_sac'] ? $specifications['ket_noi']['cong_sac'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Jack tai nghe</td>
                            <td>
                                {{
                                    $specifications['ket_noi']['jack_tai_nghe'] ? $specifications['ket_noi']['jack_tai_nghe'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Kêt nối khác</td>
                            <td>
                                @if($specifications['ket_noi']['ket_noi_khac'][0]['name'])
                                    @foreach ($specifications['ket_noi']['ket_noi_khac'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>

                        {{-- Pin & Sạc --}}
                        <tr>
                            <td colspan="2" class="p-0">
                                <div class='detail-specifications-title'>pin & sạc</div>
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Dung lượng pin</td>
                            <td>
                                {{
                                    $specifications['pin']['dung_luong'] ? $specifications['pin']['dung_luong'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Loại pin</td>
                            <td>
                                {{
                                    $specifications['pin']['loai'] ? $specifications['pin']['loai'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Công nghệ pin</td>
                            <td>
                                @if($specifications['pin']['cong_nghe'][0]['name'])
                                    @foreach ($specifications['pin']['cong_nghe'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>

                        {{-- Tiện ích --}}
                        <tr>
                            <td colspan="2" class="p-0">
                                <div class='detail-specifications-title'>tiện ích</div>
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Bảo mật nâng cao</td>
                            <td>
                                @if ($specifications['tien_ich']['bao_mat'][0]['name'])
                                    @foreach ($specifications['tien_ich']['bao_mat'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Tính năng đặc biệt</td>
                            <td>
                                @if ($specifications['tien_ich']['tinh_nang_khac'][0]['name'])
                                    @foreach ($specifications['tien_ich']['tinh_nang_khac'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Ghi âm</td>
                            <td>
                                {{
                                    $specifications['tien_ich']['ghi_am'] ? $specifications['tien_ich']['ghi_am'] : $updating
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Xem phim</td>
                            <td>
                                @if ($specifications['tien_ich']['xem_phim'][0]['name'])
                                    @foreach ($specifications['tien_ich']['xem_phim'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Nghe nhạc</td>
                            <td>
                                @if ($specifications['tien_ich']['nghe_nhac'][0]['name'])
                                    @foreach ($specifications['tien_ich']['nghe_nhac'] as $key)
                                        <div class="mb-5">{{ $key['name'] }}</div>
                                    @endforeach
                                @else
                                    {{$updating}}
                                @endif
                            </td>
                        </tr>

                        {{-- thông tin khác --}}
                        <tr>
                            <td colspan="2" class="p-0">
                                <div class='detail-specifications-title'>thông tin khác</div>
                            </td>
                        </tr>
                        <tr>
                            <td class='w-30 fw-600'>Thời điểm ra mắt</td>
                            <td>
                                {{
                                    $phone['cauhinh']['thong_tin_khac']['thoi_diem_ra_mat'] ? $phone['cauhinh']['thong_tin_khac']['thoi_diem_ra_mat'] : $updating
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
</div>

{{-- modal chỉnh sửa đánh giá --}}
<div class="modal fade" id="edit-evaluate-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="fz-22 fw-600">Đánh giá của tôi</div>
                <div type="button" class="btn-close" data-bs-dismiss="modal"></div>
            </div>
            <div class="modal-body p-0">
                <div class='relative p-30'>
                    <div class='d-flex flex-column'>
                        <input type="hidden" id="evaluate_id" value="0">
                        {{-- sao đánh giá --}}
                        <div class="d-flex align-items-center">
                            <span class="fw-600">Chọn đánh giá của bạn</span>
                            <div class='d-flex align-items-center ml-10'>
                                <i class="edit-star-rating fas fa-star gray-2 fz-20 pr-5" data-id='1'></i>
                                <i class="edit-star-rating fas fa-star gray-2 fz-20 pr-5" data-id='2'></i>
                                <i class="edit-star-rating fas fa-star gray-2 fz-20 pr-5" data-id='3'></i>
                                <i class="edit-star-rating fas fa-star gray-2 fz-20 pr-5" data-id='4'></i>
                                <i class="edit-star-rating fas fa-star gray-2 fz-20 pr-5" data-id='5'></i>

                                <input type="hidden" id='edit_star_rating' name='edit_star_rating' value='0'>
                            </div>
                        </div>
                        
                        {{-- đánh giá --}}
                        <div class='pt-20'>
                            <div class="fw-600 mb-5">Đánh giá</div>
                            <textarea id="edit_evaluate_content" name="edit_evaluate_content" rows="3" maxlength="250"
                            placeholder="Hãy chia sẽ cảm nhận của bạn về sản phẩm (Tối đa 250 ký tự)"></textarea>
                            
                            {{-- ảnh đính kèm & gửi đánh giá --}}
                            <div class='d-flex justify-content-between align-items-center pt-10'>
                                <div class="d-flex">
                                    {{-- input số lượng hình --}}
                                    <input type="hidden" class='edit-qty-img-inp' value='0'>
                                    {{-- input chọn hình --}}
                                    <input class='edit-upload-evaluate-image none-dp' type="file" multiple accept="image/*">
                                    {{-- danh sách input base64 --}}
                                    <div class="edit_array_evaluate_image"></div>
                                    {{-- icon chọn hình --}}
                                    <div id='edit-btn-photo-attached' class='pointer-cs'>
                                        <i class="fas fa-camera"></i>
                                        <span>Ảnh đính kèm</span>
                                        {{-- số lượng hình --}}
                                        <span class='edit-qty-img'></span></span>
                                    </div>
                                </div>
                            </div>
                            {{-- hình xem trước --}}
                            <div class="edit-evaluate-img-div"></div>
                            <div id="edit-send-evaluate-btn" class='main-btn w-100 mt-20'>Cập nhật</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal chọn điện thoại để đánh giá --}}
<div class="modal fade" id="phone-evaluate-modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="fz-22 fw-600">Sản phẩm đã mua</div>
                <div type="button" class="btn-close" data-bs-dismiss="modal"></div>
            </div>
            <div class="modal-body">
                <div class="phone-evaluate-div">
                    <div class="row">
                        @foreach ($haveNotEvaluated as $key)
                            <div class="col-lg-6 mb-20">
                                <div data-id="{{$key['id']}}" class="phone-evaluate">
                                    <img src="{{$url_phone.$key['hinhanh']}}" alt="" width="100px">
                                    <div class="ml-5">
                                        <div class="fw-600">{{$key['tensp']}}</div>
                                        <div>Màu sắc: {{$key['mausac']}}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="pl-20 pr-20 pb-20">
                    <div class="mt-10">
                        <input type="checkbox" id="all_phone_evaluate" name="all_phone_evaluate">
                        <label for="all_phone_evaluate">Chọn tất cả</label>
                    </div>
    
                    <div class="mt-40">
                        <div id="choose-phone-evaluate" class="main-btn none-dp">Tiếp tục</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- delete modal --}}
@include("user.content.modal.xoa-modal")

<div id="toast"></div>

@include('user.content.section.sec-logo')

@stop