<!doctype html>
<html lang="en">

@include("user.header.head")

<body>
    <!-- Messenger Plugin chat Code -->
    <div id="fb-root"></div>

    <!-- Your Plugin chat code -->
    <div id="fb-customer-chat" class="fb-customerchat">
    </div>

    <script>
      var chatbox = document.getElementById('fb-customer-chat');
      chatbox.setAttribute("page_id", "157073173138532");
      chatbox.setAttribute("attribution", "biz_inbox");

      window.fbAsyncInit = function() {
        FB.init({
          xfbml            : true,
          version          : 'v11.0'
        });
      };

      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
    
    {{-- session --}}
    @if (session('toast_message'))
        <div id="toast-message" data-message="{{session('toast_message')}}"></div>
    @endif
    @if(session('alert_top'))
        <div id="alert-top" data-message="{{session('alert_top')}}"></div>
    @endif
    @if (session('user'))
        <?php $user = session('user') ?>
        <input type="hidden" id="session-user" data-id="{{$user->id}}">
    @endif
    

    {{-- hết hạn phiên đăng nhập fb, gg --}}
    @if (session('login_status'))
        <div class="modal fade" id="invalid-login-modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-60">
                        <div class="text-center">
                            <i class="fal fa-info-circle fz-100 main-color-text"></i>
                            <div class="fz-20 mt-20">Phiên đăng nhập đã hết hạn.</div>
                        </div>
                        <div class="mt-30 d-flex justify-content-between">
                            <div class="close-invalid-login-modal cancel-btn p-10 w-48" data-bs-dismiss="modal">Đóng</div>
                            <a href="{{route('user/dang-nhap')}}" id="delete-btn" class="close-invalid-login-modal checkout-btn p-10 w-48">Đăng nhập</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <div class="loader"><div class="loader-bg"></div><div class="loader-img"><img src="images/logo/LDMobile-logo.png" alt=""></div><div class="spinner-border" role="status"></div></div>

    @include('user.header.header')
    
    @yield("content")

    <div id='btn-scroll-top'><i class="fas fa-chevron-up"></i></div>
  
    @include("user.footer.footer")
    @include("user.footer.footer-link")
</body>
</html>
