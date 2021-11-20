<!DOCTYPE html>
<html lang="en">
@section("title")Thông báo | LDMobile @stop
@include("user.header.head")

<body class="notification-bg-c">
    <div class="loader"><div class="loader-bg"></div><div class="loader-img"><img src="images/logo/LDMobile-logo.png" alt=""></div><div class="spinner-border" role="status"></div></div>

    <div class="notification-bg">
        <div class="notification-header">Thông báo</div>
        <div class="notification-body">
            <div class="notification-message">{{session('message') ? session('message') : ''}}</div>
            <div class="notification-contact">
                <div class="fw-600 mb-5">Gọi tư vấn</div>
                <div class="gray-1">
                    <i class="fas fa-phone mr-5"></i>077 979 2000 | 038 415 1501
                </div>
            </div>
        </div>
        <div class="notification-footer">
            <a href="{{route('user/index')}}" class="back-home-btn"><i class="fas fa-home mr-10"></i>Về trang chủ</a>
        </div>
    </div>

    @include("user.footer.footer-link")
</body>
</html>