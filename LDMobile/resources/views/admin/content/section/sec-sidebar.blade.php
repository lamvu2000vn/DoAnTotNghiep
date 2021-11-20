<!-- Sidebar -->
<div class="sidebar custom-scrollbar">
    <!-- link -->
    <div class="sidebar-menu">
        <a href={{route('admin/dashboard')}} class="sidebar-link @yield('sidebar-dashboard')">
            <div class="sidebar-icon"><i class="fas fa-home"></i></div>
            <div class="sidebar-content">Bảng điều khiển</div>
        </a>
 
        <a href={{route('hinhanh.index')}} class="sidebar-link @yield('sidebar-image')">
            <div class="sidebar-icon"><i class="fas fa-image"></i></div>
            <div class="sidebar-content">Hình ảnh</div>
        </a>

        <a href={{route('banner.index')}} class="sidebar-link @yield('sidebar-banner')">
            <div class="sidebar-icon"><i class="fab fa-adversal"></i></div>
            <div class="sidebar-content">Banner</div>
        </a>

        <a href={{route('slideshow.index')}} class="sidebar-link @yield('sidebar-slideshow')">
            <div class="sidebar-icon"><i class="fas fa-images"></i></div>
            <div class="sidebar-content">Slideshow</div>
        </a>

        <a href={{route('slideshow-msp.index')}} class="sidebar-link @yield('sidebar-slideshow-msp')">
            <div class="sidebar-icon"><i class="fas fa-images"></i></div>
            <div class="sidebar-content">Slideshow MSP</div>
        </a>

        <a href={{route('mausanpham.index')}} class="sidebar-link @yield('sidebar-product-model')">
            <div class="sidebar-icon"><i class="fas fa-th-list"></i></div>
            <div class="sidebar-content">Mẫu sản phẩm</div>
        </a>

        <a href={{route('sanpham.index')}} class="sidebar-link @yield('sidebar-product')">
            <div class="sidebar-icon"><i class="fas fa-mobile-alt"></i></div>
            <div class="sidebar-content">Sản phẩm</div>
        </a>

        <a href={{route('imei.index')}} class="sidebar-link @yield('sidebar-imei')">
            <div class="sidebar-icon"><i class="fas fa-barcode"></i></div>
            <div class="sidebar-content">IMEI</div>
        </a>

        <a href={{route('kho.index')}} class="sidebar-link @yield('sidebar-warehouse')">
            <div class="sidebar-icon"><i class="fad fa-warehouse"></i></div>
            <div class="sidebar-content">Kho</div>
        </a>

        <a href={{route('chinhanh.index')}} class="sidebar-link @yield('sidebar-branch')">
            <div class="sidebar-icon"><i class="far fa-code-branch"></i></div>
            <div class="sidebar-content">Chi nhánh</div>
        </a>

        <a href={{route('tinhthanh.index')}} class="sidebar-link @yield('sidebar-province')">
            <div class="sidebar-icon"><i class="fad fa-city"></i></div>
            <div class="sidebar-content">Tỉnh thành</div>
        </a>

        <a href={{route('voucher.index')}} class="sidebar-link @yield('sidebar-voucher')">
            <div class="sidebar-icon"><i class="fas fa-ticket-alt"></i></div>
            <div class="sidebar-content">Voucher</div>
        </a>

        <a href={{route('nhacungcap.index')}} class="sidebar-link @yield('sidebar-supplier')">
            <div class="sidebar-icon"><i class="fas fa-building"></i></div>
            <div class="sidebar-content">Nhà cung cấp</div>
        </a>

        <a href={{route('danhgia.index')}} class="sidebar-link @yield('sidebar-evaluate')">
            <div class="sidebar-icon"><i class="fas fa-star"></i></div>
            <div class="sidebar-content">Đánh giá</div>
        </a>

        <a href={{route('khuyenmai.index')}} class="sidebar-link @yield('sidebar-promotion')">
            <div class="sidebar-icon"><i class="fas fa-gift"></i></div>
            <div class="sidebar-content">Khuyến mãi</div>
        </a>

        <a href={{route('donhang.index')}} class="sidebar-link @yield('sidebar-order')">
            <div class="sidebar-icon"><i class="fas fa-file-alt"></i></div>
            <div class="sidebar-content">Đơn hàng</div>
        </a>

        <a href={{route('baohanh.index')}} class="sidebar-link @yield('sidebar-warranty')">
            <div class="sidebar-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="sidebar-content">Bảo hành</div>
        </a>

        <a href={{route('taikhoan.index')}} class="sidebar-link @yield('sidebar-account')">
            <div class="sidebar-icon"><i class="fas fa-user"></i></div>
            <div class="sidebar-content">Tài khoản</div>
        </a>

        <a href={{route('giohang.index')}} class="sidebar-link @yield('sidebar-cart')">
            <div class="sidebar-icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="sidebar-content">Giỏ Hàng</div>
        </a>

        <a href={{route('spyeuthich.index')}} class="sidebar-link @yield('sidebar-wishlist')">
            <div class="sidebar-icon"><i class="fas fa-heart"></i></div>
            <div class="sidebar-content">Sản Phẩm Yêu Thích</div>
        </a>

        <a href={{route('taikhoandiachi.index')}} class="sidebar-link @yield('sidebar-account-address')">
            <div class="sidebar-icon"><i class="fas fa-map-marker"></i></div>
            <div class="sidebar-content">Tài Khoản Địa Chỉ</div>
        </a>

        <a href={{route('thongbao.index')}} class="sidebar-link @yield('sidebar-notification')">
            <div class="sidebar-icon"><i class="fas fa-bell"></i></div>
            <div class="sidebar-content">Thông Báo</div>
        </a>

        <a href={{route('taikhoanvoucher.index')}} class="sidebar-link @yield('sidebar-account-voucher')">
            <div class="sidebar-icon"><i class="fas fa-ticket-alt"></i></div>
            <div class="sidebar-content">Tài Khoản Voucher</div>
        </a>
    </div>
</div>