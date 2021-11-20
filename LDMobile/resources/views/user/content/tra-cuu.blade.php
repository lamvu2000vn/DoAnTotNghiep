@extends("user.layout")
@section("title")Tra cứu | LDMobile @stop
@section("content")

@section("breadcrumb")
    <a href="{{route('user/tra-cuu')}}" class="bc-item active">Tra cứu</a>
@stop
@include("user.content.section.sec-thanh-dieu-huong")

<section class="pt-80 pb-100">
    <div class="container">
        {{-- check imei --}}
        <div id='check-imei' class="col-lg-6 col-12 mx-auto">
            <div class="imei-wrapper">
                <h3 class="mb-20">Nhập số IMEI điện thoại</h3>
                <div class="mb-3">
                    <input type="text" id="imei-inp" class="text-center" maxlength="15" autofocus>
                </div>
                <div id='btn-check-imei' class="main-btn">Tra cứu</div>
            </div>           
        </div>

        <div id="valid-imei"></div>
    </div>
</section>

@include('user.content.section.sec-logo')

@stop