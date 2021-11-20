<section class="user-bg-color pt-40">
    <div class="container">
        <div class="index-promotion-title">
            <div class="mr-10">KHUYẾN MÃI HOT</div>
            <div class="relative">
                <div class="fire-animation"><i class="fad fa-fire-alt"></i></div>
            </div>
        </div>

        <div class='relative white-bg'>
            <div id='index-promotion-carousel' class="owl-carousel">
                @foreach($lst_promotion as $key)
                <a href="{{route('user/chi-tiet', ['name' => $key['tensp_url']])}}" class="index-promotion-phone">
                    {{-- hình ảnh --}}
                    <div class="relative">
                        @if ($key['comingSoon'])
                            <div class="coming-soon">HÀNG SẮP VỀ</div>
                        @endif
                        <img src="{{ $url_phone.$key['hinhanh'] }}" class="index-promotion-image">
                    </div>
                    
                    {{-- tên sản phẩm --}}
                    <div class='fw-600 black text-center'>{{ $key['tensp'] }}</div>

                    <div>
                        <div class='index-sale-tag'>{{ 'SALE '.($key['khuyenmai'] * 100).'%' }}</div>

                        <div class="pt-20">
                            <span class="red fw-600">{{ number_format($key['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></span>
                            <span class="text-strike gray-1 ml-10">{{ number_format($key['gia'], 0, '', '.') }}<sup>đ</sup></span>
                        </div>

                        <div class='d-flex align-items-center pt-20'>
                            @if ($key['danhgia']['qty'] != 0)
                                @for ($i = 1; $i <= 5; $i++)
                                    @if($key['danhgia']['star'] >= $i)
                                    <i class="fas fa-star checked"></i>
                                    @else
                                    <i class="fas fa-star uncheck"></i>
                                    @endif
                                @endfor
                                <span class='fz-14 ml-10 black'>{{ $key['danhgia']['qty'].' đánh giá' }}</span>
                            @else
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star uncheck"></i>
                                @endfor
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="d-flex">
                <div id='prev-index-promotion' class="prev-owl-carousel d-flex align-items-center btn-owl-left-style-1"><i class="fas fa-chevron-left fz-26"></i></div>
                <div id='next-index-promotion' class="next-owl-carousel d-flex align-items-center btn-owl-right-style-1"><i class="fas fa-chevron-right fz-26"></i></div>
            </div>
        </div>  
    </div>
</section>