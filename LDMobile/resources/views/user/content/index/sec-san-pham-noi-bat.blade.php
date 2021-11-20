<section class="user-bg-color pt-50 pb-70">
    <div class="container">
        <div class="index-featured-header">
            <div class="index-featured-title">
                <div class="mr-10">SẢN PHẨM MỚI NHẤT</div>
                <div class="relative">
                    <div class="fire-animation"><i class="fad fa-fire-alt"></i></div>
                </div>
            </div>
            <div class="featured-brand">
                @foreach ($lst_brand as $key)
                    <a href="{{route('user/dien-thoai', ['hang' => $key['brand']])}}" class="index-brand-tag">{{$key['brand']}}</a>    
                @endforeach
                <a href="{{route('user/dien-thoai')}}" class="index-brand-tag">Xem tất cả</a>
            </div>
        </div>
        
        <div class="d-flex flex-wrap">
            @foreach($lst_featured as $key)
            <a href="{{route('user/chi-tiet', ['name' => $key['tensp_url']])}}" class="index-featured-phone">
                {{-- khuyến mãi tag --}}
                @if($key['khuyenmai'] != 0)
                    <div class='shop-promotion-tag'>
                        <span class='shop-promotion-text'>{{ '-'.($key['khuyenmai']*100).'%'}}</span>
                    </div>
                @endif

                {{-- hình ảnh --}}
                <div class="relative">
                    @if ($key['comingSoon'])
                        <div class="coming-soon">HÀNG SẮP VỀ</div>
                    @endif
                    <img src="{{ $url_phone.$key['hinhanh'] }}">
                </div>

                {{-- tên sản phẩm --}}
                <div class='fw-600 black text-center'>{{ $key['tensp'] }}</div>

                <div class="text-center">
                    <div class="pt-10">
                        <span class="red fw-600">{{ number_format($key['giakhuyenmai'], 0, '', '.') }}<sup>đ</sup></span>    
                        @if ($key['khuyenmai'] != 0)
                        <span class="text-strike gray-1 ml-10">{{ number_format($key['gia'], 0, '', '.') }}<sup>đ</sup></span>
                        @endif
                    </div>

                    <div class='d-flex justify-content-center align-items-center pt-10'>
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
    </div>
</section>