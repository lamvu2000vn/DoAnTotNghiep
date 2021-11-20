<div class='d-flex flex-column align-items-center'>
    {{-- login with label --}}
    <div class="login-with"></div>
    {{-- facebook --}}
    <a href="{{route('user/facebook-redirect')}}" type="button" class='btn-login-signup-with'>
        <img src="images/icon/facebook-icon.png" alt='facebook'>
        <div class="login-with-label">Facebook</div>
    </a>
    {{-- google --}}
    <a href="{{route('user/google-redirect')}}" type="button" class='btn-login-signup-with'>
        <img src="images/icon/google-icon.png" alt='google'>
        <div class="login-with-label">Google</div>
    </a>
</div>