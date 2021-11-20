<!DOCTYPE html>
<html lang="en">

@include('admin/header/head')
<?php $user = session('user') ?> 

<body class="admin-bg-color">
    @include("admin/header/header")
    @include("admin.content.section.sec-sidebar")

    {{-- loader --}}
    <div class="loader"><div class="loader-bg"></div><div class="loader-img"><img src="images/logo/LDMobile-logo.png" alt=""></div><div class="spinner-border" role="status"></div></div>

    <div class='backdrop'></div>
    
    <!-- content -->
    <div class="content">
        <!-- title of page -->
        <div class="content-header d-flex justify-content-between">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fz-26 fw-600">@yield('content-title')</div>
            </div>
        </div>

        <div class="m-20">
            @yield("content")
        </div>
    </div>
    
    <div id='btn-scroll-top'><i class="fas fa-chevron-up"></i></div>

    @include("admin/footer/foot")
</body>

</html>
