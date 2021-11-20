<!DOCTYPE html>
<html lang="en">
    @section("title")Đăng nhập | LDMobile @stop
    @include("user.header.head")
<body>
    <div class="loader"><div class="loader-bg"></div><div class="loader-img"><img src="images/logo/LDMobile-logo.png" alt=""></div><div class="spinner-border" role="status"></div></div>
    <section class='login-signup-sec'>
        <div class='container'>
            <div class='row'>
                <div class='col-lg-5 col-md-8 col-12 mx-auto'>
                    <div class="login-signup-box">
                        @if(session('success_message'))
                            <div class="success-message mb-20">{{ session('success_message') }}</div>
                        @elseif (session('error_message'))
                            <div class="error-message mb-20">{{ session('error_message') }}</div>
                        @endif
                        <h3 class='mb-30 fw-600'>Đăng nhập</h3>
        
                        <form id="login-form" action="{{route('user/login')}}" method="POST">
                            @csrf
                            <!-- sdt -->
                            <div class='mb-3'>
                                <input type='text' id="login_tel" name="login_tel" placeholder='Số điện thoại' maxlength="10" autofocus>
                            </div>
        
                            <!-- mật khẩu -->
                            <div class='mb-3'>
                                <input type='password' id="login_pw" name="login_pw" placeholder='Mật khẩu'>
                            </div>
        
                            <!-- lưu đăng nhập & quên mật khẩu -->
                            <div class="d-flex justify-content-between align-items-center mb-20">
                                <div>
                                    <input type="checkbox" id='remember' name="remember" value="0">
                                    <label for="remember" class='form-check-label'>Ghi nhớ đăng nhập</label>
                                </div>
                                <a href="{{route('user/khoi-phuc-tai-khoan')}}">Quên mật khẩu?</a>
                            </div>
        
                            <!-- button đăng nhặp -->
                            <div id="btn-login" class='main-btn w-100 mb-10'>Đăng nhập</div>
                            <div class="d-flex justify-content-center mb-30">Chưa có tài khoản? <a href={{route('user/dang-ky')}} class="ml-5">Đăng ký</a></div>
        
                            <!-- đăng nhập khác -->
                            <div class='mb-20'>
                                @include("user.content.components.dangnhap-dangky.hinh-thuc-dang-nhap-khac")
                            </div>
    
                            {{-- về trang chủ --}}
                            <a href={{route('user/index')}} class="d-flex align-items-center"><i class="far fa-chevron-left mr-10 fz-14"></i>Về trang chủ</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include("user.footer.footer-link")
</body>
</html>
