<!DOCTYPE html>
<html lang="en">
@section("title")Liên hệ | LDMobile @stop
@include('user.header.head')
@include('user.header.header')
<body>
    <div class="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-10 mx-auto box-shadow p-40">
                    <div class="row">
                        {{-- title --}}
                        <div class="col-12">
                            <div class="fz-50 fw-600 main-color-text text-center mb-40">Liên hệ</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12 d-flex flex-column justify-content-between">
                                {{-- hình minh họa --}}
                                <img src="images/customer-service-cartoon.png" alt="" class="center-img pb-40">
                        
                                {{-- icon --}}
                                <div class="d-flex fz-20">
                                    <div class="main-color-text"><i class="fab fa-facebook-f ml-10 mr-10"></i></div>
                                    <div class="main-color-text"><i class="fab fa-instagram ml-10 mr-10"></i></div>
                                    <div class="main-color-text"><i class="fab fa-twitter ml-10 mr-10"></i></div>
                                    <div class="main-color-text"><i class="fab fa-pinterest-p ml-10 mr-10"></i></div>
                                    <div class="main-color-text"><i class="fab fa-whatsapp ml-10 mr-10"></i></div>
                                </div>
                            </div>
                            {{-- form --}}
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="mb-20">
                                    <div class="fw-600 fz-20 mb-10">Tổng đài hỗ trợ</div>
                                    <ul>
                                        <li>Gọi mua: 077 979 2000</li>
                                        <li>Kỹ thuật: 038 415 1501</li>
                                    </ul>
                                </div>
                                <div class="mb-20">
                                    <div class="fw-600 fz-20 mb-10">Chi nhánh</div>
                                    <ul>
                                        @foreach ($lst_branch as $branch)
                                            <li>{{$branch->diachi}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </d>

    @include('user.footer.footer-link')
</body>
</html>