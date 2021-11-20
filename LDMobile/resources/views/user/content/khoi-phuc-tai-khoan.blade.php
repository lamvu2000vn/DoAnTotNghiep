<!DOCTYPE html>
<html lang="en">
    @section("title")Khôi phục | LDMobile @stop
    @include("user.header.head")
<body>
    <div class="loader"><div class="loader-bg"></div><div class="loader-img"><img src="images/logo/LDMobile-logo.png" alt=""></div><div class="spinner-border" role="status"></div></div>
    <section class='login-signup-sec'>
        <div class='container'>
            <div class='row'>
                <div class="col-md-5 col-sm-10 col-10 mx-auto box-shadow login-signup-box">
                    {{-- session message --}}
                    @if(session('error_message'))
                        <div class="error-message mb-20">{{ session('error_message') }}</div>
                    @endif
                    <form id="forget-form" action="{{route('user/recover-account')}}" method="POST">
                        @csrf
                        {{-- enter phone number --}}
                        <div id='enter-information'>
                            {{-- title --}}
                            <div class="mb-30">
                                <h2 class="fw-600">Khôi phục tài khoản</h2>
                            </div>

                            {{-- SDT --}}
                            <div class="mb-20">
                                <div class="mb-5">Nhập Số điện thoại đã đăng ký để khôi phục tài khoản</div>
                                <input type='text' id='forget_tel' name="forget_tel" maxlength="10" placeholder='Số điện thoại' required>
                            </div>

                            <div class="mb-20">
                                <div id="recaptcha-container"></div>
                            </div>

                            {{-- nút tiếp tục --}}
                            <div type='button' id='forget-step-1' class='main-btn w-100'>Tiếp tục</div>
                            <div class="d-flex justify-content-center mt-10">Đã có tài khoản? <a href="{{route('user/dang-nhap')}}" class="ml-10">Đăng nhập</a></div>
    
                            {{-- về trang chủ --}}
                            <div class="mt-30">
                                <a href={{route('user/index')}} class="d-flex align-items-center"><i class="far fa-chevron-left mr-10 fz-14"></i>Về trang chủ</a>
                            </div>  
                        </div>

                        {{-- enter Verification --}}
                        <div id="enter-verify-code" class="none-dp">
                            {{-- nút quay lại --}}
                            <div class="mb-30">
                                <span type="button" id="back-to-enter-tel"><i class="far fa-chevron-left fz-26"></i></span>
                            </div>

                            <div class="mb-30">
                                <h3 class="fw-600">Nhập mã xác thực</h3>
                                <div>Mã xác thực đã được gửi đến số điện thoại <b id="tel-confirm"></b></div>
                            </div>
                            
                            <div class="mb-30">
                                <input type="text" id="verify-code-inp" maxlength="6" placeholder="Ví dụ: 123456" class="text-center">
                            </div>

                            <div type="button" id="forget-step-2" class="main-btn">Tiếp tục</div>
                        </div>

                        {{-- enter password --}}
                        <div id='enter-password' class="none-dp">
                            <div class="mb-30">
                                <h3 class="fw-600">Mật khẩu mới</h3>
                            </div>
                            {{-- mật khẩu --}}
                            <div class="mb-3">
                                <label for="forget_pw" class="fw-600 form-label">Mật khẩu mới</label>
                                <input type='password' id="forget_pw" name="forget_pw">
                            </div>

                            {{-- nhập lại mật khẩu --}}
                            <div class="mb-3">
                                <label for="forget_re_pw" class="fw-600 form-label">Nhập lại mật khẩu</label>
                                <input type='password' id="forget_re_pw" name="forget_re_pw">
                            </div>

                            <div type='button' id='forget-step-3' class="main-btn">Khôi phục</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @include("user.footer.footer-link")
</body>
</html>