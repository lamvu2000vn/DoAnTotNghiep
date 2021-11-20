
<div class='backdrop'></div>

<div class="head-bg">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            {{-- Logo --}}
            <div class="w-7">
                <a href="{{route('user/index')}}" class='head-brand'>
                    <img src="images/logo/LDMobile-logo.png" alt="Logo">
                </a>
            </div>

            {{-- link --}}
            <div class="w-93">
                <div class="head-items">
                    <div class="d-flex align-items-center justify-content-lg-between">
                        {{-- điện thoại --}}
                        <a href="{{route('user/dien-thoai')}}" class='head-item pt-15 pb-15 white'>Điện thoại</a>
                        
                        {{-- tìm kiếm & giỏ hàng --}}
                        <div class="head-item">
                            {{-- tìm kiếm --}}
                            <div class='relative'>
                                <div class="head-input-grp">
                                    <input id="head-search-input" type="text" class='head-search-input' placeholder="Tìm kiếm">
                                    <span class='input-icon-right'><i class="fal fa-search"></i></span>
                                    
                                    {{-- danh sách kết quả --}}
                                    <div class="head-search-result">
                                    </div>
                                    <div class="search-loading">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="spinner-border search-loading-icon" role="status"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
            
                            {{-- giỏ hàng --}}
                            <div class="relative">
                                <a href="{{route('user/gio-hang')}}" class='head-cart ml-20'>
                                    <i class="fas fa-shopping-cart fz-32"></i>
                                    @if (session('user') && $data['cartQty'] != 0)
                                        <span class='head-qty-cart'>{{ $data['cartQty'] }}</span>
                                    @else
                                        <span class='head-qty-cart none-dp'>0</span>
                                    @endif
                                </a>
                                <div id="add-cart-success"></div>
                            </div>
                            
                        </div>
            
                        {{-- tài khoản --}}
                        @if (session('user'))
                            <div class='head-item mr-20'>
                                <div class='d-flex'>
                                    <?php $user = session('user'); ?>
                                    <img id="avt-header" src="{{ $user->htdn == 'normal' ? $url_user.$user->anhdaidien : $user->anhdaidien }}" alt="avatar" class="header-avatar">
                                    
                                    <div class='d-flex flex-column justify-content-end'>
                                        <i class='white fz-14'>Xin Chào!</i>
                                        <div class='white head-account'>
                                            <div>{{ session('user')->hoten }}</div>
                                            <i class="fas fa-caret-down ml-5"></i>
                                            <div class='head-account-dropdown'>
                                                {{-- xem tài khoản --}}
                                                <a href="{{route('user/tai-khoan')}}" class='head-account-option'><i class="fas fa-user mr-10"></i>Xem Tài khoản</a>
                                                {{-- xem thông báo --}}
                                                <a href="{{route('user/tai-khoan-thong-bao')}}" class='head-account-option'>
                                                    <span><i class="fas fa-bell mr-10"></i>Thông báo</span>
                                                    @if ($data['notSeen'] != 0)
                                                        <div class='not-seen-qty number-badge ml-10'>{{$data['notSeen']}}</div>
                                                    @endif
                                                </a>
                                                {{-- xem đơn hàng --}}
                                                <a href="{{route('user/tai-khoan-don-hang')}}" class='head-account-option'>
                                                    <span><i class="fas fa-box mr-10"></i>Đơn hàng của tôi</span>
                                                    @if ($data['processing'] != 0)
                                                        <span class='processing-qty number-badge ml-10'>{{$data['processing']}}</span>
                                                    @endif
                                                </a>
                                                {{-- đăng xuất --}}
                                                <a href="{{route('user/logout')}}" class='head-account-option'><i class="far fa-power-off mr-10"></i>Đăng xuất</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- đăng nhập/đăng ký --}}
                            <div class="head-item">
                                <a href="{{route('user/dang-nhap')}}" class='head-item white'><i class="fas fa-user mr-10"></i>Đăng nhập</a>
                                <span class="ml-10 mr-10 fz-26 white">|</span>
                                <a href="{{route('user/dang-ky')}}" class='head-btn-signup'>Đăng ký</a>
                            </div>
                        @endif
                    </div>
                </div>     

                <div id='show-offcanvas' expanded="false"><i class="fas fa-bars white fz-30"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="head-offcanvas-box">
    {{-- nút đóng  --}}
    <div class='d-flex justify-content-end p-10'><div id='btn-close-offcanvas'><i class="fas fa-times fz-30 gray-1"></i></div></div>

    <div class='row'>
        <div class='col-lg-10 col-12'>
            <div class='d-flex flex-column justify-content-center p-20'>
                <a href="{{route('user/index')}}"><img src="images/logo/LDMobile-logo.png" class='head-offcanvas-img'></a><hr>

                {{-- tài khoản --}}
                @if (session('user'))
                    <div class="relative">
                        <div class="d-flex justify-content-center">
                            <div class="offcanvas-account">
                                <img src="{{$user->htdn == 'normal' ? $url_user.$user->anhdaidien : $user->anhdaidien}}" alt="user avatar" class="offcanvas-avatar">
                                <div class="offcanvas-account-name">
                                    <div id="offcanvas-name" class="fw-600 mr-5">{{$user->hoten}}</div>
                                    <i class="fas fa-caret-down"></i>
                                </div>
                            </div>
                            {{-- options --}}
                            <div class="offcanvas-account-option">
                                <a href="{{route('user/tai-khoan')}}" class="offcanvas-account-single-option"><i class="fas fa-user mr-10"></i>Xem Tài khoản</a>
                                <a href="{{route('user/tai-khoan-thong-bao')}}" class="offcanvas-account-single-option">
                                    <span><i class="fas fa-bell mr-10"></i>Thông báo</span>
                                    @if ($data['notSeen'] != 0)
                                        <div class='not-seen-qty number-badge ml-10'>{{$data['notSeen']}}</div>
                                    @endif
                                </a>
                                <a href="{{route('user/tai-khoan-don-hang')}}" class="offcanvas-account-single-option">
                                    <span><i class="fas fa-box mr-10"></i>Đơn hàng của tôi</span>
                                    @if ($data['processing'] != 0)
                                        <span class='processing-qty number-badge ml-10'>{{$data['processing']}}</span>
                                    @endif
                                </a>
                                <a href="{{route('user/logout')}}" class="offcanvas-account-single-option"><i class="far fa-power-off mr-10"></i>Đăng xuất</a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class='d-flex justify-content-center align-items-center'>
                        <i class="fas fa-user mr-10"></i>
                        <a href="{{route('user/dang-nhap')}}" class='black fw-600'>Đăng nhập</a>
                        <b class='ml-10 mr-10'>|</b>
                        <a href="{{route('user/dang-ky')}}" class='main-btn'>Đăng ký</a>
                    </div>
                @endif
                <hr>
                
                {{-- tìm kiếm --}}
                <div class='d-flex justify-content-center mb-20 relative'>
                    <div class="head-input-grp">
                        <input type="text" class='head-search-input border' placeholder="Tìm kiếm">
                        <span class='input-icon-right'><i class="fal fa-search"></i></span>

                        {{-- danh sách kết quả --}}
                        <div class="head-search-result">
                        </div>
                        <div class="search-loading border">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="spinner-border search-loading-icon" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
            
                {{-- điện thoại --}}
                <div class='head-drop-2 head-offcanvas-item text-center pb-20'>
                    <a href="{{route('user/dien-thoai')}}" class="fw-600">Điện thoại</a>
                </div>

                {{-- giỏ hàng --}}
                <div class='head-offcanvas-item pb-20'>
                    <div class='d-flex align-items-center justify-content-center'>
                        <a href="{{route('user/gio-hang')}}" class="fw-600">Giỏ hàng</a>
                    </div>
                </div><hr>

                {{-- gọi tư vấn --}}
                <div class='d-flex justify-content-start'>
                    <div class='d-flex flex-column pt-20'>
                        <b>Gọi tư vấn:</b>
                        <div class='d-flex flex-column mt-10 ml-30'>
                            <span><i class="fas fa-phone mr-5"></i>077 9792000</span>
                            <span><i class="fas fa-phone mr-5"></i>038 4151501</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    