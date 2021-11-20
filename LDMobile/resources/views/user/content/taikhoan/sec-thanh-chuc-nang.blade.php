<div class='d-flex flex-column mb-20'>
    {{-- xem tài khoản --}}
    <a href="{{route('user/tai-khoan')}}" class='account-sidebar-tag @yield('acc-info-active')'>
        <i class="fas fa-user mr-20"></i>Thông tin tài khoản
    </a>
    {{-- xem thông báo --}}
    <a href="{{route('user/tai-khoan-thong-bao')}}" class='account-sidebar-tag @yield('acc-noti-active')'>
        <i class="fas fa-bell mr-20"></i>Thông báo
        @if ($data['notSeen'] != 0)
            <div class='not-seen-qty number-badge ml-10'>{{$data['notSeen']}}</div>
        @endif
    </a>
    {{-- xem đơn hàng --}}
    <a href="{{route('user/tai-khoan-don-hang')}}" class='account-sidebar-tag @yield('acc-order-active')'>
        <i class="fas fa-box mr-20"></i>Quản lý đơn hàng
        @if ($data['processing'] != 0)
            <div class='processing-qty number-badge ml-10'>{{$data['processing']}}</div>
        @endif
    </a>
    {{-- xem sổ địa chỉ --}}
    <a href="{{route('user/tai-khoan-dia-chi')}}" class='account-sidebar-tag @yield('acc-address-active')'>
        <i class="fas fa-map-marker-alt mr-20"></i>Sổ địa chỉ
    </a>
    {{-- sản phẩm yêu thích --}}
    <a href="{{route('user/tai-khoan-yeu-thich')}}" class='account-sidebar-tag @yield('acc-favorite-active')'>
        <i class="fas fa-heart mr-20"></i>Sản phẩm yêu thích
    </a>
    {{-- mã giảm gía --}}
    <a href="{{route('user/tai-khoan-voucher')}}" class='account-sidebar-tag @yield('acc-voucher-active')'>
        <i class="fas fa-ticket-alt mr-20"></i>Mã giảm giá
    </a>
    <hr>
    {{-- đăng xuất --}}
    <a href="{{route('user/logout')}}" class='account-sidebar-tag red'>
        <i class="far fa-power-off mr-20"></i>Đăng xuất
    </a>
</div>