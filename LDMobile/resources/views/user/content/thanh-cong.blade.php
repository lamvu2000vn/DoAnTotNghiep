<!doctype html>
<html lang="en">
    @section("title")Thành công | LDMobile @stop
@include("user.header.head")
<body>
    <section class="success-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-7 col-sm-10 col-10 mx-auto box-shadow white-bg" style="border-radius: 20px">
                    <div class="d-flex flex-column">
                        <img id='success-img' src="images/icon/check.gif" alt="" class="center-img">
                    </div>
                    <div id='success-checkout'>
                        <div class="text-center fw-600 fz-20">Cảm ơn bạn đã mua hàng tại LDMobile</div>
                        <div class="row p-50">
                            <div class="col-md-5">
                                <div class="fz-14">Mã đơn hàng của bạn</div>
                                <div class="d-flex">
                                    <div class="order-id">#{{$order->id}}</div>
                                </div>
                                <div class="fz-14">Bạn có thể xem lại <a href={{route('user/tai-khoan-don-hang')}}>Đơn hàng của tôi</a></div>
                            </div>
                            <div class="col-md-2 d-flex justify-content-center">
                                <div class="vl"></div>
                            </div>
                            <div class="col-md-5 d-flex justify-content-center align-items-center">
                                <a href={{route('user/dien-thoai')}} class="main-btn w-100">Tiếp tục mua sắm</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include("user.footer.footer-link")
</body>
</html>