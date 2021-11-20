{{-- logo --}}
<a href={{route('admin/dashboard')}} class='header-logo' aria-expanded="false">
    <img src="images/logo/LDMobile-logo.png" alt="">
</a>
{{-- navigation --}}
<div class="navigation">
    <!-- logo -->
    <a href={{route('admin/dashboard')}} class="d-flex align-items-center">
        <div class="fz-22 ml-10 white">ADMIN</div>
    </a>

    <!-- account -->
    <div class="relative d-flex justify-content-end align-items-center pl-100 pr-20">
        <div class="d-flex align-items-center">
            {{-- avatar --}}
            <img src="{{$url_user.$user->anhdaidien}}" id="avatarHeaderUser" alt="" width="40px" class="circle-img mr-10">
            <!-- name -->
            <div id='btn-expand-account' class="d-flex align-items-center">
                <div class="pointer-cs white" id="nameHeader">{{$user->hoten}}<i class="far fa-chevron-down ml-10 fz-14"></i></div>
            </div>
        </div>

        {{-- account option --}}
        <div class="account-option">
            <a href="{{route('admin/dashboard')}}" class="single-option"><i class="fas fa-home mr-10"></i>Bảng điều khiển</a>
            <a href="{{route('taikhoan.index')}}" class="single-option"><i class="fas fa-user-alt mr-10"></i>Xem tài khoản</a>
            <a href="{{route('user/logout')}}" class="single-option"><i class="far fa-power-off mr-10"></i>Đăng xuất</a>
        </div>
    </div>
</div>