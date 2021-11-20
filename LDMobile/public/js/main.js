$(window).on('load', function(){
    $('.loader').fadeOut();
});

$(function(){
    if(iOS()){
        showIOSScreen();
        return
    }

    const page = window.location.pathname.split('/')[1];
    const childPage = window.location.pathname.split('/')[2];
    const navigation = performance.getEntriesByType("navigation")[0].type;

    const X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content')
    const BYTE = 1024
    const MAX_SIZE_IMAGE = 5 // 5 MB
    const maxSizeImagMessage = 'Dung lượng tối đa cho hình ảnh là 5 MB'
    const errorMessage = 'Đã có lỗi xảy ra. Vui lòng thử lại'

    let loadMoreFlag = false;
    let storageFlag = false;
    let loadMoreRow = 0;
    let timer = null;
    let checkoutTimer;
    let removeQueueFlag = true;

    let xhrProvince = null
    let xhrDistrict = null
    let xhrWard = null

    // API URL
    const ADDRESS_URL = 'https://provinces.open-api.vn/api/'

    /*==============================================================
                Xóa kí tự "#_=_" khi đăng nhập facebook
    ================================================================*/

    if (window.location.hash == '#_=_') {
        window.location.hash = ''; // for older browsers, leaves a # behind
        history.pushState('', document.title, window.location.pathname); // nice and clean
    }

    /*==============================================================*/

    // welcome back
    welcomeBack()

    // xử lý cuộn trang
    $(window).scroll(function(e){
        var scrollTop = $(window).scrollTop();
        var docHeight = $(document).height();
        var winHeight = $(window).height();
        var scrollPercent = (scrollTop) / (docHeight - winHeight);
        var scrollPercentRounded = Math.round(scrollPercent*100);

        if(scrollPercentRounded >= 20){
            $('#btn-scroll-top').css({
                '-ms-transform' : 'translateY(0)',
                'transform' : 'translateY(0)',
            });
        } else {
            $('#btn-scroll-top').css({ 
                '-ms-transform' : 'translateY(100px)',
                'transform' : 'translateY(100px)',
            });
        }

        // loadmore data
        if(page === 'dienthoai' && childPage === undefined){
            if(loadMoreFlag == false){
                if(scrollPercentRounded >= 60){
                    loadMoreFlag = true;

                    if($('#lst_product').attr('data-done') !== 'done'){
                        var row = parseInt($('#lst_product').attr('data-row'));
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'ajax-load-more',
                            type: 'POST',
                            data: {'page': page, 'row': row, 'limit': 10},
                            success: function(data){
                                loadMoreFlag = false;
                                if(data == 'done'){
                                    $('#lst_product').attr('data-done', 'done');
                                } else {
                                    let html = renderPhoneCard(data)

                                    $('#lst_product').append(html);
                                    $('#lst_product').attr('data-row', (row + 10));
                                    loadMoreRow += 10;

                                    sessionStorage.removeItem('loadMoreRow');
                                    sessionStorage.setItem('loadMoreRow', loadMoreRow);
                                    console.log(sessionStorage.getItem('loadMoreRow'));
                                }
                            }
                        });
                    }
                }
            }

            if(storageFlag == true){
                sessionStorage.removeItem('scrollPosition');
                sessionStorage.setItem('scrollPosition', scrollTop);
            }
        } else if(page === 'taikhoan' && childPage === 'thongbao'){
            if(loadMoreFlag == false){
                if(scrollPercentRounded >= 50){
                    loadMoreFlag = true;

                    if($('#lst_noti').attr('data-done') != 'done'){
                        var row = parseInt($('#lst_noti').children().length);

                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'ajax-load-more',
                            type: 'POST',
                            data: {'page': childPage, 'row': row, 'limit': 10},
                            success: function(data){
                                loadMoreFlag = false;
                                if(data == 'done'){
                                    $('#lst_noti').attr('data-done', 'done');
                                } else {
                                    const html = renderNotification(data)
                                    $('#lst_noti').append(html);
                                }
                            }
                        });
                    }
                }
            }
        }
    });

    // xử lý cuộn lên đầu trang
    $('#btn-scroll-top').on('click', function(){
        $(window).scrollTop(0);
    });

    /*==============================================================
                            Toast + alert top
    ================================================================*/

    // thông báo toast lưu trong session
    if(sessionStorage.getItem('toast-message')){
        const message = sessionStorage.getItem('toast-message');
        sessionStorage.removeItem('toast-message');
        showToast(message);
    }

    // thông báo alert top lưu trong session
    if(sessionStorage.getItem('alert-top-message')){
        let message = sessionStorage.getItem('alert-top-message');
        sessionStorage.removeItem('alert-top-message');
        showAlertTop(message);
    }

    // toast thông báo
    if($('#toast-message').length){
        const message = $('#toast-message').attr('data-message')
        $('#toast-message').remove();
        showToast(message);
    }

    // alert top session
    if($('#alert-top').length){
        var message = $('#alert-top').data('message');
        showAlertTop(message);
    }

    // đóng alert top
    $(document).on('click', '.close-alert-top', function(){
        closeAlertTop();
    });
    $(document).on('click', '.close-alert-top-icon', function(){
        closeAlertTop();
    });

    /*==============================================================
                                Hàng đợi
    ================================================================*/

    // cập nhật & khôi phục hàng đợi
    if(page === 'thanhtoan' || page === 'diachigiaohang'){
        const id_tk = $('#session-user').data('id')
        
        window.addEventListener('beforeunload', function() {
            if(removeQueueFlag) {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
                    url: 'ajax-update-queue-status',
                    type: 'POST',
                    data: {id_tk}
                });
            }
        })

        if(navigation === 'reload') {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: 'ajax-recover-queue-status',
                type: 'POST',
                data: {id_tk},
                success: function(data){
                    if(data.status === 'another platform') {
                        // hủy cờ hàng đợi trong session
                        sessionStorage.removeItem('checkoutQueueFlag')
                        // thông báo alert top
                        sessionStorage.setItem('alert-top-message', 'Đơn hàng đang được thanh toán trên ứng dụng di động')

                        window.location.href = '/giohang'
                        return
                    } else {
                        console.log('recover queue');
                    }
                }
            })
        }
    } else {
        isRemoveQueue()
    }

    // slide logo các hãng
    $('#logo-carousel').owlCarousel({
        items:5,
        margin: 40,
        loop:true,
        dots:false,
        nav: false,
        autoplay:true,
        autoplayTimeout:1500,
        autoplayHoverPause:true,
        smartSpeed: 1000,
        responsive: {
            0: {
                items: 3
            },
            600: {
                items: 3
            },
            1000: {
                items: 4
            },
            1200: {
                items: 5
            }
        }
    });

    // hết hạn đăng nhập
    if($('#invalid-login-modal').length){
        $('#invalid-login-modal').modal('show');
    }

    // đóng modal, xóa session login status
    $('.close-invalid-login-modal').off('click').click(function(){
        $.ajax({
            headers:{
                'X-CSRF-TOKEN': X_CSRF_TOKEN
            },
            url: 'ajax-forget-login-status-session'
        });
    });

    /*============================================================================================================
                                                            Header
    ==============================================================================================================*/
    // thanh tìm kiếm
    $('.head-search-input').focus(function(){
        $('.backdrop').fadeIn();
        if($('.head-search-input').val() != ''){
            $('.head-search-result').css('display', 'block');
        }
    });
    $('.head-search-input').focusout(function(){
        setTimeout(() => {
            $('.backdrop').fadeOut();
            $('.head-search-result').css('display', 'none');
        }, 400);
    });

    let enterKey = false;
    let xhr = null;

    $('.head-search-input').keypress(function(e){
        if(e.keyCode == '13'){
            const keyword = removeAccents($(this).val().toLowerCase().trim())
            submitSearch(keyword);
        }
    });
    $('.head-search-input').keyup(function(e){
        clearTimeout(timer);
        timer = setTimeout(() =>{
            var val = removeAccents($(this).val().toLowerCase().trim());
            if(val == ''){
                $('.head-search-result').hide();
                return;
            }

            xhr = $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/ajax-search-phone',
                    type: 'POST',
                    cache: false,
                    data: {'str': val},
                    success:function(data){
                        if(data.phoneList.length === 0){
                            $('.search-loading').hide()
                            return;
                        }
                        
                        let phone = '';
                        let infoComponent = ''

                        $.each(data.phoneList, function(i, val){
                            // ngừng kinh doanh
                            if(val.modelStatus === false) {
                                infoComponent =
                                    `<div class="d-flex flex-column w-75 p-10">
                                        <b>${val.tensp}</b>
                                        <div class="d-flex align-items-center mt-5">
                                            <div class="red fw-600">NGỪNG KINH DOANH</div>
                                        </div>
                                    </div>`
                            }
                            // hàng sắp về
                            else if(val.comingSoon === true) {
                                infoComponent =
                                    `<div class="d-flex flex-column w-75 p-10">
                                        <div class="fw-600">
                                            ${val.tensp}
                                            <span class="red ml-10">HÀNG SẮP VỀ</span>
                                        </div>
                                        <div class="d-flex align-items-center mt-5">
                                            <span class="red fw-600">${numberWithDot(val.giakhuyenmai)}<sup>đ</sup></span>
                                            ${val.khuyenmai != 0 ?
                                                `<span class="text-strike ml-10">${numberWithDot(val.gia)}<sup>đ</sup></span>
                                                <span class="red ml-10">-${val.khuyenmai * 100}%</span>`
                                                : ''
                                            }
                                        </div>
                                    </div>`
                            }
                            // tạm hết hàng
                            else if(val.inStocks === false) {
                                infoComponent =
                                    `<div class="d-flex flex-column w-75 p-10">
                                        <b>${val.tensp}</b>
                                        <div class="d-flex align-items-center mt-5">
                                            <div class="red fw-600">TẠM HẾT HÀNG</div>
                                        </div>
                                    </div>`
                            } else {
                                infoComponent =
                                    `<div class="d-flex flex-column w-75 p-10">
                                        <b>${val.tensp}</b>
                                        <div class="d-flex align-items-center mt-5">
                                            <span class="red fw-600">${numberWithDot(val.giakhuyenmai)}<sup>đ</sup></span>
                                            ${val.khuyenmai != 0 ?
                                                `<span class="text-strike ml-10">${numberWithDot(val.gia)}<sup>đ</sup></span>
                                                <span class="red ml-10">-${val.khuyenmai * 100}%</span>`
                                                : ''
                                            }
                                        </div>
                                    </div>`
                            }

                            phone += 
                                `<a href="dienthoai/${val.tensp_url}" class="head-single-result">
                                    <div class="d-flex">
                                        <div class="w-25 p-10">
                                            <img src="${data.url_phone + val.hinhanh}" alt="">
                                        </div>
                                        ${infoComponent}
                                    </div>
                                </a>`
                        });

                        $('.head-search-result').append(phone);
                                    
                        $('.search-loading').hide();
                        $('.head-search-result').show();
                        $('.head-search-result').scrollTop(0);
                    }
                });
        }, 200);

        if(xhr){
            xhr.abort();
        }

        $('.head-search-result').children().remove();
        $('.head-search-result').hide();
        $('.search-loading').show()

        if(enterKey == true){
            $('.search-loading').hide();

            if(xhr){
                xhr.abort();
            }
            return;
        }
    
        if($(this).val() == ''){
            $('.search-loading').hide();
        }
    });

    function submitSearch(keyword){
        let keywordArray = keyword.split(' ');
        let formatKeyword = '';

        // vd: iPhone 12 Pro Max => iphone-12-pro-max
        $.each(keywordArray, (i, val) => {
            if (i === keywordArray.length - 1) {
                formatKeyword += val;
            } else {
                formatKeyword += val + '-';
            }

        });

        enterKey = true;
        location.href = 'timkiem?keyword=' + formatKeyword;
    }

    // hiển thị offcanvas
    $('#show-offcanvas').on('click', function(){
        if($(this).attr('expanded') == 'false'){
            $('.head-offcanvas-box').css({
                'width' : '80%',
            });
            $('.backdrop').fadeIn();
            $(this).attr('expanded', 'true');
        } else {
            $('.head-offcanvas-box').css({
                'width' : '0',
            });
            $('.backdrop').fadeOut();
            $(this).attr('expanded', 'false');
        }
    });

    // nút đóng offcanvas
    $('#btn-close-offcanvas').on('click', function(){
        $('.head-offcanvas-box').css({
            'width' : '0',
        });
        $('.backdrop').css('display', 'none');
        $('#show-offcanvas').attr('aria-expanded', 'false');
    });

    // dropdown tài khoản trong offcanvas
    $('.offcanvas-account').click(function(){
        $('.offcanvas-account-option').toggle('blind', 300);
    });

    $('.head-account').click(function(){
        $('.head-account-dropdown').toggle('blind', 300);
    });

    switch(page) {
        /*============================================================================================================
                                                Đăng ký & Quên mật khẩu
        ==============================================================================================================*/
        case 'dangky':
        case 'khoiphuctaikhoan': {
            var firebaseConfig = {
                apiKey: "AIzaSyBmuQyKi5Xer3D4hFYHMkYTMx0Jb3Bcgrs",
                authDomain: "ldmobileauth-eb072.firebaseapp.com",
                projectId: "ldmobileauth-eb072",
                storageBucket: "ldmobileauth-eb072.appspot.com",
                messagingSenderId: "285654392090",
                appId: "1:285654392090:web:7dd64e8651ed9fdad660a3",
                measurementId: "G-YHSGQC11SQ"
            };
    
            // khởi tạo
            firebase.initializeApp(firebaseConfig);
            firebase.analytics();
            firebase.auth().useDeviceLanguage();
            firebase.auth().signOut();
    
            // window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('signup-step-1', {
            //     'size': 'invisible',
            //     'callback': (response) => {
            //         sendVerifyCode();
            //     },
            //     'expired-callback': () => {
            //         location.reload();
            //     }
            // });
            var verifier = '';
            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                'size': 'normal',
                'callback': (response) => {
                    verifier = response;
                    console.log(verifier)
                },
                'expired-callback': () => {
                    location.reload();
                }
            });
    
            recaptchaVerifier.render().then(function(widgetId) {
                window.recaptchaWidgetId = widgetId;
            });
    
            // gửi mã xác nhận
            function sendVerifyCode(){
                var telInp = $('#su_tel');
    
                // kiểm tra bẫy lỗi
                var valiTel = validatePhoneNumber(telInp);
    
                // sdt không hợp lệ
                if(!valiTel || verifier == ''){
                    return;
                }
    
                $('.loader').fadeIn();
                // kiểm tra đã sdt đã được đăng ký chưa
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'ajax-phone-number-is-exists',
                    type: 'POST',
                    data: {'sdt': telInp.val()},
                    success: function(data){
                        if(data == 'valid'){
                            if($('.error-message').length){
                                $('.error-message').remove();
                            }
    
                            var tel = telInp.val().toString();
                            var telFormat = tel.replace(tel[0], '+84'); // 0123456789 => +84123456789
                            var appVerifier = window.recaptchaVerifier;
                            verifier = '';
    
                            // gửi mã thành công
                            firebase.auth().signInWithPhoneNumber(telFormat, appVerifier).then(function (confirmationResult) {
                                window.confirmationResult = confirmationResult;
                                coderesult = confirmationResult;
    
                                // tiếp tục bước tiếp theo
                                $('#enter-information').addClass('none-dp');
    
                                // hiển thị gửi code vào số điện thoại
                                var displayTel = tel.replace('+1','');
                                $('#tel-confirm').text(displayTel);
                                $('#enter-verify-code').removeClass('none-dp');
                                $('#verify-code-inp').focus()
    
                            }).catch(function (error) { // gửi mã thất bại
                                console.log(error);
                                showAlertTop(errorMessage);
                                grecaptcha.reset(window.recaptchaWidgetId);
                                //location.reload();
                            });
                        } else {
                            // reset sdt
                            $('#su_tel').val('');
                            // reset reCAPTCHA
                            grecaptcha.reset(window.recaptchaWidgetId)
                            verifier = '';
    
                            if(!$('.error-message').length){
                                var message = $('<div class="error-message mb-20">Số điện thoại '+telInp.val()+' đã được đăng ký</div>');
                                $('#signup-form').before(message);
                            }
                        }
                    }
                });
                $('.loader').fadeOut();
            }
    
            function codeVerify(codeInput) {
                $('.loader').fadeIn();
    
                var code = codeInput.val();
            
                // xác nhận code hợp lệ
                coderesult.confirm(code).then(function (result) {
                    window.verifyingCode = false;
                    window.confirmationResult = null;
    
                    // tạo mật khẩu
                    $('#enter-verify-code').addClass('none-dp');
                    $('#enter-password').removeClass('none-dp');
                    $('#su_fullname').focus()
    
                }).catch(function (error) { // code không hợp lệ
                    window.verifyingCode = false;
                    console.log(error);
    
                    // hiển thị thông báo lỗi
                    codeInput.addClass('required');(errMessage);
                    var errMessage = $('<div class="required-text text-center">Mã xác thực không hợp lệ, vui lòng kiểm tra lại</div>');
                    codeInput.after(errMessage);
                });
    
                $('.loader').fadeOut();
            }
    
            if(page === 'dangky'){
                /*=====================================================
                                Nhập thông tin                  
                =======================================================*/
                // đăng ký tài khoản
                $('#signup-step-1').click(function(){
                    sendVerifyCode();
                });
    
                // kiểm tra nhập số diện thoại
                $('#su_tel').keyup(function(){
                    valiPhonenumberTyping($(this));
                });
    
                /*=====================================================
                                Xác minh mã xác nhận                  
                =======================================================*/
    
                // quay lại bước nhập sdt
                $('#back-to-enter-tel').click(function(){
                    // reset sdt
                    $('#su_tel').val('');
                    $('#su_tel').focus()
                    // reset reCAPTCHA
                    grecaptcha.reset(window.recaptchaWidgetId);
    
                    window.confirmationResult = null;
    
                    // quay lại phần nhập sdt
                    $('#enter-information').removeClass('none-dp');
                    $('#enter-verify-code').addClass('none-dp');
                });
    
                // Xác nhận code
                $('#signup-step-2').click(function(){
                    var codeInput = $('#verify-code-inp');
                    var valiVerfifyCode = validateVerifyCode(codeInput);
    
                    // code không hợp lệ
                    if(!valiVerfifyCode){
                        return;
                    } else {    // code hợp lệ
                        codeVerify(codeInput);
                    }
                });

                $('#verify-code-inp').keypress(function(e) {
                    if(e.keyCode == '13') {
                        $('#signup-step-2').trigger('click')
                    }
                })
    
                $('#verify-code-inp').keyup(function(){
                    if($(this).hasClass('required')){
                        $(this).removeClass('required');
                        $(this).next().remove();
                    }
                });
    
                /*=====================================================
                                Nhập mật khẩu
                =======================================================*/
    
                $('#signup-step-3').click(function(){
                    var nameInp = $('#su_fullname');
                    var passwordInp = $('#su_pw');
                    var rePasswordInp = $('#su_re_pw');
                    
                    var valiPw = validatePassword(passwordInp, rePasswordInp);
                    var valiName = validateFullName(nameInp)
    
                    if(valiName && valiPw){
                        $('#signup-form').submit();
                    }
                });

                $('#su_fullname').keyup(function(){
                    if($(this).hasClass('required')){
                        $(this).removeClass('required');
                        $(this).next().remove();
                    }
                });

                $('#su_re_pw').keypress(function(e) {
                    if(e.keyCode == '13') {
                        $('#signup-step-3').trigger('click')
                    }
                })
    
                $('#su_pw').keyup(function(){
                    if($(this).hasClass('required')){
                        $(this).removeClass('required');
                        $(this).next().remove();
                    }
                });
            
                $('#su_re_pw').keyup(function(){
                    if($(this).hasClass('required')){
                        $(this).removeClass('required');
                        $(this).next().remove();
                    }
                });
            }
            // quên mật khẩu 
            else {
                /*=====================================================
                                Nhập thông tin                  
                =======================================================*/
                // đăng ký tài khoản
                $('#forget-step-1').click(function(){
                    var valiTel = validatePhoneNumber($('#forget_tel'));
    
                    if(valiTel){
                        if(!verifier){
                            var elmnt = $('<span id="required-recaptcha" class="required-text">Vui lòng xác nhận</span>');
                            setTimeout(() => {
                                setTimeout(() => {
                                    elmnt.remove();
                                }, 500);
                                elmnt.fadeOut();
                            }, 3000);
                            $('#recaptcha-container').after(elmnt);
                            return;
                        }
                        $('.loader').fadeIn();
    
                        var tel = $('#forget_tel').val().toString();
                        var telFormat = tel.replace(tel[0], '+84');
                        var appVerifier = window.recaptchaVerifier;
                        verifier = '';
    
                        // gửi mã thành công
                        firebase.auth().signInWithPhoneNumber(telFormat, appVerifier).then(function (confirmationResult) {
                            window.confirmationResult = confirmationResult;
                            coderesult = confirmationResult;
    
                            // tiếp tục bước tiếp theo
                            $('#enter-information').addClass('none-dp');
    
                            // hiển thị gửi code vào số điện thoại
                            var displayTel = tel.replace('+1','');
                            $('#tel-confirm').text(displayTel);
                            $('#enter-verify-code').removeClass('none-dp');
    
                        }).catch(function (error) { // gửi mã thất bại
                            console.log(error);
                            showAlertTop(errorMessage);
                            grecaptcha.reset(window.recaptchaWidgetId);
                            location.reload();
                        });
    
                        $('.loader').fadeOut();
                    }
                });
    
                // kiểm tra nhập số diện thoại
                $('#forget_tel').keyup(function(){
                    valiPhonenumberTyping($(this));
                });
    
                /*=====================================================
                                Xác minh mã xác nhận                  
                =======================================================*/
    
                // quay lại bước nhập sdt
                $('#back-to-enter-tel').click(function(){
                    // reset sdt
                    $('#forget_tel').val('');
                    // reset reCAPTCHA
                    grecaptcha.reset(window.recaptchaWidgetId);
    
                    window.confirmationResult = null;
    
                    // quay lại phần nhập sdt
                    $('#enter-information').removeClass('none-dp');
                    $('#enter-verify-code').addClass('none-dp');
                });
    
                // Xác nhận code
                $('#forget-step-2').click(function(){
                    var codeInput = $('#verify-code-inp');
                    var valiVerfifyCode = validateVerifyCode(codeInput);
    
                    // code không hợp lệ
                    if(!valiVerfifyCode){
                        return;
                    } else {    // code hợp lệ
                        codeVerify(codeInput);
                    }
                });
    
                $('#verify-code-inp').keyup(function(){
                    if($(this).hasClass('required')){
                        $(this).removeClass('required');
                        $(this).next().remove();
                    }
                });
    
                /*=====================================================
                                Nhập mật khẩu
                =======================================================*/
    
                $('#forget-step-3').click(function(){
                    var passwordInp = $('#forget_pw');
                    var rePasswordInp = $('#forget_re_pw');
                    var valiPw = validatePassword(passwordInp, rePasswordInp);
    
                    if(valiPw){
                        $('#forget-form').submit();
                    }
                });
    
                $('#forget_pw').keyup(function(){
                    if($(this).hasClass('required')){
                        $(this).removeClass('required');
                        $(this).next().remove();
                    }
                });
            
                $('#forget_re_pw').keyup(function(){
                    if($(this).hasClass('required')){
                        $(this).removeClass('required');
                        $(this).next().remove();
                    }
                });
            }

            break
        }
        /*============================================================================================================
                                                        Đăng nhập
        ==============================================================================================================*/
        case 'dangnhap': {
            // đăng nhập
            $('#btn-login').click(function(){
                var telInp = $('#login_tel');
                var pwInp = $('#login_pw');
                var valiLogin = validateLogin(telInp, pwInp);
                $('#remember').is(':checked') ? $('#remember').val(true) : $('#remember').val(false);

                if(valiLogin){
                    $('#login-form').submit();
                }
            });

            function validateLogin(telInp, pwInp){
                if(telInp.hasClass('required') || pwInp.hasClass('required')){
                    return;
                }

                var phoneno = /^\d{10}$/;

                // chưa nhập sdt
                if(telInp.val().length == 0){
                    telInp.addClass('required');
                    var required = $('<span class="required-text">Vui lòng nhập số diện thoại</span>');
                    telInp.after(required);
                    return false;
                } else if(!telInp.val().match(phoneno)){ // không đúng định dạng
                    var required = $('<span class="required-text">Số diện thoại không hợp lệ</span>');
                    telInp.addClass('required');
                    telInp.after(required);
                    return false;
                }

                // chưa nhập mật khẩu
                if(pwInp.val().length == 0){
                    pwInp.addClass('required');
                    var required = $('<span class="required-text">Vui lòng nhập mật khẩu</span>');
                    pwInp.after(required);
                    return false;
                }

                return true;
            }

            $('#login_tel').keypress(function(e){
                if(e.keyCode == 13){
                    $('#btn-login').trigger('click');   
                }
            });
            $('#login_tel').keyup(function(){
                valiPhonenumberTyping($(this));
            });

            $('#login_pw').keypress(function(e){
                if(e.keyCode == 13){
                    $('#btn-login').trigger('click');   
                }
            });

            $('#login_pw').keyup(function(){
                if($(this).hasClass('required')){
                    $(this).removeClass('required');
                    $(this).next().remove();
                }
            });

            break
        }
        /*============================================================================================================
                                                        Index
        ==============================================================================================================*/
        case '': {
            // sec khuyến mãi
            var owl_promotion = $('#index-promotion-carousel');
            owl_promotion.owlCarousel({ 
                nav: false,
                rewind: true,
                dots: false,
                responsiveClass:true,
                responsive: {
                    0: {
                        items: 2
                    },
                    768: {
                        items: 3
                    },
                    992: {
                        items: 4
                    },
                    1200: {
                        items: 5
                    }
                }
            });

            $('#prev-index-promotion').on('click', function(){
                owl_promotion.trigger('prev.owl.carousel', [300]);
            });

            $('#next-index-promotion').on('click', function(){
                owl_promotion.trigger('next.owl.carousel');
            });

            break
        }
        /*============================================================================================================
                                                        Tài khoản
        ==============================================================================================================*/
        case 'taikhoan': {
            if(childPage == 'thongbao' && (navigation == 'reload' || navigation == 'back_forward')){
                loadMoreFlag = true;
                setTimeout(() => {
                    setTimeout(() => {
                        loadMoreFlag = false;
                    }, 400);
                    $(window).scrollTop(0);
                }, 400);
            }
    
            /*==================================================================================
                                            thông tin tài khoản
            ====================================================================================*/
            var modal_avt = $('#change-avt');
            var image = document.getElementById('pre-avt-big');
            var cropper_avt;
            var zoom_range = document.getElementById('zoom-range');
            var zoom_in = 0, zoom_out = 0;
    
            // thay đổi ảnh đại diện
            $('#btn-change-avt').click(function(){
                $('#change-avt-inp').trigger('click');
            });
    
            // hiển thị modal tiến hành cắt ảnh dại diện
            $('#change-avt-inp').change(function(e){
                startCropImg(cropper_avt, e);
            });
    
            function startCropImg(cropper, e){
                var files = e.target.files;
    
                // kiểm tra file hình
                var fileName = files[0].name.split('.');
                var extend = fileName[fileName.length - 1];
                console.log(extend);
    
                // định dạng hình ảnh
                if(extend === 'jpg' || extend === 'jpeg' || extend === 'png'){
                    const size = (files[0].size / BYTE) / BYTE // byte => MB
                            
                    // dung lượng file tối đa là 5MB
                    if(size > MAX_SIZE_IMAGE) {
                        showAlertTop(maxSizeImagMessage)
                        return
                    }
                    // hiển thị hình ảnh
                    if(files && files.length > 0){
                        var reader = new FileReader();
                        reader.onload = function(){
                            if(cropper != null){
                                cropper.replace(reader.result);
                                return;
                            } else {
                                image.src = reader.result;
                                modal_avt.modal('show');
                            }
                        };
                        reader.readAsDataURL(files[0]); 
                    }
                }
                // không phải định dạng hình ảnh
                else {
                    showAlertTop('Bạn chỉ có thể upload hình ảnh');
                }
            }
    
            // xử lý tạo mới cropper khi modal avt hiển thị
            modal_avt.on('shown.bs.modal', function(){    
                cropper_avt = new Cropper(image, {
                    aspectRatio: 1/1,
                    viewMode: 1,
                    responsive: true,
                    dragMode: 'move',
                    preview: '.preview-avt',
                    zoomOnWheel: false,
                    toggleDragModeOnDblclick: false,
                });
            }).on('hidden.bs.modal', function(){
                cropper_avt.destroy();
                cropper_avt = null;
                $('#change-avt-inp').val(null);
            }); // xử lý hủy cropper khi modal avt ẩn
    
            // reset crop img avt
            $('#reset-canvas').click(function(){
                cropper_avt.reset();
                refreshZoomVal();
            });
    
            // chọn ảnh khác
            $('.reselect-img').click(function(){
                $('#change-avt-inp').trigger('click');
                refreshZoomVal();
            });
    
            // cắt ảnh
            $(".crop-img").click(function(){
                var canvas = cropper_avt.getCroppedCanvas({
                    width: 320,
                    height: 320
                });
    
                canvas.toBlob(function(blob){
                    var reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function(){
                        $('.loader').show();
                        const base64String = reader.result;

                        $.ajax({
                            headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
                            url: 'ajax-change-avatar',
                            type: 'POST',
                            data: {base64String},
                            success: function(data) {
                                refreshZoomVal();
                                sessionStorage.setItem('toast-message', data.message)
                                location.reload()
                                return
                            },
                            error: function() {
                                $('.loader').fadeOut()
                                showToast(errorMessage)
                            }
                        })
                    };
                });
            });
    
            // thanh thu/phóng
            if($("#zoom-range").length){
                zoom_range.oninput = function(){
                    var val = this.valueAsNumber;
            
                    if(val == 0){
                        // đang phóng lớn
                        if(zoom_in == 0){
                            cropper_avt.zoom(0.1);
                            zoom_out = 0;
                        } else { // đang thu nhỏ
                            cropper_avt.zoom(-0.1);
                            zoom_in = 0;
                        }
                    } else if(val < 0){ // thu nhỏ
                        zoom_in = 0;
                        if(zoom_out > val){
                            zoom_out = val;
                            cropper_avt.zoom(-0.1);
                        } else {
                            zoom_out = val;
                            cropper_avt.zoom(0.1);
                        }
                    } else { // phóng lớn
                        if(zoom_in < val){
                            zoom_in = val;
                            cropper_avt.zoom(0.1);
                        } else {
                            zoom_in = val;
                            cropper_avt.zoom(-0.1);
                        }
                    }
                }   
            }
    
            // hàm gán lại giá trị ban đầu của biến
            function refreshZoomVal(){
                $('#zoom-range').val(0);
                zoom_in = 0, zoom_out = 0;
            }
    
            // thay đổi thông tin tài khoản
            $('#btn-change-info').click(function(){
                $('#change-info-div').toggle('blind', 300);
                removeRequried($('input[name="new_fullname_inp"]'));
            });
    
            // thay đổi tên người dùng
            $('#change-fullname-btn').click(function(){
                var valiFullname = validateFullName($('input[name="new_fullname_inp"]'));
    
                if(valiFullname){
                    const newFullName = $('input[name="new_fullname_inp"]').val()
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-change-fullname',
                        type: 'POST',
                        data: {'hoten': newFullName},
                        success:function(){
                            // thay đổi họ tên
                            $('#user_fullname').text(newFullName);
                            $($('.head-account').children()[0]).text(newFullName);
                            $('#offcanvas-name').text(newFullName);
    
                            showToast('Cập nhật họ và tên thành công');
    
                            $('#change-info-div').toggle('blind', 300);
                        }
                    });
                }
            });
    
            // thay đổi mật khẩu
            $('#change-pw-btn').click(function(){
                // chưa nhập mật khẩu
                if($('#old_pw').val().length == 0){
                    $('#old_pw').addClass('required');
                    var errMess = $('<div class="required-text">Vui lòng nhập mật khẩu</div>');
                    $('#old_pw').after(errMess);
                    return;
                }
                valiPW = validatePassword($('#new_pw'), $('#retype_pw'));
    
                if(valiPW){
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-change-password',
                        type: 'POST',
                        data: {'old_pw': $('#old_pw').val(), 'new_pw': $('#new_pw').val()},
                        success:function(data){
                            if(data['status'] == 'invalid password'){
                                $('#old_pw').addClass('required');
                                var errMess = $('<div class="required-text">Mật khẩu cũ không chính xác</div>');
                                $('#old_pw').after(errMess);
                            } else {
                                $('#change-pw-modal').modal('hide');
                                
                                showToast('Thay đổi mật khẩu thành công');
                            }
                        }
                    });
                }
            });
    
            // reset modal change password
            $('#change-pw-modal').on('hidden.bs.modal', function(){
                $('#old_pw').val('');
                $('#new_pw').val('');
                $('#retype_pw').val('');
    
                removeRequried($('#old_pw'));
                removeRequried($('#new_pw'));
                removeRequried($('#retype_pw'));
            });
    
            $('#old_pw').keyup(function(){
                removeRequried($(this));
            });
    
            $('#new_pw').keyup(function(){
                removeRequried($(this));
            });
    
            $('#retype_pw').keyup(function(){
                removeRequried($(this));
            });
    
            $('input[name="new_fullname_inp"]').keyup(function(){
                if($(this).hasClass('required')){
                    $(this).removeClass('required');
                    $(this).next().remove();
                }
            });
    
            /*==================================================================================
                                                Thông báo
            //==================================================================================*/
            
            // chọn loại thông báo
            $('.noti-type').off('click').click(function(){
                var type = $(this).data('type');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'ajax-get-type-notification',
                    type: 'POST',
                    data: {type},
                    success: function(data){
                        $('.noti-type').removeClass('noti-type-selected');
                        $(`.noti-type[data-type="${type}"]`).addClass('noti-type-selected');
                        $('#lst_noti').children().remove();
    
                        const html = renderNotification(data)
                        $('#lst_noti').append(html);

                        if(type === 'all'){
                            $('#lst_noti').attr('data-done', 'load');
                            loadMoreFlag = false;
                        } else {
                            $('#lst_noti').attr('data-done', 'done');
                            loadMoreFlag = true;
                        }
                    }
                })
            });
    
            // đánh dấu đã đọc
            $(document).on('click', '.noti-btn-read', function(){
                var id = $(this).data('id');
    
                $.ajax({
                    headers:{
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/ajax-check-noti',
                    type: 'POST',
                    data: {'id': id},
                    success:function(){
                        $('#noti-' + id).addClass('account-noti-checked').removeClass('account-noti-wait');
    
                        // cập nhật số lượng thông báo chưa đọc
                        var qty = parseInt($($('.not-seen-qty')[0]).text());
                        qty--;
                        if(qty == 0){
                            $('.not-seen-qty').hide();
                        } else {
                            $('.not-seen-qty').text(qty);
                        }
                        // xóa nút "đánh dấu đã đọc"
                        $('.noti-btn-read[data-id="'+id+'"]').remove();
    
                        $('.noti-type-selected').click();
                    }
                });
            });
    
            // xóa thông báo
            $(document).on('click', '.noti-btn-delete', function(){
                var id = $(this).data('id');
    
                $.ajax({
                    headers:{
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/ajax-delete-noti',
                    type: 'POST',
                    data: {'id':id},
                    success:function(){
                        setTimeout(() => {
                            $('#noti-' + id).remove();
                            if($('#lst_noti').children().length == 0){
                                var elmnt = $('<div class="p-70 box-shadow text-center">Bạn không có thông báo nào.</div>');
                                elmnt.show('fade');
                                $('#lst_noti').append(elmnt);
                            }
                        }, 500);
    
                        $('#noti-' + id).hide('drop');
    
                        // cập nhật số lượng thông báo chưa đọc
                        if($('.noti-btn-read[data-id="'+id+'"]').length){
                            var qty = parseInt($($('.not-seen-qty')[0]).text());
                            qty--;
                            if(qty == 0){
                                $('.not-seen-qty').hide();
                            } else {
                                $('.not-seen-qty').text(qty);
                            }
                        }
    
                        showToast('Đã xóa thông báo');
                    }
                });
            });
    
            // đánh dấu đọc tất cả
            $('#noti-btn-read-all').click(function(){
                if(!$('#lst_noti').children().length){
                    showToast('Bạn không có thông báo nào');
                } else {
                    $.ajax({
                        headers:{
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-check-all-noti',
                        success:function(){
                            $('.noti-btn-read').remove();
                            $('.account-noti-wait').addClass('account-noti-checked').removeClass('account-noti-wait');
                            
                            $('.not-seen-qty').text(0);
                            $('.not-seen-qty').hide();
    
                            showToast('Đã đọc tất cả thông báo');
                        }
                    });
                }
            });
    
            // xóa tất cả thông báo
            $('#noti-btn-delete-all').click(function(){
                if(!$('#lst_noti').children().length){
                    showToast('Bạn không có thông báo nào');
                } else {
                    $.ajax({
                        headers:{
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-delete-all-noti',
                        success:function(){
                            setTimeout(() => {
                                var elmnt = $('<div class="p-70 box-shadow text-center">Bạn không có thông báo nào.</div>');
                                elmnt.show('fade');
                                $('#lst_noti').after(elmnt);
                                $('.single-noti').remove();
                            }, 500);
        
                            $('.single-noti').hide('drop');
        
                            $('.not-seen-qty').text(0);
                            $('.not-seen-qty').hide();
                            
                            showToast('Đã xóa tất cả thông báo');
                        }
                    });
                }
            });
    
            // nút 3 chấm trang tài khoản - thông báo
            $('.account-btn-option').on('click', function(){
                $('.account-option-div').toggle()
            });
    
            /*==================================================================================
                                            Sản phẩm yêu thích
            //==================================================================================*/
    
            const noFavoriteProductElement =
                $('<div class="p-70 box-shadow d-flex justify-content-center flex-wrap">Bạn chưa có sản phẩm nào. <a href="dienthoai" class="ml-5">Xem sản phẩm</a></div></div>');

            // xóa điện thoại yêu thích
            $('.fav-btn-delete').off('click').click(function(){
                var id = $(this).data('id');
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/ajax-delete-favorite',
                    type: 'POST',
                    data: {'id': id},
                    success:function(){
                        setTimeout(() => {
                            $('#favorite-' + id).remove();
                            if($('#lst_favorite').children().length == 0){
                                noFavoriteProductElement.show('fade');
                                noFavoriteProductElement.appendTo($('#lst_favorite'));
                            }
                        }, 500);
    
                        $('#favorite-' + id).hide('drop');
    
                        // toast
                        showToast('Đã xóa sản phẩm khỏi danh sách yêu thích');
                    }
                });
            });
    
            // xóa tất cả điện thoại yêu thích
            $('#fav-btn-delete-all').click(function(){
                if(!$('#lst_favorite').children().length){
                    showToast('Bạn chưa có sản phẩm nào');
                } else {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-delete-all-favorite',
                        success:function(){
                            setTimeout(() => {
                                noFavoriteProductElement.show('fade');
                                $('#lst_favorite').after(noFavoriteProductElement);
                                $('.single-favorite').remove();
                            }, 500);
    
                            $('.single-favorite').hide('drop');
    
                            // toast
                            showToast('Đã xóa danh sách yêu thích');
                        }
                    });
                }
            });
    
            //================================================================================
            //============================== chi tiết đơn hàng ===============================
            //================================================================================
    
            // tùy chỉnh độ cao
            if($('#HTNH-div').length){
                var height = $('#HTNH-div').height();
                $('#PTTT-div').height(height);
            }
    
            // hủy đơn hàng
            $('#cancel-order-btn').click(function(){
                $('#delete-content').text('Bạn muốn hủy đơn hàng này?');
                $('#delete-btn').attr('data-id', $(this).data('id'));
                $('#delete-btn').attr('data-object', 'order');
                $('.cancel-btn').text('Đóng')
                $('#delete-btn').text('Hủy');
                $('#delete-modal').modal('show');
            });
    
            //=========================================================================
            //============================== sổ địa chỉ ===============================
            //=========================================================================
    
            // thêm địa chỉ giao hàng mới
            $('#btn-new-address').click(function(){
                $('#new-address-modal').modal('show');
            });
    
            $('#new-address-modal').on('hidden.bs.modal', function(){
                $('#TinhThanh-box').hide();
                $('#QuanHuyen-box').hide();
                $('#PhuongXa-box').hide();
            });
    
            // xóa 1 địa chỉ
            $('.btn-remove-address').click(function(){
                $('#confirm-modal').modal('show');
            });
    
            //=========================================================================
            //============================== Voucher===================================
            //=========================================================================
    
            // xóa voucher hết HSD
            $('.delete-voucher-btn').click(function(){
                let id = $(this).data('id')
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'ajax-delete-expired-voucher',
                    type: 'POST',
                    data: {'id': id},
                    success: function(){
                        // xóa voucher
                        $('.expired-voucher[data-id="'+id+'"]').remove()    
    
                        // toast
                        showToast('Đã xóa mã giảm giá')
                    }
                })
            })
    
            break
        }
        /*============================================================================================================
                                                        Điện thoại
        ==============================================================================================================*/
        case 'dienthoai':
        case 'timkiem': {
            /*===========================================================================
                                    Điện thoại || Tìm kiếm
            =============================================================================*/
            if(childPage === undefined){
                var queryString = window.location.search;
                var params = new URLSearchParams(queryString);
                if(params.has('hang')){
                    // không load thêm dữ liệu
                    loadMoreFlag = true;
                    // không lưu sessionStorage
                    storageFlag = false;
    
                    var brand = params.get('hang');
                    
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'ajax-get-product-by-brand',
                        type: 'POST',
                        data: {'brand': brand},
                        success: function(data){
                            setTimeout(() => {
                                $(window).scrollTop(0);
                            }, 500);
    
                            let html = ''
                            if(data.lst_product.length === 0) {
                                html =
                                    `<div>
                                        * Sản phẩm từ nhà cung cấp <b>${data.brandName}</b> hiện đang tạm hết hàng hoặc ngừng kinh doanh.
                                        <a href="dienthoai" class="ml-5">Xem sản phẩm khác</a>
                                    </div>`
                            } else {
                                html = renderPhoneCard(data.lst_product)
                            }
    
                            // gán dữ liệu vào view
                            $('#lst_product').append(html);
                            // tiêu đề lọc & sắp xếp
                            $('#qty-product').text(data.fs_title);
                            // ẩn nút lọc & sắp xếp
                            $('#filter-sort-btn').hide();
    
                            $('.loader').fadeOut();
                        },
                        error: function() {
                            showAlertTop(errorMessage)
                        }
                    });
                } else {
                    getTotalQtyProByCap()

                    var arrFilterSort = {
                        'filter': {},
                        'sort' : $('[name="sort"]:checked').val(),
                    };

                    var arrTemp = [];
                    var filterSortData = null;

                    // xóa sessionStorage & localStorage
                    switch(navigation) {
                        case 'navigate':
                            storageFlag = true;
                            loadMoreFlag = false;
                            sessionStorage.removeItem('loadMoreRow');
                            sessionStorage.removeItem('scrollPosition');
                            localStorage.removeItem('filterSortJson');
                            break
                        case 'reload':
                            localStorage.removeItem('filterSortJson')
                            getBackToTheOldPosition()
                            break
                        case 'back_forward':
                            getBackToTheOldPosition()
                            break
                    }
    
                    // nút hiển thị sắp xếp
                    $('#btn-show-sort').click(function(){
                        $('.shop-sort-box').toggle('blind', 250);
                    });
    
                    // gỡ bỏ chọn tất cả bộ lọc
                    $('.shop-btn-remove-filter').click(function(){
                        // icon loading
                        var loading = $('<div class="spinner-border text-light" role="status" style="width: 24px; height: 24px"></div>');
                        $('#btn-see-filter').text('');
                        loading.appendTo($('#btn-see-filter'));
    
                        arrFilterSort.filter = {};
    
                        arrTemp = [];
                        $('div[name="filter-item"]').removeClass('filter-selected');
                        filterSortProduct(arrFilterSort, data => {
                            filterSortData = data;
                                
                            $('.see-result-filter').show();
                            $('#btn-see-filter').text(`Xem ${data.length} kết quả`);

                            if(data.length == 0){
                                $('#btn-see-filter').attr('enable', 'false');
                            } else {
                                $('#btn-see-filter').attr('enable', 'true');
                            }
                        })
                    });
    
                    // thêm | xóa bộ lọc
                    $('.filter-item').off('click').click(function(){
                        // icon loading
                        var loading = $('<div class="spinner-border text-light" role="status" style="width: 24px; height: 24px"></div>');
                        $('#btn-see-filter').text('');
                        $('#btn-see-filter').attr('enable', 'false')
                        loading.appendTo($('#btn-see-filter'));
                        $('.see-result-filter').show();
                                        
                        var type = $(this).data('type');
                        var keyword = $(this).data('keyword');
                        let index = 0;
    
                        // hủy 1 bộ lọc
                        if($(this).hasClass('filter-selected')){
                            switch(type) {
                                case 'brand':
                                    index = arrFilterSort.filter['brand'].indexOf(keyword);
                                    if (index > -1) {
                                        arrFilterSort.filter['brand'].splice(index, 1);
                                    }
                                    if(arrFilterSort.filter['brand'].length == 0){
                                        delete arrFilterSort.filter['brand'];
                                    }
                                    $(this).removeClass('filter-selected');

                                    break
                                case 'price':
                                    index = arrFilterSort.filter['price'].indexOf(keyword);
                                    if (index > -1) {
                                        arrFilterSort.filter['price'].splice(index, 1);
                                    }
                                    if(arrFilterSort.filter['price'].length == 0){
                                        delete arrFilterSort.filter['price'];
                                    }
                                    $(this).removeClass('filter-selected');

                                    break
                                case 'os':
                                    index = arrFilterSort.filter['os'].indexOf(keyword);
                                    if (index > -1) {
                                        arrFilterSort.filter['os'].splice(index, 1);
                                    }
                                    if(arrFilterSort.filter['os'].length == 0){
                                        delete arrFilterSort.filter['os'];
                                    }
                                    $(this).removeClass('filter-selected');

                                    break
                                case 'ram':
                                    index = arrFilterSort.filter['ram'].indexOf(keyword);
                                    if (index > -1) {
                                        arrFilterSort.filter['ram'].splice(index, 1);
                                    }
                                    if(arrFilterSort.filter['ram'].length == 0){
                                        delete arrFilterSort.filter['ram'];
                                    }
                                    $(this).removeClass('filter-selected');

                                    break
                                case 'capacity':
                                    index = arrFilterSort.filter['capacity'].indexOf(keyword);
                                    if (index > -1) {
                                        arrFilterSort.filter['capacity'].splice(index, 1);
                                    }
                                    if(arrFilterSort.filter['capacity'].length == 0){
                                        delete arrFilterSort.filter['capacity'];
                                    }
                                    $(this).removeClass('filter-selected');

                                    break
                            }
                        }
                        // thêm bộ lọc
                        else {
                            switch(type) {
                                case 'brand':
                                    addFilter(arrFilterSort.filter, 'brand', keyword);
                                    $(this).addClass('filter-selected');

                                    break
                                case 'price':
                                    addFilter(arrFilterSort.filter, 'price', keyword);
                                    $(this).addClass('filter-selected');

                                    break
                                case 'os':
                                    addFilter(arrFilterSort.filter, 'os', keyword);
                                    $(this).addClass('filter-selected');

                                    break
                                case 'ram':
                                    addFilter(arrFilterSort.filter, 'ram', keyword);
                                    $(this).addClass('filter-selected');

                                    break
                                case 'capacity':
                                    addFilter(arrFilterSort.filter, 'capacity', keyword);
                                    $(this).addClass('filter-selected');

                                    break
                            }
                            
                            arrTemp.push($(this));
                        }

                        filterSortProduct(arrFilterSort, data => {
                            filterSortData = data;
                                    
                            $('#btn-see-filter').text(`Xem ${data.length} kết quả`);

                            if(data.length === 0){
                                $('#btn-see-filter').attr('enable', 'false');
                            } else {
                                $('#btn-see-filter').attr('enable', 'true');
                            }
                        })
                    });
                    
                    // sắp xếp
                    $('input[name="sort"]').change(function(){
                        $('.loader').show();
    
                        var sortType = $('input[name="sort"]:checked').val();
                        arrFilterSort['sort'] = sortType;
    
                        filterSortProduct(arrFilterSort, data => {
                            filterSortData = data;
                            $('#btn-see-filter').trigger('click');
                        })
                    });
    
                    function addFilter(arrFilterSort, filter, keyword){
                        if(arrFilterSort[filter] == null){
                            arrFilterSort[filter] = [];
                        }
                        arrFilterSort[filter].push(keyword);
                    }
    
                    // lọc & sắp xếp
                    function filterSortProduct(arrFilterSort, callback){
                        if(xhr) {
                            xhr.abort()
                        }

                        xhr = $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: '/ajax-filter-product',
                            type: 'POST',
                            data: {
                                arrFilterSort
                            },
                            success: function(data){
                                callback(data)
                            }
                        });
                    }
    
                    // xem danh sách kết quả lọc
                    $('#btn-see-filter').click(function(){
                        try{
                            if($(this).attr('enable') == 'false'){
                                return;
                            }
    
                            $('.loader').show();

                            $('#filter-modal').modal('hide');
    
                            loadFilterSortData(filterSortData);
                            $('.shop-sort-box').hide('blind', 250);
    
                            // không load thêm
                            loadMoreFlag = true;
                            // không lưu sessionStorage
                            storageFlag = false;
    
                            arrTemp = [];
                            $('.loader').fadeOut();
                        } catch(error){
                            showAlertTop('Đã cố lỗi xảy ra. Vui lòng thử lại');
                            location.reload();
                        }
                    });
    
                    // hiển thị danh sách kết quả bộ lọc
                    function loadFilterSortData(data){
                        $('#lst_product').children().remove();
                        $('#qty-product').text(data.length + ' điện thoại');
    
                        let html = renderPhoneCard(data)
    
                        $('#lst_product').append(html)
    
                        // số tiêu chí lọc
                        if(Object.keys(arrFilterSort.filter).length == 0){
                            $('.filter-badge').text('');
                            $('.filter-badge').css('display', 'none');
                        } else {
                            $('.filter-badge').text(Object.keys(arrFilterSort.filter).length);
                            $('.filter-badge').css('display', 'block');
                        }
    
                        if(Object.keys(arrFilterSort.filter).length == 0 && (arrFilterSort.sort == '' || arrFilterSort.sort === 'default')){
                            localStorage.removeItem('filterSortJson');
                        } else {
                            localStorage.setItem('filterSortJson', JSON.stringify(arrFilterSort));
                        }
                    }

                    $('#filter-modal').on('shown.bs.modal', function(){
                        $('.shop-sort-box').hide('blind', 250);
                    })
                    // reset modal
                    $('#filter-modal').on('hidden.bs.modal', function(){
                        if(xhr) {
                            xhr.abort()
                        }

                        if(arrTemp.length){
                            $.each(arrTemp, (i, element) => {
                                $(element).removeClass('filter-selected');
                            })

                            arrFilterSort = {
                                'filter': {},
                                'sort' : $('[name="sort"]:checked').val(),
                            };
                        }

                        if(!$('.filter-selected').length) {
                            $('.see-result-filter').hide();
                        }
                    });
                }
    
                // modal chọn màu sắc để thêm vào giỏ hàng
                $(document).on('click', '.shop-cart-link', function(){
                    var id_sp = $(this).data('id');
                    chooseColor(id_sp)
                })
            }
            /*===========================================================================
                                            Chi tiết
            =============================================================================*/
            else {
                if(sessionStorage.getItem('positionEvaluate')){
                    var position = sessionStorage.getItem('positionEvaluate');
                    
                    setTimeout(() => {
                        $(window).scrollTop(position);
                        sessionStorage.removeItem('positionEvaluate');
                    }, 250);
                }
    
                if(sessionStorage.getItem('seeAllEvaluate')) {
                    setTimeout(() => {
                        if(navigation === 'navigate') {
                            sessionStorage.removeItem('seeAllEvaluate');
                        } else {
                            $('.see-all-evaluate').trigger('click')
                        }
    
                    }, 500)
                }
    
                var queryString = window.location.search;
                var params = new URLSearchParams(queryString);
    
                if(params.has('mausac')){
                    var needColor = params.get('mausac');
                    
                    $('.color-option').each(function(){
                        var color = removeAccents($(this).data('color')).toLowerCase();
                        color = color.replace(' ', '-');
                        if(color === needColor){
                            var id = $(this).data('id');
                            var image = $(this).data('image');
                            var id_sp = $(this).data('id');
                            changeColor(id, image, id_sp);
                            return;
                        }
                    });
                }
    
                if(params.has('danhgia')){
                    const id_dg = params.get('danhgia');
                    const evaluate = $(`.evaluate[data-id="${id_dg}"]`);
                    if(evaluate.length){
                        const position = parseFloat(evaluate.position().top) + 650;
        
                        setTimeout(() => {
                            setTimeout(() => {
                                setTimeout(() => {
                                    evaluate.removeAttr('style');
                                }, 1000);
                                evaluate.css({
                                    'background-color': '#fff'
                                });    
                            }, 3000);
        
                            $(window).scrollTop(position);
        
                            evaluate.css({
                                'background-color': '#D2F4EA',
                                'transition': '.5s',
                            });
                        }, 300);
                    }
                }

                // mẫu sp theo dung lượng đang kinh doanh
                if(!$('#qty-in-stock-status').length) {
                    // kiểm tra slton
                    const id_sp = $('.color-option.selected').attr('data-id')
                    getQtyInStockById(id_sp)
                        .then(data => toggleElementWhenOutOfStock(data))
                        .catch(() => showAlertTop(errorMessage))
                }
                // mẫu sp ngừng kinh doanh
                else {
                    $('#main-img').after('<div class="stop-business-badge note-badge"></div>')
                    $('.favorite-tag').hide()
                    $('#check-qty-in-stock-btn').hide()
                }
                
                // slide hình ảnh khác của sản phẩm
                $('#detail-carousel').owlCarousel({
                    stagePadding: 5,
                    nav: false,
                    rewind: true,
                    dots: false,
                    items: 4
                });
    
                // chuyển đổi hình ảnh khác
                var owl_detail = $('#detail-carousel');
                owl_detail.owlCarousel();
    
                $('#prev-another-img').on('click', function(){
                    owl_detail.trigger('prev.owl.carousel', [300]);
                });
    
                $('#next-another-img').on('click', function(){
                    owl_detail.trigger('next.owl.carousel');
                });
    
                // chuyển đổi hình ảnh khác
                $('.another-img').off('click').click(function(){
                    const id = $(this).data('id')
                    $(`.color-option[data-id="${id}"]`).trigger('click')
                });
    
                // thay đổi màu sắc
                $('.color-option').off('click').click(function(){
                    var id = $(this).data('id');
                    var image = $(this).data('image');
                    var id_sp = $(this).data('id');
                    changeColor(id, image, id_sp);
                });
    
                function changeColor(id, image, id_sp){
                    var element = $(`.color-option[data-id="${id}"]`);
                    // thay đổi hình sản phẩm
                    $('#main-img').attr('src', image);
    
                    // gỡ check các màu khác
                    $('.color-option').removeClass('selected');
                    $('.color-name').removeClass('fw-600');
    
                    // check vào màu đang chọn
                    element.addClass('selected');
                    $(element.children()[0]).addClass('fw-600');
    
                    // kiểm tra yêu thích sản phẩm
                    if(element.attr('favorite') == "true"){
                        // thay đổi icon
                        $('.favorite-tag').html('<i class="fas fa-heart"></i>');
                    } else {
                        // thay đổi icon
                        $('.favorite-tag').html('<i class="far fa-heart"></i>');
                    }
    
                    // cập nhật id nút mua ngay
                    $('.buy-now').attr('data-id', id_sp);
    
                    // kiểm tra còn hàng
                    getQtyInStockById(id_sp)
                        .then(data => toggleElementWhenOutOfStock(data))
                        .catch(() => showAlertTop(errorMessage))
                }

                // ẩn/ hiện 1 vài element khi slton < 0 hoặc ngừng kinh doanh
                function toggleElementWhenOutOfStock(data) {
                    const product = data.product
                    const qtyInStock = data.qtyInStock

                    $('.note-badge').remove()

                    // ngừng kinh doanh
                    if(!product.trangthai){
                        $('#main-img').after('<div class="stop-business-badge note-badge"></div>')
                        $('.buy-now').hide();
                        $('.favorite-tag').hide()
                        $('#check-qty-in-stock-btn').hide()
                        $('#evaluate-section').show()
                        $('#comment-seciton').show()
                    }
                    // hàng sắp về
                    else if(product.comingSoon) {
                        $('#main-img').after('<div class="coming-soon note-badge">HÀNG SẮP VỀ</div>')
                        $('.buy-now').hide();
                        $('.favorite-tag').show()
                        $('#check-qty-in-stock-btn').hide()
                        $('#evaluate-section').hide()
                        $('#comment-section').hide()
                    }
                    // hết hàng
                    else if(!qtyInStock){
                        $('#main-img').after('<div class="out-of-stock-badge note-badge"></div>')
                        $('.buy-now').hide();
                        $('.favorite-tag').show()
                        $('#check-qty-in-stock-btn').hide()
                        $('#evaluate-section').show()
                        $('#comment-seciton').show()
                    }
                    // còn hàng
                    else {
                        $('.buy-now').show();
                        $('.favorite-tag').show()
                        $('#check-qty-in-stock-btn').show()
                        $('#evaluate-section').show()
                        $('#comment-seciton').show()
                    }
                }
    
                // đánh đấu đã yêu thích sản phẩm
                if($('.favorite-tag').length){
                    var favorite = $('.color-option.selected').attr('favorite');
                    // đã yêu thích
                    if(favorite == "true"){
                        // thay đổi icon
                        $('.favorite-tag').children().remove();
                        var heartClicked = $('<i class="fas fa-heart"></i>');
                        heartClicked.appendTo($('.favorite-tag'));
                    }
                }
    
                // yêu thích sản phẩm
                $('.favorite-tag').click(function(){
                    var id_sp = $('.color-option.selected').data('id');
                    var phoneName = $(this).data('name');
                    var color = $('.color-option.selected').data('color');
    
                    clearTimeout(timer);
                    $('#add-favorite').remove();
    
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-add-delete-favorite',
                        type: 'POST',
                        data: {'id_sp':id_sp},
                        success:function(data){
                            // yêu cầu đăng nhập
                            if(data['status'] == 'login required'){
                                showToast('Vui lòng đăng nhập để thực hiện chức năng này');
                            }
                            // thêm thành công
                            else if(data['status'] == 'add success'){
                                // thay đổi icon
                                $('.favorite-tag').html('<i class="fas fa-heart"></i>');
    
                                // hiển thị toast
                                const message = `Đã thêm <b>${phoneName} - ${color}</b> vào danh sách yêu thích`
                                showToast(message);
    
                                // đánh dấu yêu thích của màu sắc
                                $('.color-option.selected').attr('favorite', 'true');
                            }
                            // xóa thành công
                            else if(data['status'] == 'delete success'){
                                // thay đổi icon
                                $('.favorite-tag').html('<i class="far fa-heart"></i>');
    
                                const message = `Đã xóa <b>${phoneName} - ${color}</b> khỏi danh sách yêu thích`
                                showToast(message);
    
                                // đánh dấu yêu thích của màu sắc
                                $('.color-option.selected').attr('favorite', 'false');
                            }
                        },
                        error: function() { showAlertTop(errorMessage) }
                    });
                });
    
                // kiểm tra còn hàng
                $('#check-qty-in-stock-btn').click(function(){
                    var id_sp = $(this).data('id');
    
                    // lấy các màu sắc của sản phẩm
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-choose-color',
                        type: 'POST',
                        data: {'id_sp': id_sp, 'page': page},
                        success:function(data){
                            $('#check-qty-in-stock-phone-name').text(data.tensp);
    
                            let html = ''
                            const urlPhone = data.url_phone
    
                            $.each(data.mausac, (i, val) => {
                                html +=`
                                    <div type="button" data-id="${val.id}" class="check-qty-in-stock-item">
                                        <img src="${urlPhone + val.hinhanh}" alt="">
                                        <div id="color-name" class="pt-5">${val.mausac}</div>
                                    </div>`
                            })
    
                            $('#check-qty-in-stock-lst-color').append(html)
                            $('#check-qty-in-stock-modal').modal('show');
                        },
                        error: function() {
                            showToast(errorMessage)
                        }
                    })
                });
    
                // thay đổi chi nhánh kiểm tra
                $('#check-qty-in-stock-select').change(function() {
                    const selectedColor = $('.check-qty-in-stock-item.choose-color-selected')
    
                    if(selectedColor.length) {
                        const id_sp = selectedColor.attr('data-id')
                        const id_tt = $(this).val()
    
                        getBranchWithQtyInStock(id_sp, id_tt)
                    }
                })
    
                // chọn màu sắc kiểm tra còn hàng
                $(document).on('click', '.check-qty-in-stock-item', function(){
                    const id_sp = $(this).attr('data-id');
                    const id_tt = $('#check-qty-in-stock-select').val()
    
                    $('.check-qty-in-stock-item').removeClass('choose-color-selected');
                    $(this).addClass('choose-color-selected');
    
                    getBranchWithQtyInStock(id_sp, id_tt)
                });
    
                // reset modal check stock
                $('#check-qty-in-stock-modal').on('hidden.bs.modal', function(){
                    $('#check-qty-in-stock-lst-color').children().remove();
                    $('#check-qty-in-stock-select option:first').prop('selected', true)
                    $('.list-branch').children().remove();
                    $('.list-branch').hide();
                    $('#check-qty-in-stock-status').html('');
                });
    
                function getBranchWithQtyInStock(id_sp, id_tt) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN,
                        },
                        url: 'ajax-get-branch-with-qty-in-stock',
                        type: 'POST',
                        data: {
                            id_sp,
                            id_tt
                        },
                        success: function(data) {
                            $('.list-branch').children().remove()

                            // không có chi nhánh còn hàng
                            if(data.length === 0) {
                                const noBranch =
                                    `<div class="single-branch text-center">Không có chi nhánh còn hàng.</div>`
                                    
                                $('.list-branch').append(noBranch)
                            } else {
                                const branchs = data.map(val => {
                                    return (
                                        `<div class="single-branch">
                                            <i class="fas fa-store mr-10"></i>${val.diachi}
                                        </div>`
                                    )
                                }).join('')
    
                                $('.list-branch').append(branchs)
                            }

                            $('.list-branch').show('blind', 250)
                        },
                        error: function() {
                            showToast(errorMessage)
                        }
                    })
                }
    
                // sản phẩm cùng hãng
                var owl_sameBrand = $('#same-brand-pro-carousel');
                owl_sameBrand.owlCarousel({
                    nav: false,
                    rewind: true,
                    dots: false,
                    responsiveClass:true,
                    responsive: {
                        0: {
                            items: 2
                        },
                        768: {
                            items: 3
                        },
                        992: {
                            items: 4
                        },
                        1200: {
                            items: 5
                        }
                    }
                });
    
                $('#prev-brand').on('click', function(){
                    owl_sameBrand.trigger('prev.owl.carousel', [300]);
                });
    
                $('#next-brand').on('click', function(){
                    owl_sameBrand.trigger('next.owl.carousel');
                });
    
                // sản phẩm tương tự
                var owl_similar = $('#similar-pro-carousel');
                owl_similar.owlCarousel({
                    nav: false,
                    rewind: true,
                    dots: false,
                    responsiveClass:true,
                    responsive: {
                        0: {
                            items: 2
                        },
                        768: {
                            items: 3
                        },
                        992: {
                            items: 4
                        },
                        1200: {
                            items: 5
                        }
                    }
                });
    
                $('#prev-similar').on('click', function(){
                    owl_similar.trigger('prev.owl.carousel', [300]);
                });
    
                $('#next-similar').on('click', function(){
                    owl_similar.trigger('next.owl.carousel');
                });

                /**=====================================================
                 *                      Đánh giá
                 * =====================================================
                 */
    
                // hiển thị đánh giá sản phẩm
                if($('#total_rating').length){
                    var total_rating = parseInt($('#total_rating').val());
    
                    // tổng số lượng đánh giá từng sao
                    var total_star_5 = 5 * parseInt($('#percent-5-star').data('id'));
                    var total_star_4 = 4 * parseInt($('#percent-4-star').data('id'));
                    var total_star_3 = 3 * parseInt($('#percent-3-star').data('id'));
                    var total_star_2 = 2 * parseInt($('#percent-2-star').data('id'));
                    var total_star_1 = 1 * parseInt($('#percent-1-star').data('id'));
    
                    // tính sao trung bình = tổng của tổng số lượng đánh giá từng sao / tổng số đánh giá
                    var avg_star = (total_star_5 + total_star_4 + total_star_3 + total_star_2 + total_star_1) / total_rating;
    
                    $('.detail-vote-avg').text(avg_star.toFixed(1));
    
                    // hiển thị chi tiết số lượng đánh giá của từng sao
                    for(var i = 1; i <= 5; i++){
                        var element = `#percent-${i}-star`;
                        var qtyOfStar = $(element).data('id');
                        var total = total_rating;
                        var id = `#percent-${i}-star`;
                        ratingStar(id, qtyOfStar, total);
                    }
                }
    
                // hàm hiển thị phần trăm thanh progress
                function ratingStar(id, qtyOfStar, total){
                    var avg = (qtyOfStar / total) * 100 + '%';
                    $(id).css('width', avg);
                }
    
                // đánh giá sao sản phẩm
                $('.star-rating').hover(
                    // mouse enter
                    function(){
                        $('.star-rating').removeAttr('style');
                        var star = $(this).data('id');
                        for(var i = 1; i <= star; i++){
                            $(`.star-rating[data-id="${i}"]`).css('color' , 'orange');
                        }
                    }, 
                    // mouse leave
                    function(){
                        $('.star-rating').removeAttr('style');
                        var star = $('#star_rating').val();
                        if(star != 0){
                            for(var i = 1; i <= star; i++){
                                $(`.star-rating[data-id="${i}"]`).css('color' , 'orange');
                            }
                        } else {
                            $('.star-rating').removeAttr('style');
                        }
                    }
                );
    
                // chọn sao
                $('.star-rating').off('click').click(function(){
                    if($('.star_rating_required').length){
                        $('.star_rating_required').remove();
                    }
                    var star = $(this).data('id');
                    $('#star_rating').val(star);
    
                    //$('.star-rating').removeAttr('style');
    
                    for(var i = 1; i <= star; i++){
                        $(`.star-rating[data-id="${i}"]`).css('color' , 'orange');
                    }
                });
    
                $('#btn-photo-attached').click(function(){
                    $('.upload-evaluate-image').trigger('click');
                });
    
                // hiển thị modal chọn sản phẩm đánh giá
                $('#phone-evaluate-show').click(function(){
                    $('#phone-evaluate-modal').modal('show');
                });
    
                // chọn sản phẩm muốn đánh giá
                $('.phone-evaluate').off('click').click(function(){
                    removeRequried($('.phone-evaluate-div'));
    
                    // gỡ chọn
                    if($(this).hasClass('phone-evaluate-selected')){
                        $(this).removeClass('phone-evaluate-selected');
                        $('#all_phone_evaluate').prop('checked', false);
                    } else {
                        $('#all_phone_evaluate').prop('checked', false);
                        $(this).addClass('phone-evaluate-selected');
                    }            
                });
    
                // chọn tất cả sản phẩm muốn đánh giá
                $('#all_phone_evaluate').click(function(){
                    if($(this).is(':checked')){
                        $('.phone-evaluate').addClass('phone-evaluate-selected');
                        removeRequried($('.phone-evaluate-div'));
                    } else {
                        $('.phone-evaluate').removeClass('phone-evaluate-selected');
                    }
                });
    
                // xác nhận chọn sản phẩm đánh giá
                $('#choose-phone-evaluate').click(function(){
                    // chưa chọn
                    if($('.phone-evaluate.phone-evaluate-selected').length == 0){
                        if($('.phone-evaluate-div').hasClass('required')){
                            return;
                        }
                        $('.phone-evaluate-div').addClass('required');
                        $('.phone-evaluate-div').after('<span class="required-text">Vui lòng ít nhất 1 sản phẩm</span>');
                        return;
                    }
    
                    // các sản phẩm được chọn
                    var lst_id = [];
                    $('.phone-evaluate.phone-evaluate-selected').each(function() {
                        lst_id.push($(this).attr('data-id'));
                    })
    
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-choose-phone-to-evaluate',
                        type: 'POST',
                        data: {'lst_id': lst_id},
                        success:function(data){
                            $('#phone-evaluate-div').children().remove();
    
                            let html = ''
                            if(data.length === 1){
                                html += `
                                    <div class="d-flex align-items-center">
                                        <img src="images/phone/${data[0].hinhanh}" alt="" width="35px">
                                        <div>${data[0].tensp}-${data[0].mausac}</div>
                                        <div id="phone-evaluate-show" type="button" class="main-color-text ml-10">Thay đổi</div>
                                    </div>
                                `
    
                                $('#phone-evaluate-div').append(html)
                            } else {
                                const phoneChosen = data.map(val => {
                                    return (
                                        `<div class="phone-chosen-item">
                                            <img src="images/phone/${val.hinhanh}" width="50px" height="50px" class="mr-5">
                                            <div class="d-flex flex-column fz-14">
                                                <b>${val.tensp}</b>
                                                <div>${val.mausac}</div>
                                            </div>
                                        </div>`
                                    )
                                }).join('')
                                
                                html += `
                                    <div class="relative">
                                        <div class="d-flex">
                                            <div class="d-flex align-items-center border p-10">
                                                <div class="mr-10">Đánh giá cho ${data.length} sản phẩm</div>
                                                <i id="show-phone-chosen" class="fas fa-eye"></i>
                                                <div id="phone-evaluate-show" type="button" class="main-color-text">Thay đổi</div>
                                            </div>
                                        </div>

                                        <div class="phone-chosen-wrapper">
                                            ${phoneChosen}
                                        </div>
                                    </div>

                                `
                                $('#phone-evaluate-div').append(html)
                            }
    
                            $('#phone-evaluate-modal').modal('hide');
                            $('#phone-evaluate-div').attr('data-flag', '1');
                            $('#choose-phone-evaluate-required').remove();
                            $('#lst_id').val(lst_id);
                            
                            //console.log(data);
                        }
                    });
                });

                let isShow = false
                $(document).on('mouseenter', '#show-phone-chosen', function() {
                    const position = $(this).position()
                    const width = $('.phone-chosen-wrapper').width()
                    const left = position.left - width + 15
                    const top = position.top + 20

                    $('.phone-chosen-wrapper').css({
                        left,
                        top,
                        display: 'block'
                    })
                })
                $(document).on('mouseleave', '#show-phone-chosen', function() {
                    setTimeout(() => {
                        if(isShow === false) {
                            $('.phone-chosen-wrapper').removeAttr('style')
                        }
                    }, 250)
                })

                $(document).on('mouseenter', '.phone-chosen-wrapper', function() {
                    isShow = true
                    $('.phone-chosen-wrapper').css('display', 'block')
                })
                $(document).on('mouseleave', '.phone-chosen-wrapper', function() {
                    isShow = false
                    $('.phone-chosen-wrapper').removeAttr('style')
                })
    
                $(document).on('click', '#phone-evaluate-show', function(){
                    $('#phone-evaluate-modal').modal('show');
                });
    
                // tự động select vào các điện thoại đang chọn
                $('#phone-evaluate-modal').on('shown.bs.modal', function(){
                    var lst_id = $('#lst_id').val();
                    if(lst_id){
                        lst_id = lst_id.split(',');
                        $.each(lst_id, function(key, val){
                            $(`.phone-evaluate[data-id="${val}"]`).addClass('phone-evaluate-selected');
                        });
                    }
                });
                $('#phone-evaluate-modal').on('hidden.bs.modal', function(){
                    $('.phone-evaluate').removeClass('phone-evaluate-selected');
                    removeRequried($('.phone-evaluate-div'))
                });
    
                // thêm hình đánh giá
                $('.upload-evaluate-image').change(function(){
                    //số lượng hình upload
                    var count = this.files.length;
    
                    // nếu không chọn hình nào thì thoát 
                    if(count === 0){
                        return;
                    }

                    // tổng số lượng hình hiện tại
                    var qty_img = parseInt($('.qty-img-inp').val());
    
                    // số lượng hình
                    $('.qty-img').show();
    
                    // hiển thị div chứa hình ảnh
                    $('.evaluate-img-div').css({
                        'display': 'flex',
                    });

                    const timestamp = new Date().getTime()
    
                    // tạo thẻ div, nút xóa, hình đánh giá
                    $.each(this.files, (i, image) => {
                        // kiểm tra file hình
                        var fileName = image.name.split('.');
                        var extend = fileName[fileName.length - 1];
                
                        if(extend === 'jpg' || extend === 'jpeg' || extend === 'png'){
                            // nếu số lượng hình > 3 thì hiển thị modal thông báo
                            if(qty_img >= 3) {
                                showAlertTop('Bạn chỉ được phép chọn 3 ảnh đính kèm');
                                return false
                            }

                            const size = (image.size / BYTE) / BYTE // byte => MB
                            
                            // dung lượng file tối đa là 5MB
                            if(size > MAX_SIZE_IMAGE) {
                                showAlertTop('Ảnh đính kèm có dung luọng tối đa là 5 MB')
                                return false
                            }

                            const id = timestamp + i
    
                            // hình đánh giá
                            const imgURL = URL.createObjectURL(image)
                            const imageEvaluate = renderEvaluateImage('create', id, imgURL)
                            $('.evaluate-img-div').append(imageEvaluate)
    
                            qty_img++;
                        } else {
                            showAlertTop('Bạn chỉ có thể upload hình ảnh');
                            if(qty_img == 0){
                                $(this).val('');
                            }
                            return false
                        }
                    })
    
                    $('.qty-img-inp').val(qty_img);
    
                    // hiển thị số lượng hình ảnh đang có
                    $('.qty-img').html(`(${qty_img})`);

                    // làm mới input file
                    $(this).val('')
                });
    
                // gửi đánh giá
                $('#send-evaluate-btn').click(function(){
                    submitEvaluate()
                });

                async function submitEvaluate() {
                    var valiStarRating = validateStarRating($('#star_rating'));
                    var valiPhoneEvaluate = validatePhoneEvaluate($('#phone-evaluate-div'));
    
                    if(valiStarRating && valiPhoneEvaluate) {
                        $('.loader').fadeIn();

                        // mảng base64 string hình đánh giá
                        let evaluateImage = [];

                        // chuyển hình => base64
                        if($('.evaluate-img-div').children().length) {
                            await pushBase64ToArray($('.img-rating img'))
                                .then(base64List => evaluateImage = base64List)
                        }

                        const lst_id = $('#lst_id').val()
                        const evaluateStarRating = $('#star_rating').val()
                        const evaluateContent = $('#evaluate_content').val()

                        const data = {
                            lst_id,
                            evaluateStarRating,
                            evaluateContent,
                            evaluateImage
                        }

                        const length = evaluateImage.length - 1

                        // không có ảnh đính kèm
                        if(length === -1) {
                            return createEvaluate(data)
                        } else {
                            $.each(evaluateImage, (i, base64) => {
                                // upload từng hình
                                uploadSingleImageEvaluate(base64, i)
                                    .then(() => {
                                        // khi đã upload tất cả hình xong thì tạo đánh giá
                                        if(i === length) {
                                            return createEvaluate(data)
                                        }
                                    })
                                    .catch(() => {
                                        $('.loader').fadeOut()
                                        showToast(errorMessage)
                                    })
                            })
                        }
                    }
                }

                function uploadSingleImageEvaluate(base64String, index) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
                            url: 'ajax-upload-single-image-evaluate',
                            type: 'POST',
                            data: {
                                base64String,
                                index,
                            },
                            success: function() {
                                resolve()
                            },
                            error: function(error) {
                                console.error(error)
                                reject()
                            }
                        })
                    })
                }

                function createEvaluate(data) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-create-evaluate',
                        type: 'POST',
                        data: data,
                        success:function(data){
                            sessionStorage.setItem('toast-message', data.message);
                            location.reload();
                            return
                        },
                        error: function(){
                            $('.loader').fadeOut()
                            sessionStorage.setItem('alert-top-message', errorMessage)
                            location.reload()
                        }
                    });
                }
    
                // kiểm tra đã chọn sao đánh giá chưa
                function validateStarRating(starRating){
                    // nếu kiểm tra rồi thì return
                    if($('.star_rating_required').length){
                        return;
                    }
    
                    if(starRating.val() == '0'){
                        var required = $('<span class="star_rating_required required-text">Vui lòng chọn đánh giá</span>');
                        starRating.after(required);
                        return false;
                    }
    
                    return true;
                }
    
                // kiểm tra đã chọn sản phẩm để đánh giá chưa
                function validatePhoneEvaluate(phoneEvaluate) {
                    if($('#choose-phone-evaluate-required').length){
                        return;
                    }
    
                    // chưa chọn
                    if(phoneEvaluate.data('flag') == null){
                        var required = $('<span id="choose-phone-evaluate-required" class="required-text">Vui lòng chọn sản phẩm đánh giá</span>');
                        phoneEvaluate.after(required);
                        return false;
                    }
    
                    return true;
                }
    
                // xem ảnh đánh giá
                $(document).on('click', '.img-evaluate', function(){
                    var id_img = $(this).data('id');
                    var id_dg = $(this).data('evaluate');
                    seeReviewImage(id_img, id_dg);
                });
    
                function seeReviewImage(id_img, id_dg) {
                    // id_dg cho nút đóng
                    $('.close-see-review-image').attr('evaluate', id_dg);
    
                    // ảnh lớn
                    $('#review-image-main').attr('src', $(`.img-evaluate[data-id="${id_img}"]`).attr('src'));
    
                    // ảnh nhỏ
                    $('#another-review-image').children().remove();
                    var evaluateImage = $(`img[data-evaluate="${id_dg}"]`);
                    evaluateImage.clone().appendTo($('#another-review-image'));
    
                    // đánh dấu ảnh đang xem
                    $(`img[data-evaluate="${id_dg}"]`).removeClass('img-evaluate-selected');
                    $(`img[data-id="${id_img}"]`).addClass('img-evaluate-selected');
    
                    $('body').css('overflow', 'hidden');
                    $('.backdrop').css('z-index', 110);
                    $('.backdrop').fadeIn();
                    $('.see-review-image-card').show('drop');
                }
    
                // đóng xem ảnh đánh giá
                $('.close-see-review-image').click(function(){
                    $('.backdrop').fadeOut();
                    $('.see-review-image-card').hide('drop');
                    $('body').removeAttr('style');
    
                    var id_dg = $(this).attr('evaluate');
    
                    // xóa ảnh đang chọn
                    $(`img[data-evaluate="${id_dg}"]`).removeClass('img-evaluate-selected');
    
                    $('#another-review-image').children().remove();
                });
    
                // nút prev, next xem ảnh đánh giá
                $('.prev-see-review-image').click(function(){
                    var imgSelected = $('#another-review-image').find($('.img-evaluate.img-evaluate-selected'));
    
                    // hình ảnh phía trước
                    var prevImg = imgSelected.prev();
    
                    // đang ở hình ảnh đầu tiên => hiển thị hình cuối cùng
                    if(prevImg.length == 0){
                        $('#another-review-image').children().last().click();
                    } else {
                        prevImg.click();
                    }
                });
                $('.next-see-review-image').click(function(){
                    var imgSelected = $('#another-review-image').find($('.img-evaluate.img-evaluate-selected'));
    
                    // hình ảnh phía sau
                    var nextImg = imgSelected.next();
    
                    // đang ở hình ảnh đầu tiên => hiển thị hình cuối cùng
                    if(nextImg.length == 0){
                        $('#another-review-image').children().first().click();
                    } else {
                        nextImg.click();
                    }
                });
    
                // so sánh
                $('.compare-btn').off('click').click(function(){
                    var currentName = window.location.pathname.split('/')[window.location.pathname.split('/').length - 1];
                    var compareName = $(this).attr('id').split('_')[1];
    
                    var redirectPage =`/sosanh/${currentName}vs${compareName}`;
    
                    location.href = redirectPage;
                });
    
                // thích bình luận
                $(document).on('click', '.like-comment', function(){
                    var id_dg = $(this).data('id');
    
                    clearTimeout(timer);
                    $('#comment-toast').remove();
    
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-like-comment',
                        type: 'POST',
                        data: {'id_dg': id_dg},
                        success:function(data){
                            if(data['status'] == 'like success'){
                                // thay đổi nút
                                $(`.like-comment[data-id="${id_dg}"]`).addClass('liked-comment');
                                $('#like-icon').removeClass('fal fa-thumbs-up').addClass('fas fa-thumbs-up');
    
                                // tăng lượt thích
                                var qty = parseInt($(`.qty-like-comment[data-id="${id_dg}"]`).text());
                                $(`.qty-like-comment[data-id="${id_dg}"]`).text(++qty);
                            }
                            // bỏ thích
                            else {
                                // thay đổi nút
                                $(`.like-comment[data-id="${id_dg}"]`).removeClass('liked-comment');
                                $('#like-icon').removeClass('fas fa-thumbs-up').addClass('fal fa-thumbs-up');
    
                                // giảm lượt thích
                                var qty = parseInt($(`.qty-like-comment[data-id="${id_dg}"]`).text());
                                $(`.qty-like-comment[data-id="${id_dg}"]`).text(--qty);
                            }
                        },
                    });
                });
    
                // phản hồi
                $(document).on('click', '.reply-btn', function(){
                    var id_dg = $(this).data('id');
                    setTimeout(() => {
                        $(`.reply-div[data-id="${id_dg}"]`).toggle('blind');
                    }, 200);
    
                    $('.reply-div').each(function(){
                        if($(this).data('id') != id_dg){
                            $(this).hide();
                        }
                    })
                    $('[name="reply-content"]').val('');
                    removeRequried($('[name="reply-content"]'));
                });
    
                // hủy phản hồi
                $(document).on('click', '.cancel-reply', function(){
                    var id_dg = $(this).data('id');
    
                    $(`.reply-div[data-id="${id_dg}"]`).hide('blind');
    
                    // xóa nội dung
                    $(`#reply-content-${id_dg}`).val('');
                });
    
                // gửi phản hồi
                $(document).on('click', '.send-reply', function(){
                    var id_dg = $(this).data('id');
    
                    // kiểm tra chưa nhập
                    var replyContent = $(`#reply-content-${id_dg}`);
                    if(replyContent.val().length == 0){
                        replyContent.addClass('required');
                        replyContent.after('<span class="required-text">Vui lòng nhập câu trả lời</span>');
                        return;
                    }
    
                    $('.loader').show();
                    if(params.has('danhgia')){
                        params.delete('danhgia');
                        window.location.hash = ''; // for older browsers, leaves a # behind
                        history.pushState('', document.title, window.location.pathname); // nice and clean
                    }
    
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-reply',
                        type: 'POST',
                        data: {'id_dg': id_dg, 'replyContent': replyContent.val(), 'id_dg': id_dg},
                        success:function(){
                            sessionStorage.setItem('toast-message', 'Đã trả lời đánh giá');
                            location.reload();
                        }
                    });
                });
                
                $('[name="reply-content"]').keyup(function(){
                    if($(this).hasClass('required')){
                        removeRequried($(this));
                    }
                });
    
                $('#list-comment').bind('DOMSubtreeModified', function() {
                    $('[name="reply-content"]').keyup(function(){
                        if($(this).hasClass('required')){
                            removeRequried($(this));
                        }
                    }); 
                })
    
                // xem thêm phản hồi
                $(document).on('click', '.see-more-reply', function(){
                    var id_dg = $(this).data('id');
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'ajax-get-all-reply',
                        type: 'POST',
                        data: {'id_dg': id_dg},
                        success: function (data) {
                            let html = ''
                            $.each(data, (i, val) => {
                                html +=
                                    `
                                    <div class="d-flex mb-20">
                                        <img src="${val.taikhoan.anhdaidien}" alt="" width="40px" height="40px" class="circle-img">
                                        <div class="reply-content-div ml-10">
                                            <div class="d-flex align-items-center">
                                                <b>${val.taikhoan.hoten}</b>
                                                <div class="ml-10 mr-10 fz-6 gray-1"><i class="fas fa-circle"></i></div>
                                                <div class="gray-1">${val.thoigian}</div>
                                            </div>
                                            <div class="mt-5">${val.noidung}</div>
                                        </div>
                                    </div>
                                    `
                            })
    
                            $(`.all-reply[data-id="${id_dg}"]`).append(html);
                            $(`.see-more-reply[data-id="${id_dg}"]`).remove();
                        }
                    })
                });
    
                /*=======================================================
                                    chỉnh sửa đánh giá
                =========================================================*/

                // chỉnh sửa đánh giá
                $('.edit-evaluate').off('click').click(function(){
                    var id_dg = $(this).data('id');
                    $('#evaluate_id').val(id_dg);
    
                    // gán sao
                    var star = $(`#evaluate-rating-${id_dg}`).val();
                    $('#edit_star_rating').val(star);
                    for(var i = 1; i <= star; i++){
                        $(`.edit-star-rating[data-id="${i}"]`).css('color' , 'orange');
                    }
    
                    // gán nội dung
                    var content = $('#evaluate-content-' + id_dg).val();
                    $('#edit_evaluate_content').val(content);
    
                    // số lượng hình
                    $('.edit-qty-img-inp').val($(`img[data-evaluate="${id_dg}"]`).length);
                    $('.edit-qty-img').show();
                    var qty_img = $('.edit-qty-img-inp').val();
    
                    // hiển thị div chứa hình ảnh
                    $('.edit-evaluate-img-div').css({
                        'display': 'flex',
                    });
    
                    // load hình ảnh
                    $(`img[data-evaluate="${id_dg}"]`).each(function(i) {
                        const src = $(this).attr('src')
                        // hình đánh giá
                        var imageEvaluate = renderEvaluateImage('edit', i, src)
                        $('.edit-evaluate-img-div').append(imageEvaluate);
                    })
    
                    $('.edit-qty-img-inp').val(qty_img);
    
                    // hiển thị số lượng hình ảnh đang có
                    $('.edit-qty-img').html(`(${qty_img})`);
    
                    // hiển thị modal
                    $('#edit-evaluate-modal').modal('show');
                });
                
                // reset modal
                $('#edit-evaluate-modal').on('hidden.bs.modal', function(){
                    arrayBase64 = [];
                    $('.edit-evaluate-img-div').children().remove();
                    $('.edit-star-rating').removeAttr('style');
                });
    
                // đánh giá sao sản phẩm
                $('.edit-star-rating').hover(
                    // mouse enter
                    function(){
                        $('.edit-star-rating').removeAttr('style');
                        var star = $(this).data('id');
                        for(var i = 1; i <= star; i++){
                            $(`.edit-star-rating[data-id="${i}"]`).css('color' , 'orange');
                        }
                    }, 
                    // mouse leave
                    function(){
                        $('.edit-star-rating').removeAttr('style');
                        var star = $('#edit_star_rating').val();
                        if(star != 0){
                            for(var i = 1; i <= star; i++){
                                $(`.edit-star-rating[data-id="${i}"]`).css('color' , 'orange');
                            }
                        } else {
                            $('.edit-star-rating').removeAttr('style');
                        }
                    }
                );
    
                // chọn sao
                $('.edit-star-rating').off('click').click(function(){
                    var star = $(this).data('id');
                    $('#edit_star_rating').val(star);
    
                    $('.edit-star-rating').removeAttr('style');
    
                    for(var i = 1; i <= star; i++){
                        $(`.edit-star-rating[data-id="${i}"]`).css('color' , 'orange');
                    }
                });
    
                $('#edit-btn-photo-attached').click(function(){
                    $('.edit-upload-evaluate-image').trigger('click');
                });
    
                // thêm hình đánh giá
                $('.edit-upload-evaluate-image').change(function(){
                    //số lượng hình upload
                    var count = this.files.length;
    
                    // tổng số lượng hình hiện tại
                    var qty_img = parseInt($('.edit-qty-img-inp').val());
    
                    // nếu không chọn hình nào thì thoát 
                    if(count == 0){
                        return;
                    }
    
                    // số lượng hình
                    $('.edit-qty-img').show();
    
                    // hiển thị div chứa hình ảnh
                    $('.edit-evaluate-img-div').css({
                        'display': 'flex',
                    });

                    const timestamp = new Date().getTime()
    
                    // tạo thẻ div, nút xóa, hình đánh giá
                    $.each(this.files, (i, image) => {
                        // kiểm tra file hình
                        var fileName = image.name.split('.');
                        var extend = fileName[fileName.length - 1];
    
                        if(extend === 'jpg' || extend === 'jpeg' || extend === 'png'){
                            // nếu số lượng hình upload > 3 thì hiển thị modal thông báo
                            if(qty_img >= 3){
                                showAlertTop('Bạn chỉ được phép chọn 3 ảnh đính kèm');
                                return false
                            }

                            // Byte => MB
                            const size = (image.size / BYTE) / BYTE
                            if(size > MAX_SIZE_IMAGE) {
                                showAlertTop(maxSizeImagMessage)
                                return false
                            }
    
                            var id = timestamp + i
    
                            // hình đánh giá
                            const imgURL = URL.createObjectURL(image)
                            var imageEvaluate = renderEvaluateImage('edit', id, imgURL)
                            $('.edit-evaluate-img-div').append(imageEvaluate);
    
                            qty_img++;
                        } else {
                            showAlertTop('Bạn chỉ có thể upload hình ảnh');
                            return false
                        }
                    })
    
                    $('.edit-qty-img-inp').val(qty_img);
    
                    // hiển thị số lượng hình ảnh đang có
                    $('.edit-qty-img').html(`(${qty_img})`);

                    $(this).val('')
                });
    
                // gửi đánh giá chỉnh sửa
                $('#edit-send-evaluate-btn').click(function(){
                    submitEditEvaluate()
                });

                async function submitEditEvaluate() {
                    $('.loader').fadeIn();

                    // mảng base64 string hình đánh giá
                    let evaluateImage = [];

                    // chuyển hình => base64
                    if($('.edit-evaluate-img-div').children().length) {
                        await pushBase64ToArray($('.img-rating img'))
                            .then(base64List => evaluateImage = base64List)
                    }

                    const id_dg = $('#evaluate_id').val()
                    const evaluateStarRating = $('#edit_star_rating').val()
                    const evaluateContent = $('#edit_evaluate_content').val()

                    const data = {
                        id_dg,
                        evaluateStarRating,
                        evaluateContent,
                        evaluateImage
                    }

                    const length = evaluateImage.length - 1

                    // không có ảnh đính kèm
                    if(length === -1) {
                        return editEvaluate(data)
                    } else {
                        $.each(evaluateImage, (i, base64) => {
                            // upload từng hình
                            uploadSingleImageEvaluate(base64, i)
                                .then(() => {
                                    // khi đã upload tất cả hình xong thì tạo đánh giá
                                    if(i === length) {
                                        return editEvaluate(data)
                                    }
                                })
                                .catch(() => {
                                    $('.loader').fadeOut()
                                    showToast(errorMessage)
                                })
                        })
                    }
                }

                function editEvaluate(data) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: '/ajax-edit-evaluate',
                        type: 'POST',
                        data: data,
                        success:function(data){
                            sessionStorage.setItem("toast-message", data.message);
                            location.reload();
                        },
                        error: function() {
                            $('.loader').fadeOut();
                            showAlertTop(errorMessage)
                        }
                    });
                }
    
                // xóa đánh giá
                $('.delete-evaluate').off('click').click(function(){
                    $('#delete-content').text('Bạn muốn xóa đánh giá này?')
                    $('#delete-btn').attr('data-object', 'evaluate');
                    $('#delete-btn').attr('data-id', $(this).data('id'));
                    $('#delete-modal').modal('show');
                });

                // xóa từng hình
                $(document).on('click', '.btn-remove-single-img', function(){
                    const type = $(this).attr('data-type')
                    const id = $(this).attr('data-id')

                    if(type === 'create') {
                        // xóa hình
                        $(`#img-rating-${id}`).remove()
    
                        // tổng số lượng hình hiện tại
                        var qty_img = parseInt($('.qty-img-inp').val());
    
                        // số lượng hình -1
                        qty_img--;
    
                        // cập nhật hiển thị số lượng
                        $('.qty-img').html(`(${qty_img})`);
                        $('.qty-img-inp').val(qty_img);
    
                        // ẩn thẻ hiển thị hình ảnh
                        if($('.qty-img-inp').val() == 0){
                            $('.evaluate-img-div').children().remove();
                            $('.qty-img').hide();
                        }
                    } else {
                        // xóa hình
                        $(`#edit-img-rating-${id}`).remove()
    
                        // tổng số lượng hình hiện tại
                        var qty_img = parseInt($('.edit-qty-img-inp').val());
    
                        // số lượng hình -1
                        qty_img--;
    
                        // cập nhật hiển thị số lượng
                        $('.edit-qty-img').html(`(${qty_img})`);
                        $('.edit-qty-img-inp').val(qty_img);
    
                        // ẩn thẻ hiển thị hình ảnh
                        if($('.edit-qty-img-inp').val() == 0){
                            $('.edit-evaluate-img-div').children().remove();
                            $('.edit-qty-img').hide();
                        }
                    }
                });

                function renderEvaluateImage(type, id, src) {
                    let element = ''

                    if(type === 'create') {
                        element = 
                            `<div id="img-rating-${id}" class="img-rating">
                                <div class="img-rating-overlay"></div>
                                <div data-id="${id}" data-type="create"
                                    class="btn-remove-single-img"><i class="fal fa-times-circle fz-30"></i></div>
                                <img class="w-100" src="${src}">
                            </div>`
                    } else {
                        element = 
                            `<div id="edit-img-rating-${id}" class="img-rating">
                                <div class="img-rating-overlay"></div>
                                <div data-id="${id}" data-type="edit"
                                    class="btn-remove-single-img"><i class="fal fa-times-circle fz-30"></i></div>
                                <img class="w-100" src="${src}">
                            </div>`
                    }

                    return element
                }
            }

            break
        }
        /*============================================================================================================
                                                        Giỏ hàng
        ==============================================================================================================*/
        case 'giohang': {
            if(navigation == 'back_forward'){
                location.reload();
                return;
            }

            // đánh dấu sẵn checkbox chọn tất cả
            const totalItem = $('#lst-cart-item').children().length
            const totalChecked = $('.cus-checkbox-checked').length

            if(totalItem === totalChecked) {
                $('.cart-header [data-id="all"]').addClass('cus-checkbox-checked')
            }
            
            let checkoutList = []
            let outOfStockList = []
    
            provisionalAndTotalOrder()
            
            // chọn hoặc bỏ sản phẩm thanh toán
            $('.select-item-cart').off('click').on('click', function() {
                const id = $(this).attr('data-id')
    
                if(id === 'all') {
                    if($(this).hasClass('cus-checkbox-checked')) {
                        $('.select-item-cart').removeClass('cus-checkbox-checked');
                    } else {
                        $('.select-item-cart').addClass('cus-checkbox-checked');
                    }
                } else {
                    const checkAllElement = $('.select-item-cart[data-id="all"]')
                    if(checkAllElement.hasClass('cus-checkbox-checked')) {
                        checkAllElement.removeClass('cus-checkbox-checked')
                    }
    
                    $(this).toggleClass('cus-checkbox-checked')
                }
    
                provisionalAndTotalOrder()
            })
            
            // modal xác nhận xóa giỏ hàng
            $('.remove-all-cart').click(function(){
                $('#delete-content').text('Xóa tất cả sản phẩm trong giỏ hàng?')
                $('#delete-btn').attr('data-object', 'all-cart');
                $('#delete-modal').modal('show');
            });
    
            // modal xác nhận xóa 1 sản phẩm trong giỏ hàng
            $('.remove-cart-item').click(function(){
                $('#delete-content').text('Bạn có muốn xóa sản phẩm này?')
                $('#delete-btn').attr('data-object', 'item-cart');
                $('#delete-btn').attr('data-id', $(this).data('id'));
                $('#delete-modal').modal('show');
            });
    
            // tiến hành thanh toán
            $('#checkout-page').click(function(){
                // chưa chọn sản phẩm thanh toán
                if(!$('.cus-checkbox-checked').length) {
                    showToast('Vui lòng chọn sản phẩm để thanh toán')
                    return
                }
    
                makePayments()
            });
    
            // xác nhận bỏ qua sản phẩm đã hết hàng
            $('#skip-product-btn').click(function() {
                // bỏ qua các sản phẩm đã hết hàng
                const newCheckoutList = checkoutList.filter(val => {
                    if(outOfStockList.indexOf(val) === -1) {
                        return val
                    }
                })
    
                checkoutList = newCheckoutList
                $('#skip-product-modal').modal('hide')
                checkoutQueue(checkoutList)
            })
    
            async function makePayments() {
                const isCheck = await voucherCheck()
                
                if(isCheck) {
                    getProductIdListToCheckout()
                        .then(response => {
                            checkoutList = response.checkoutList
                            outOfStockList = response.outOfStockList
        
                            // chưa chọn sản phẩm thanh toán
                            if(checkoutList.length === 0) {
                                showToast('Vui lòng chọn sản phẩm thanh toán')
                                return
                            }
        
                            // lọc sản phẩm còn hàng
                            const newCheckoutList = checkoutList.filter(val => {
                                if(outOfStockList.indexOf(val) === -1) {
                                    return val
                                }
                            })
        
                            // chọn sản phẩm hết hàng
                            if(newCheckoutList.length === 0) {
                                showToast('Bạn không thể thanh toán sản phẩm đã hết hàng')
                                return
                            }
                            // chọn sản phẩm hết hàng lẫn còn hàng
                            else if(newCheckoutList.length < checkoutList.length) {
                                $('#skip-product-modal').modal('show')
                            }
                            // chọn sản phẩm còn hàng
                            else {
                                checkoutQueue(checkoutList)
                            }
                        })
                } else {
                    location.reload()
                }
            }
    
            function getProductIdListToCheckout() {
                return new Promise(resolve => {
                    // danh sách id_sp thanh toán
                    let checkoutList = []
                    // danh sách id_sp đã hết hàng
                    let outOfStockList = []

                    $.each($('.out-of-stock'), (i, element) => {
                        outOfStockList.push($(element).attr('data-id'))
                    })
    
                    $.each($('.cus-checkbox-checked'), (i, element) => {
                        const id = $(element).attr('data-id')
    
                        if(id !== 'all') {
                            checkoutList.push(id)
                        }
                    })
    
                    const response = {
                        checkoutList,
                        outOfStockList,
                    }
    
                    resolve(response)
                })
            }
    
            function checkoutQueue(checkoutList){
                $('.loader').fadeIn();
    
                const id_tk = $('#session-user').attr('data-id')
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'ajax-checkout-queue',
                    type: 'POST',
                    data: {
                        'id_tk': id_tk,
                        'checkoutList': checkoutList
                    },
                    success: function(data){
                        console.log(data)
    
                        switch(data.status) {
                            case 'continue':
                                sessionStorage.setItem('checkoutSession', true)
                                sessionStorage.setItem('checkoutList', JSON.stringify(checkoutList))
                                window.location.href = '/thanhtoan';
                                break
                            case 'waiting':
                                $('.loader').fadeOut()
                                // xóa hàng đợi
                                removeQueue(id_tk)
                                    .then(() =>
                                        showAlertTop('Sản phẩm đang được thanh toán bởi người dùng khác, xin vui lòng chờ đến lượt'))
                                    .catch(() => showAlertTop(errorMessage))
                                break
                            case 'out of stock':
                                removeQueue(id_tk)
                                .then(() => {
                                    const message = 'Oops! Có sản phẩm đã hết hàng, bạn không thể thanh toán đơn hàng này.'
                                    sessionStorage.setItem('alert-top-message', message)
                                    location.reload()
                                })
                                break
                            case 'not enough quantity':
                                removeQueue(id_tk)
                                .then(() => {
                                    const productList = data.productList.map(product => {
                                        return (
                                            `<li>
                                                <b>${product.tensp} - ${product.mausac}</b>: <span class="red">Còn ${product.qtyInStock} sản phẩm</span>
                                            </li>`
                                        )
                                    }).join('')
        
                                    const message =
                                        `Số lượng tồn kho không đủ để thanh toán:
                                        <ul>${productList}</ul>`
                                        
                                    sessionStorage.setItem('alert-top-message', message)
                                    location.reload()
                                })
                                break
                            case 'another platform':
                                $('.loader').fadeOut()
                                showAlertTop('Đơn hàng đang được thanh toán trên ứng dụng di động')
                                break
                            case 'have been paid':
                                const message = 'Sản phẩm bạn chọn đã được thanh toán trên ứng dụng di động'
                                sessionStorage.setItem('alert-top-message', message)
                                location.reload()
                                break
                        }
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage);
                    }
                });
            }

            break
        }
        /*============================================================================================================
                                                        Thanh toán
        ==============================================================================================================*/
        case 'thanhtoan': {
            sessionStorage.setItem('checkoutQueueFlag', true);

            if(navigation === "back_forward"){
                location.reload();
                return;
            }

            /**
             * người dùng gõ link truy cập thẳng tới trang thanh toán
             * sẽ bị chặn lại vào yêu cầu phải từ trang giỏ hàng truy cập đến
             */
            if(!sessionStorage.getItem('checkoutSession')) {
                sessionStorage.setItem('toast-message', 'Phiên thanh toán đã hết hạn')
                window.location.href = '/giohang'
                return
            }
    
            // render giỏ hàng
            const idList = JSON.parse(sessionStorage.getItem('checkoutList'))

            getCartByIdProduct(idList)
                .then(data => renderCart(data))
                .catch(() => showToast('Đã xảy ra lỗi khi hiển thị giỏ hàng'))
            
            // thời gian thanh toán
            let minute = 10
            let second = 0
    
            if(sessionStorage.getItem('minute')){
                minute = parseInt(sessionStorage.getItem('minute'));
                // trừ 2s khi load trang
                second = parseInt(sessionStorage.getItem('second') - 2);
                if(second < 0 && minute){
                    second = 59;
                    minute--;
                }   
            }
    
            checkoutTimeout(minute, second)
                // hết thời gian thanh toán
                .then(() => {
                    sessionStorage.setItem('alert-top-message', 'Đã hết thời gian thanh toán.');
                    location.href = '/giohang';
                })
                .catch(() => showAlertTop(errorMessage))
    
            /*=============================================================================
                                            nhận tại nhà
            ===============================================================================*/
            if($('input[name="receive-method"]').is(':checked')){
                var method = $('input[name="receive-method"]:checked').val();
                if(method == 'Giao hàng tận nơi'){
                    $('.atStore').css('display', 'none');
                    $('.atHome').css('display', 'block');
                } else {
                    $('.atStore').css('display', 'block');
                    $('.atHome').css('display', 'none');
                }
            }
    
            $('#TaiCuaHang').on('click', function(){
                removeRequried($('.atHome'))

                $('.atStore').css('display', 'block');
                $('.atHome').css('display', 'none');
    
                // hiển thị danh sách chi nhánh theo tỉnh thành
                const id_tt = $('#at-store-select').val()
    
                showBranchByAreaId(id_tt)
            });
    
            $('#TaiNha').on('click', function(){
                removeRequried($('.list-branch'))

                $('.atHome').css('display', 'block');
                $('.atStore').css('display', 'none');
            });
    
            // thay đổi địa chỉ giao hàng
            $('#btn-change-address-delivery').click(function(){
                setTimeout(() => {
                    location.href = "diachigiaohang";
                }, 200);
    
                removeQueueFlag = false;
            });
    
            /*=============================================================================
                                      nhận tại cửa hàng
            ===============================================================================*/
    
            // kiểm tra còn hàng tại chi nhánh không
            $('input[name="branch"]').change(function(){
                removeRequried($('.list-branch'));
    
                const id = $(this).val();
                const idList = JSON.parse(sessionStorage.getItem('checkoutList'))
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/ajax-check-qty-in-stock-branch',
                    data: {
                        id,
                        idList
                    },
                    type: 'POST',
                    success:function(data){
                        $('.info-qty-in-stock').children().remove();
                        $('.info-qty-in-stock').attr('is-checkout', 'true')
    
                        const productList = data.productList

                        let outOfStock = ''
                        let qtyIsNotEnough = ''
                        let inStocks = ''
    
                        $.each(productList, (i, val) => {
                            if(val.trangthai === 'out of stock') {
                                outOfStock +=
                                    `<div class="d-flex red pb-10 fz-14">
                                        <img src="images/phone/${val.hinhanh}" width="80px">
                                        <div class="ml-5">
                                            <div>${val.tensp}</div>
                                            <div>Màu sắc: ${val.mausac}</div>
                                            <div class="d-flex align-items-center">
                                                <b class="mr-10">Tạm hết hàng</b>
                                                <div type="button" data-id="${val.id}" class="checkout-delete-item-cart">Bỏ chọn</div>
                                            </div>
                                        </div>
                                    </div>`
                            } else if(val.trangthai === 'not enough') {
                                qtyIsNotEnough +=
                                    `<div class="d-flex red pb-10 fz-14">
                                        <img src="images/phone/${val.hinhanh}" width="80px">
                                        <div class="ml-5">
                                            <div>${val.tensp}</div>
                                            <div>Màu sắc: ${val.mausac}</div>
                                            <div>
                                                <b class="mr-10">Còn ${val.slton} sản phẩm</b>
                                                <span type="button" data-id="${val.id}" class="checkout-delete-item-cart">Bỏ chọn</span>
                                            </div>
                                        </div>
                                    </div>`
                            } else {
                                inStocks +=
                                    `<div class="d-flex pb-10 fz-14">
                                        <img src="images/phone/${val.hinhanh}" width="80px">
                                        <div class="ml-5">
                                            <div>${val.tensp}</div>
                                            <div>Màu sắc: ${val.mausac}</div>
                                            <div class="d-flex align-items-center">
                                                <b class="mr-10">Còn hàng</b>
                                                <i class="fas fa-check-circle success-color"></i>
                                            </div>
                                        </div>
                                    </div>`
                            }
                        })
    
                        const html = outOfStock + qtyIsNotEnough + inStocks
                        $('.info-qty-in-stock').append(html)
    
                        if(!data.status) {
                            $('.info-qty-in-stock').attr('is-checkout', 'false')
                        }
    
                        $('.info-qty-in-stock').show('blind')
                    },
                    error: function() {
                        showToast('Đã có lỗi xảy ra khi lấy dữ liệu, Vui lòng làm mới lại trang')
                    }
                });
            });
    
            // thay đổi khu vực
            $('#at-store-select').change(function() {
                const id_tt = $(this).val()
                showBranchByAreaId(id_tt)
            })
    
            // xóa sản phẩm đã hết hàng tại chi nhánh
            $(document).on('click', '.checkout-delete-item-cart', function(){
                $('.loader').fadeIn()
    
                const id = $(this).attr('data-id')
                const idxOfId = idList.indexOf(id)
                idList.splice(idxOfId, 1)
    
                // nếu không còn sản phẩm trong giỏ hàng thanh toán
                if(idList.length === 0) {
                    // redirect về giỏ hàng  
                    sessionStorage.setItem('alert-top-message', 'Không có sản phẩm nào để thanh toán');
                    window.location.href = '/giohang'
                    return
                } else {
                    sessionStorage.setItem('checkoutList', JSON.stringify(idList))
                    sessionStorage.setItem('toast-message', 'Đã bỏ chọn sản phẩm')
                }
                
                location.reload()
            });
    
            function showBranchByAreaId(id_tt) {
                $('.single-branch').each(function() {
                    if($(this).attr('data-area') === id_tt) {
                        $(this).show()
                    } else {
                        $(this).hide()
                    }
                })
    
                $('.list-branch').show()
    
                const infoQtyInstock = $('.info-qty-in-stock')
                infoQtyInstock.hide('blind')
                infoQtyInstock.children().remove();
                infoQtyInstock.attr('is-checkout', 'true')
    
                $('input[name="branch"]').prop('checked', false)
            }
    
            /*===============================================================================
            =================================================================================*/
    
            // xác nhận thanh toán
            $('#btn-confirm-checkout').click(function(){
                confirmCheckout()
            });
    
            async function confirmCheckout() {
                const isCheck = await voucherCheck()
                
                if(isCheck) {
                    // hình thức nhận hàng
                    var receciveMethod = $('input[name="receive-method"]:checked').val();
    
                    if(receciveMethod === 'Giao hàng tận nơi'){
                        // chưa có địa chỉ giao hàng
                        if($('.atHome').data('flag') == 0){
                            $('.atHome').addClass('required');
                            var required = $('<span class="required-text">Bạn chưa có địa chỉ giao hàng</span>');
                            $('.atHome').after(required);
                        } else {
                            // ngừng thời gian thanh toán
                            clearInterval(checkoutTimer);
                            // hình thức nhận hàng
                            $('#receciveMethod').val('Giao hàng tận nơi');
        
                            checkout();
                        }
                    }
                    // nhận tại cửa hàng
                    else {
                        // chưa chọn chi nhánh
                        if(!$('input[name="branch"]:checked').val()) {
                            $(window).scrollTop(0);
    
                            if(!$('.list-branch').hasClass('required')) {
                                $('.list-branch').addClass('required')
                                const requiredText =
                                    `<span class="required-text">Vui lòng chọn chi nhánh</span>`
                                $('.list-branch').after(requiredText)
                            }
                            return
                        }
    
                        // có sản phẩm đã hết hàng hoặc slton kho không đủ để thanh toán
                        if($('.info-qty-in-stock').attr('is-checkout') === 'false') {
                            $(window).scrollTop(0);
                            showAlertTop('Bạn không thể thanh toán khi có sản phẩm đã hết hàng hoặc số lượng không đủ để thanh toán.');
                            return
                        }
    
                        $('.loader').show();
    
                        // ngừng thời gian thanh toán
                        clearInterval(checkoutTimer);
        
                        // hình thức nhận hàng
                        $('#receciveMethod').val('Nhận tại cửa hàng');
                        $('#id_cn').val($('input[name="branch"]:checked').attr('id').split('-')[1]);
    
                        checkout();
                    }
                } else {
                    location.reload()
                }
            }
    
            function checkout(){
                sessionStorage.removeItem('checkoutQueueFlag')
                let total = 0
                // tổng tiền khi có áp dụng voucher
                if($('#total').attr('data-new-total')) {
                    total = $('#total').attr('data-new-total')
                }
                // tổng tiền không có áp dụng voucher
                else {
                    total = $('#total').attr('data-total')
                }
    
                $('#cartTotal').val(total);
    
                // phương thức thanh toán
                var paymentMethod = $('input[name="payment-method"]:checked').val();
                
                // thanh toán khi nhận hàng | zalo pay
                paymentMethod == 'cash' ? $('#paymentMethod').val('cash') : $('#paymentMethod').val('zalo-pay');
    
                // danh sách id_sp thanh toán
                const idList = JSON.parse(sessionStorage.getItem('checkoutList'))
                $('#id_sp_list').val(idList)
    
                $('#checkout-form').submit();
            }
        
            // đảo ngược button đóng/ mở giỏ hàng trang Thanh toán
            $('.checkout-btn-collapse-cart').on('click', function(){
                if($(this).attr('aria-expanded') == 'true'){
                    $(this).css({
                        '-ms-transform' : 'rotate(0)',
                        'transform' : 'rotate(0)',
                    });
                } else {
                    $(this).css({
                        '-ms-transform' : 'rotate(180deg)',
                        'transform' : 'rotate(180deg)',
                    });
                }
            });

            break
        }
        /*============================================================================================================
                                                    Địa chỉ giao hàng
        ==============================================================================================================*/
        case 'diachigiaohang': {
            if(navigation == 'back_forward'){
                location.reload();
                return;
            }
            // chọn địa chỉ giao hàng
            $('.choose-address-delivery').click(function(){
                removeQueueFlag = false;
    
                $('#address_id').val($(this).data('id'));
                $('#change-address-delivery-form').submit();
            });
    
            minute = parseInt(sessionStorage.getItem('minute'));
            // trừ 2s khi load trang
            second = parseInt(sessionStorage.getItem('second') - 2);
            if(second < 0 && minute){
                second = 59;
                minute--;
            }
            
            checkoutTimeout(minute, second)
                // hết thời gian thanh toán
                .then(() => {
                    sessionStorage.setItem('alert-top-message', 'Đã hết thời gian thanh toán.');
                    location.href = '/giohang';
                })
                .catch(() => showAlertTop(errorMessage))

            break
        }
        /*============================================================================================================
                                                        So sánh
        ==============================================================================================================*/
        case 'sosanh': {
            if(window.innerWidth <= 992){
                $('.thirdProduct').hide();
                $('#blank-td').removeClass('w-25').addClass('w-20');
                $('#currentProduct').removeClass('w-25').addClass('w-40');
                $('#compareProduct').removeClass('w-25').addClass('w-40');
                $('td[colspan="4"]').attr('colspan', '3');
            }
            // tìm kiếm điện thoại để so sánh
            $('#compare-search-phone').keyup(function(){
                var data = $(this).val();
                if(data == ''){
                    setTimeout(() => {
                        $('.compare-list-search-phone').children().remove();
                    }, 500);
                    $('.compare-list-search-phone').hide('blind');
                    
                } else{
                    // <div class='compare-single-phone'>iPhone</div>
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        var val = $(this).val().toLowerCase().trim();
                        if(val == ''){
                            return;
                        }
    
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: '/ajax-search-phone',
                            type: 'POST',
                            cache: false,
                            data: {'str': val},
                            success:function(data){
                                $('.compare-list-search-phone').children().remove();
                                if(data.phoneList.length === 0){
                                    $('.compare-list-search-phone').hide('blind');
                                    return;
                                }
    
                                let phone = '';
                                let infoComponent = ''

                                $.each(data.phoneList, function(key, val){
                                    // ngừng kinh doanh
                                    if(val.modelStatus === false) {
                                        infoComponent =
                                            `<b>${val.tensp}</b>
                                            <div class="d-flex align-items-center mt-5">
                                                <b class="red">NGỪNG KINH DOANH</b>
                                            </div>`
                                    }
                                    // hàng sắp về
                                    else if(val.comingSoon === true) {
                                        infoComponent =
                                            `<div class="d-flex">
                                                <b>${val.tensp}</b>
                                                <b class="red ml-10">HÀNG SẮP VỀ</b>
                                            </div>
                                            <div class="d-flex align-items-center mt-5">
                                                <span class="red fw-600">${numberWithDot(val.giakhuyenmai)}<sup>đ</sup></span>
                                                ${val.khuyenmai ?
                                                    `<span class="text-strike ml-10">${numberWithDot(val.gia)}<sup>đ</sup></span>
                                                    <span class="red ml-10">-${val.khuyenmai * 100}%</span>` : ''
                                                }
                                            </div>`
                                    }
                                    // tạm hết hàng
                                    else if(val.qtyInStock === false) {
                                        infoComponent =
                                            `<b>${val.tensp}</b>
                                            <div class="d-flex align-items-center mt-5">
                                                <b class="red">TẠM HẾT HÀNG</b>
                                            </div>`
                                    }
                                    else {
                                        infoComponent =
                                            `<b>${val.tensp}</b>
                                            <div class="d-flex align-items-center mt-5">
                                                <span class="red fw-600">${numberWithDot(val.giakhuyenmai)}<sup>đ</sup></span>
                                                ${val.khuyenmai ?
                                                    `<span class="text-strike ml-10">${numberWithDot(val.gia)}<sup>đ</sup></span>
                                                    <span class="red ml-10">-${val.khuyenmai * 100}%</span>` : ''
                                                }
                                            </div>`
                                    }

                                    phone +=
                                        `<div type="button" data-name="${val.tensp_url}" class="head-single-result">
                                            <div class="d-flex">
                                                <div class="w-25 p-10">
                                                    <img src="${data.url_phone + val.hinhanh}" alt="">
                                                </div>
                                                <div class="d-flex flex-column w-75 p-10">
                                                    ${infoComponent}
                                                </div>
                                            </div>
                                        </div>`
                                });

                                $('.compare-list-search-phone').append(phone);
                                $('.compare-list-search-phone').scrollTop(0);
                                $('.compare-list-search-phone').show('blind');
                            },
                            error: function() {
                                showToast(errorMessage)
                            }
                        });
                    }, 300);
                }
            });
    
            // thêm điện thoại để so sánh
            $(document).on('click', '.head-single-result', function(){
                var order = $('.compare-btn-add-phone').attr('data-order');
                if(order == 2){
                    var url = childPage;
                    var currentName = url.split('vs')[0];
                    var compareName = $(this).data('name');
    
                    location.href = `sosanh/${currentName}vs${compareName}`;
                } else {
                    var url = childPage;
                    var currentName = url.split('vs')[0];
                    var compareName = url.split('vs')[1];
                    var thirdName = $(this).data('name');

                    location.href = `sosanh/${currentName}vs${compareName}vs${thirdName}`;
                }
            });
    
            // nút xem so sánh cấu hình chi tiết
            $('.compare-btn-see-detail').click(function(){
                $(this).css('display', 'none');
                $('.compare-detail').css('display', 'table-row');
            });
    
            // xóa điện thoại so sánh
            $('.delete-compare-btn').off('click').click(function(){
                // thứ tự điện thoại
                var order = $(this).data('order');
    
                // nếu xóa dt thứ 2 mà đt thứ 3 k có thì hiển thị modal chọn đt
                if(order == 2 && $('.delete-compare-btn[data-order="'+(order + 1)+'"]').length == 0){
                    $('.compare-btn-add-phone').attr('data-order', order);
                    $('#compare-modal').modal('show');
                }
                // xóa đt theo order ở url
                else {
                    var url = childPage;
                    var lst_urlName = url.split('vs');
                    lst_urlName.splice(order - 1, 1);
                    location.href = 'sosanh/' + lst_urlName[0] +'vs' + lst_urlName[1];
                }
            });
    
            // mdoal thêm điện thoại để so sánh
            $('.compare-btn-add-phone').click(function(){
                $('#compare-modal').modal('show');
            });
    
            // thêm giỏ hàng
            $('.compare-add-card').off('click').click(function(){
                var id_sp = $(this).attr('data-id');
                chooseColor(id_sp);
            });

            $('#compare-modal').on('shown.bs.modal', function() {
                $('#compare-search-phone').focus()
            })

            break
        }
        /*============================================================================================================
                                                        Tra cứu
        ==============================================================================================================*/
        case 'tracuu': {
            $('#imei-inp').keyup(function(){
                removeRequried($(this));
            });
        
            // tra cứu imei
            $('#btn-check-imei').click(function(){
                var IMEI = $('#imei-inp').val().trim();
                var required;
        
                // nếu chưa nhập IMEI
                if(IMEI.length === 0){
                    required = $('<div class="required-text text-center">Số IMEI không được bỏ trống</div>');
                    $('#imei-inp').addClass('required');
                    $('#imei-inp').after(required);
                    return;
                }

                $('.loader').fadeIn()
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/ajax-check-imei',
                    type: 'POST',
                    data: {'imei': IMEI},
                    success:function(data){
                        $('.loader').fadeOut()
                        // imei không hợp lệ
                        if(data.status === 'invalid imei'){
                            required = $('<div class="required-text text-center">Số IMEI không hợp lệ</div>');
                            $('#imei-inp').addClass('required');
                            $('#imei-inp').after(required);
                        }
                        // hợp lệ
                        else{
                            console.log(data);
                            $('#check-imei').hide();
    
                            // trạng thái bảo hành
                            var warrantyStatusText = data.product.trangthaibaohanh ?
                                '<b class="success-color ml-10">Trong bảo hành</b>' :
                                '<b class="warning-color ml-10">Hết hạn bảo hành</b>'
                                
                            const product = data.product
                            
                            var elmnt = `<div class="row">
                                            <div class="col-lg-8 mx-auto">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-12 mb-40">
                                                        <img src="images/phone/${product.hinhanh}" alt="" class="w-80 center-img">
                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                            <div class="fz-26 fw-600 pt-20 pb-20">${product.tensp}</div>
                                                            <div class="d-flex pb-10">
                                                                <div>Màu sắc: <b>${product.mausac}</b></div>
                                                                <div class="ml-20">Ram: <b>${product.ram}</b></div>
                                                            </div>
                                                            <div class="d-flex mb-20">
                                                                <div>Dung lượng: <b> ${product.dungluong}</b></div>
                                                                <div class="ml-20">IMEI: <b>${IMEI}</b></div>
                                                            </div>
                                                            <div id="btn-check-another-imei" class="main-color-text pointer-cs">Kiểm tra số IMEI khác<i class="far fa-chevron-right ml-10"></i></div>
                                                        </div>
                                                    </div>
    
                                                    <div class="col-lg-6 col-md-6 col-12 mb-40">
                                                        <div class="fz-26 fw-600">
                                                            <i class="fas fa-shield-check mr-10"></i>Bảo hành
                                                        </div>
                                                        <div class="d-flex fz-20 mt-10">
                                                            <div>Trạng thái bảo hành:</div>
                                                            ${
                                                                product.baohanh ? (
                                                                    warrantyStatusText +
                                                                    `</div>
                                                                    <div class="mt-10">Bảo hành: <b>${product.baohanh}</b></div>
                                                                    <div class="d-flex mt-10">
                                                                        <div>Bắt đầu: <b>${product.ngaymua}</b></div>
                                                                        <div class="ml-20">Kết thúc: <b>${product.ngayketthuc}</b></div>`
                                                                ) :
                                                                '<b class="warning-color ml-10">Không bảo hành</b></div>'
                                                            }
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`
    
                            $('#valid-imei').append(elmnt)
                        }
                    },
                    error: function() {
                        $('.loader').fadeOut()
                        showAlertTop(errorMessage)
                    }
                });
            });
        
            // check imei khác
            $(document).on('click', '#btn-check-another-imei', function(){
                $('#imei-inp').val('');
                $('#check-imei').show();
                $('#valid-imei').children().remove();
                $(window).scrollTop(0);
            });

            break
        }
    }

    /*============================================================================================================
                                                        Function
    ==============================================================================================================*/

    function showIOSScreen(){
        var screen =
            `<div class="ios-screen">
                <img src="images/ios-updating.png" class="ios-image">
                ${'Website trên hệ điều hành iOS đang trong quá trình phát triển. Xin lỗi vì sự bất tiện này'}
            </div>`

        $('body').prepend(screen);
        $('body').css('overflow', 'hidden')
    }

    function iOS() {
        return [
          'iPad Simulator',
          'iPhone Simulator',
          'iPod Simulator',
          'iPad',
          'iPhone',
          'iPod'
        ].includes(navigator.platform)
        // iPad on iOS 13 detection
        || (navigator.userAgent.includes("Mac") && "ontouchend" in document)
    }

    async function welcomeBack() {
        if(!sessionStorage.getItem('firstAccess')) {
            // lấy họ tên người dùng nếu có đang nhập
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: 'ajax-get-user-fullname',
                success: function(data) {
                    const fullname = data.fullname

                    if(fullname) {
                        const message = `Chào mừng ${fullname} đã quay trở lại!`
                        showToast(message)
                    }

                    sessionStorage.setItem('firstAccess', true)
                },
                error: function(error) {
                    console.log(error)
                }
            })
        }
    }

    function isRemoveQueue() {
        const id_tk = $('#session-user').data('id')

        if(!id_tk) {
            sessionStorage.removeItem('checkoutQueueFlag')
            return
        }

        // cờ hàng đợi
        const checkoutQueueFlag = sessionStorage.getItem('checkoutQueueFlag')

        // xóa hàng đợi
        if(checkoutQueueFlag){
            removeQueue(id_tk)
                .then(() => {
                    // xóa những session đã lưu tại trang thanh toán
                    sessionStorage.removeItem('checkoutQueueFlag')
                    sessionStorage.removeItem('checkoutList')
                    sessionStorage.removeItem('checkoutSession')
                    sessionStorage.removeItem('minute')
                    sessionStorage.removeItem('second')
                })
                .catch(() => {
                    showAlertTop(errorMessage)
                })
        }
    }

    function getBackToTheOldPosition() {
        // có lọc || sắp xếp trước đó
        if(localStorage.getItem('filterSortJson')){
            // không load thêm dữ liệu khi cuộn
            loadMoreFlag = true;

            setTimeout(() => {
                $('.loader').show();

                let filterSortJson = JSON.parse(localStorage.getItem('filterSortJson'));
                localStorage.removeItem('filterSortJson');

                arrFilterSort = filterSortJson;

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/ajax-filter-product',
                    type: 'POST',
                    data: {arrFilterSort:filterSortJson},
                    success: function(data){
                        // render danh sách kết quả lọc & sắp xếp
                        loadFilterSortData(data)

                        // đánh dấu tiêu chí lọc
                        $.each(arrFilterSort.filter, function(type, arr){
                            $.each(arr, function(i, data){
                                $(`[data-type="${type}"][data-keyword="${data}"]`).addClass('filter-selected');
                            })
                        });

                        // đánh dấu tiêu chí sắp xếp
                        if(arrFilterSort.sort != ''){
                            $(`[name="sort"][value="${arrFilterSort.sort}"]`).prop('checked', true);
                        }
                        arrTemp = [];

                        // cuộn lên đầu trang
                        $(window).scrollTop(0);

                        $('.loader').fadeOut();
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showToast(errorMessage)
                    }
                });
            }, 700);
        } else if(page === 'dienthoai') {
            loadMoreFlag = true;

            const position = sessionStorage.getItem('scrollPosition') ? sessionStorage.getItem('scrollPosition') : 0;
            loadMoreRow = sessionStorage.getItem('loadMoreRow') ? parseInt(sessionStorage.getItem('loadMoreRow')) : 0;

            if(loadMoreRow === 0){
                storageFlag = true
                loadMoreFlag = false;
            } else {
                setTimeout(() => {
                    $('.loader').fadeIn()
                    
                    getMoreProduct(page, 10, loadMoreRow)
                        .then(data => {
                            var row = loadMoreRow + 10;
                            let html = renderPhoneCard(data)
                            $('#lst_product').append(html);
                            $('#lst_product').attr('data-row', row);
                        })
                        .then(() => {
                            setTimeout(() => {
                                storageFlag = true;
                                $('.loader').fadeOut()
                            }, 700)
    
                            $(window).scrollTop(position);
                            loadMoreFlag = false
                        })
                        .catch(() => {
                            $('.loader').fadeOut()
                            showToast(errorMessage)
                        })
                }, 500)
            }            
        }

    }

    function getMoreProduct(page, row, limit) {
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: 'ajax-load-more',
                type: 'POST',
                data: {
                    page,
                    row,
                    limit
                },
                success: function(data){
                    resolve(data)
                },
                error: function() {
                    reject()
                }
            });
        })
    }

    function showAlertTop(content){
        const alertTop =
            `<div class="alert-top">
                <div class="close-alert-top-icon"><i class="far fa-times-circle"></i></div>
                <div class="alert-top-title"></div>
                <div class="alert-top-content"></div>
                <div class="alert-top-footer">
                    <div class="close-alert-top">OK</div>
                </div>
            </div>`

        $('body').prepend(alertTop)

        setTimeout(() => {
            $('.alert-top').css({
                '-ms-transform': 'translateY(0)',
                'transform': 'translateY(0)',
            });
            $('.backdrop').css('z-index', '1999');
            $('.backdrop').fadeIn();
            
        }, 200);

        $('.alert-top-content').html(content);
    }
    
    function closeAlertTop(){
        setTimeout(() => {
            $('.alert-top').remove()
            $('.backdrop').removeAttr('style');
        }, 500);
        $('.alert-top').css({
            '-ms-transform': 'translateY(-500px)',
            'transform': 'translateY(-500px)',
        });
        $('.backdrop').fadeOut();
    }

    // url image => base64
    async function getBase64FromUrl(url) {
        const response = await fetch(url);
        const blob = await response.blob();
        return await new Promise(resolve => {
            let reader = new FileReader();
            reader.readAsDataURL(blob);
            reader.onloadend = () => { resolve(reader.result); };
        });
    }

    function pushBase64ToArray(imgElementList) {
        return new Promise((resolve, reject) => {
            let base64List = [];

            const length = imgElementList.length
            let url = ''
            imgElementList.each(function() {
                url = $(this).attr('src')
                getBase64FromUrl(url)
                    .then(base64 => {
                        base64List.push(base64)
                        if(base64List.length === length) {
                            resolve(base64List)
                        }
                    })
            })
        })
    }

    function getUserAddress(id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {'X-CSRF-TOKEN':X_CSRF_TOKEN},
                url: 'ajax-bind-address',
                type: 'POST',
                data: {'id': id},
                success: function(data){
                    resolve(data)
                },
                error: function() {
                    reject()
                }
            })
        })
    }

    // lấy tỉnh/ thành theo tên
    function getProvinceByName(name) {
        return new Promise((resolve, reject) => {
            let temp = ''

            if(name.includes('Tỉnh')) {
                temp = name.replace('Tỉnh', '')
            } else {
                temp = name.replace('Thành phố', '')
            }

            const newName = temp.trim()

            xhrProvince = $.ajax({
                url: `https://provinces.open-api.vn/api/p/search/?q=${newName}`,
                timeout: 10000,
                success: function(province) {
                    const result = province.filter(val => val.name === name)

                    resolve(...result)
                },
                error: function() {
                    reject()
                }
            })
        })
    }

    // lấy quận/ huyện theo tên
    function getDistrictByName(name) {
        return new Promise((resolve, reject) => {
            let temp = ''

            if(name.includes('Thành phố')) {
                temp = name.replace('Thành phố', '')
            } else if(name.includes('Quận')) {
                temp = name.replace('Quận', '')
            } else if(name.includes('Huyện')) {
                temp = name.replace('Huyện', '')
            } else if(name.includes('Thị xã')) {
                temp = name.replace('Thị xã', '')
            }

            const newName = temp.trim()

            xhrDistrict = $.ajax({
                url: `https://provinces.open-api.vn/api/d/search/?q=${newName}`,
                timeout: 10000,
                success: function(district) {
                    const result = district.filter(val => val.name === name)

                    resolve(...result)
                },
                error: function() {
                    reject()
                }
            })
        })
    }

    // lấy phường/ xã theo tên
    function getWardByName(name) {
        return new Promise((resolve, reject) => {
            let temp = ''

            if(name.includes('Phường')) {
                temp = name.replace('Phường', '')
            } else if(name.includes('Xã')) {
                temp = name.replace('Xã', '')
            } else if(name.includes('Thị trấn')) {
                temp = name.replace('Thị trấn', '')
            }

            const newName = temp.trim()

            xhrWard = $.ajax({
                url: `https://provinces.open-api.vn/api/w/search/?q=${newName}`,
                timeout: 10000,
                success: function(ward) {
                    const result = ward.filter(val => val.name === name)

                    resolve(...result)
                },
                error: function() {
                    reject()
                }
            })
        })
    }
    
    function editAddressModal(id, defaultAdr = false){
        $('.loader').fadeIn();

        getUserAddress(id)
            .then(data => {
                const address = data.diachi
                
                Promise.all([
                    getProvinceByName(data.tinhthanh),
                    getDistrictByName(data.quanhuyen),
                    getWardByName(data.phuongxa)
                ])
                .then(data => {
                    const [province, district, ward] = data

                    console.log(province)

                    $('#TinhThanh-name').text(province.name)
                    $('#TinhThanh-name').attr('data-flag', 1)

                    $('#QuanHuyen-name').text(district.name)
                    $('#QuanHuyen-name').attr('data-flag', 1)

                    $('#PhuongXa-name').text(ward.name)
                    $('#PhuongXa-name').attr('data-flag', 1)

                    // gán địa chỉ
                    $('input[name="address_inp"]').val(address);
                    // focus họ tên
                    $('[name="adr_fullname_inp"]').focus()
                    
                    Promise.all([
                        getProvince(),
                        getDistrict(province.code),
                        getWard(district.code)
                    ])
                    .then(data => {
                        $('#PhuongXa-selected').removeClass('select-disable');
                        $('#PhuongXa-selected').addClass('select-selected');
                    })
                    .catch(() => {
                        sessionStorage.setItem('toast-message', 'Không thể lấy danh sách dữ liệu. Vui lòng thử lại')
                        location.reload()
                    })
                })
                .catch(() => {
                    sessionStorage.setItem('toast-message', 'Không thể lấy danh sách dữ liệu. Vui lòng thử lại')
                    location.reload()
                })
                
                // tiêu đề modal
                $('#address-modal-title').text('Chỉnh sửa địa chỉ');
        
                if(defaultAdr == true){
                    $('#set_default_address').prop('checked', true);
                } else {
                    $('#set_default_address').prop('checked', false);
                }
        
                // thiết lập nút
                $('.address-action-btn').attr('data-type', 'edit');
                $('.address-action-btn').attr('data-id', id);
                $('.address-action-btn').text('Cập nhật');
        
                // chỉnh sửa
                $('input[name="address_type"]').val('edit');
                $('input[name="tk_dc_id"]').val(id);
        
                // gán họ tên, sdt
                $('input[name="adr_fullname_inp"]').val(data.hoten);
                $('input[name="adr_tel_inp"]').val(data.sdt);

                $('#address-modal').modal('show');
                $('.loader').fadeOut();
            })
            .catch(() => {
                $('.loader').fadeOut()
                showAlertTop('Không thể lấy dữ liệu. Vui lòng làm mới lại trang')
            })
    }

    function searchPlace(val, selectBox){
        if(val == ''){
            selectBox.children().show();    
            return;
        }

        val = val.toLowerCase();

        const children = selectBox.children()

        children.each(function() {
            const name = $(this).attr('data-name').toLowerCase()

            if(!name.includes(val)){
                $(this).hide();
            } else {
                $(this).show();
            }
        })
    }

    function choosePlace(id, name, type){
        if(type === 'TinhThanh'){
            $('#TinhThanh-name').text(name);
            $('#TinhThanh-box').hide('blind', 250)

            $('#QuanHuyen-name').text('Chọn Quận / Huyện');
            $('#QuanHuyen-name').removeAttr('data-flag');

            // spinner
            $('#QuanHuyen-selected i').replaceWith('<div class="spinner-border select-spinner" role="status"></div>')

            $('#list-phuong-xa').children().remove();
            $('#PhuongXa-name').text('Chọn Phường / Xã');
            $('#PhuongXa-name').removeAttr('data-flag');
            $('#PhuongXa-selected').addClass('select-disable').removeClass('select-selected');

            getDistrict(id)
                .then(() => {
                    $('#QuanHuyen-box').show('blind', 250);
                    $('#QuanHuyen-selected .select-spinner').replaceWith('<i class="far fa-chevron-down fz-14"></i>')
                })
                .catch(() => showAlertTop(errorMessage))

        } else if(type === 'QuanHuyen') {
            removeRequried($('#QuanHuyen-name').parent());

            $('#QuanHuyen-name').text(name);
            $('#QuanHuyen-name').attr('data-flag', '1')
            $('#QuanHuyen-box').hide('blind', 250);

            $('#PhuongXa-name').text('Chọn Phường / Xã');
            $('#PhuongXa-name').removeAttr('data-flag');

            $('#PhuongXa-selected i').replaceWith('<div class="spinner-border select-spinner" role="status"></div>');

            getWard(id)
                .then(() => {
                    $('#PhuongXa-selected').removeClass('select-disable');
                    $('#PhuongXa-selected').addClass('select-selected');
                    $('#PhuongXa-selected .select-spinner').replaceWith('<i class="far fa-chevron-down fz-14"></i>');

                    $('#PhuongXa-box').show('blind', 250);
                })
                .catch(() => showAlertTop(errorMessage))
        } else {
            removeRequried($('#PhuongXa-name').parent());

            $('#PhuongXa-name').text(name);
            $('#PhuongXa-name').attr('data-flag', '1');
            $('#PhuongXa-box').toggle('blind', 250);

            $('input[name="address_inp"]').focus();
        }
    }

    function deleteElement(id = null, object) {
        $('.loader').fadeIn()

        $.ajax({
            headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
            url: 'ajax-delete-object',
            type: 'POST',
            data: {
                id,
                object
            },
            success: function(data) {
                $('#delete-modal').modal('hide');

                switch(object) {
                    case 'address':
                        $(`#address-${id}`).remove()
                        break
                    case 'item-cart':
                        $(`.cart-item-wrapper[cart-id="${id}"]`).remove()
                        provisionalAndTotalOrder()

                        // trừ số lượng giỏ hàng
                        let qty = $('.head-qty-cart').text()
                        qty--
                        if(qty === 0) {
                            $('.head-qty-cart').hide()
                            $('.head-qty-cart').text('')
                        } else {
                            $('.head-qty-cart').text(qty)
                        }

                        $('#cart-header-qty').text(`Chọn tất cả (${qty}) sản phẩm`)

                        if(!$('#lst-cart-item').children().length) {
                            const parent = $('#cart-container')
                            parent.children().remove()

                            const emptyCartElement =
                                `<div class="col-lg-12">
                                    <div class="box-shadow">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-8 col-10 mx-auto">
                                                <div class="pt-100 pb-100 text-center">
                                                    <div class="fz-20 mb-40">Không có sản phẩm nào trong giỏ hàng.</div>
                                                    <a href="/dienthoai" class="main-btn">Tiếp tục mua hàng</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`

                            parent.append(emptyCartElement)
                        }

                        break
                    case 'all-cart':
                        // trừ số lượng giỏ hàng
                        $('.head-qty-cart').hide()
                        $('.head-qty-cart').text('')

                        const parent = $('#cart-container')
                        parent.children().remove()

                        const emptyCartElement =
                            `<div class="col-lg-12">
                                <div class="box-shadow">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-8 col-10 mx-auto">
                                            <div class="pt-100 pb-100 text-center">
                                                <div class="fz-20 mb-40">Không có sản phẩm nào trong giỏ hàng.</div>
                                                <a href="/dienthoai" class="main-btn">Tiếp tục mua hàng</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`

                        parent.append(emptyCartElement)

                        break
                    case 'order':
                        sessionStorage.setItem('toast-message', data.message)
                        location.reload()
                        return
                    case 'evaluate':
                        sessionStorage.setItem('toast-message', data.message)
                        location.reload()
                        return
                }

                $('.loader').fadeOut()
                showToast(data.message)
            },
            error: function() {
                $('.loader').fadeOut()
                showToast(errorMessage)
            }
        })
    }

    function valiPhonenumberTyping(telInp){
        if(telInp.hasClass('required')){
            telInp.next().remove();
        }

        var phoneno = /^\d{10}$/;
        var required; 

        // chưa nhập
        if(telInp.val() == ''){
            required = $('<span class="required-text">Vui lòng nhập số diện thoại</span>');
            telInp.addClass('required');
            telInp.after(required);
        }
        // không đúng định dạng | ký tự đầu k phải số 0 
        else if(!telInp.val().match(phoneno) || telInp.val().charAt(0) != 0){
            if(telInp.next().hasClass('required-text')){
                return;
            }
            required = $('<span class="required-text">Số diện thoại không hợp lệ</span>');    
            telInp.addClass('required');
            telInp.after(required);
        } 
        // hợp lệ
        else {
            telInp.removeClass('required');
            telInp.next().remove();
        }
    }

    // kiểm tra bẫy lỗi mã xác nhận
    function validateVerifyCode(codeInput){
        // nếu đã kiểm tra rồi thì return
        if(codeInput.hasClass('required')){
            return;
        }

        var code = codeInput.val().trim();

        // code hợp lệ
        if(code != '' && !isNaN(code) && code.length == 6){
            return true;
        }

        // reset nhập code
        codeInput.val('');

        // hiển thị thông báo lỗi
        codeInput.addClass('required');(errMessage);
        var errMessage = $('<div class="required-text text-center">Mã xác thực không hợp lệ, vui lòng kiểm tra lại</div>');
        codeInput.after(errMessage);
        return false;
    }

    function validatePassword(passwordInp, rePasswordInp){
        var pw = passwordInp.val();
        var rePw = rePasswordInp.val();

        if(passwordInp.hasClass('required') || rePasswordInp.hasClass('required')){
            return;
        }

        // chưa nhập mật khẩu
        if(pw == ''){
            passwordInp.addClass('required');
            var errMess = $('<div class="required-text">Vui lòng nhập mật khẩu</div>');
            passwordInp.after(errMess);
            return false;
        }

        // độ dài tối thiểu
        if(pw.length < 6 || pw.length > 16){
            passwordInp.addClass('required');
            var errMess = $('<div class="required-text">Độ dài mật khẩu từ 6-16 ký tự</div>');
            passwordInp.after(errMess);
            return false;
        }

        // chưa nhập lại mật khẩu
        if(rePw == ''){
            rePasswordInp.addClass('required');
            var errMess = $('<div class="required-text">Vui lòng nhập lại mật khẩu</div>');
            rePasswordInp.after(errMess);
            return false;
        }

        // nhập lại không khớp
        if(pw.localeCompare(rePw) != 0){
            rePasswordInp.addClass('required');
            var errMess = $('<div class="required-text">Nhập lại mật khẩu không trùng khớp</div>');
            rePasswordInp.after(errMess);
            return false;
        }

        return true;
    }

    // hiển thị toast
    function showToast(message){
        if($('#alert-toast').length) {
            $('#alert-toast').remove();
        }
        $('body').prepend(`<span id="alert-toast" class="alert-toast">${message}</span>`)

        setTimeout(() => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                // xóa toast
                setTimeout(() => {
                    $('#alert-toast').remove();
                },100);
                
                $('#alert-toast').css('transform', 'translateY(100px)')
            }, 3000);

            $('#alert-toast').css('transform', 'translateY(0)')
        }, 200);
    }

    function showToastRealtime(notificationObject) {
        if($('#alert-toast').length) {
            $('#alert-toast').remove();
        }

        let toast = ''

        // đơn hàng
        if(notificationObject.type === 'order') {
            switch(notificationObject.orderStatus) {
                // xác nhận đơn hàng
                case 'confirmed':
                    toast =
                        `<div id="alert-toast" class="alert-toast-2">
                            <span class="close-toast-btn"><i class="fal fa-times-circle"></i></span>
                            <div class="alert-toast-icon white fz-36"><i class="fas fa-truck"></i></div>
                            <div class="alert-toast-2-content">
                                <div class="mb-10">Đã xác nhận đơn hàng <b>#${notificationObject.id_dh}</b> của bạn.</div>
                                <div class="d-flex justify-content-end align-items-center mr-5">
                                    <div class="dot-green mr-5"></div>
                                    <div class="fst-italic fw-lighter fz-12">Bây giờ</div>
                                </div>
                            </div>
                        <div>`
                    break
                // đơn hàng thành công
                case 'success':
                    let style = window.innerWidth > 768 ? 'style="max-width: 350px"' : ''
                    toast =
                        `<div id="alert-toast" class="alert-toast-2">
                            <span class="close-toast-btn"><i class="fal fa-times-circle"></i></span>
                            <div class="alert-toast-icon white fz-36"><i class="fas fa-truck"></i></div>
                            <div class="alert-toast-2-content">
                                <div class="mb-10" ${style}>
                                    Đơn hàng <b>#${notificationObject.id_dh}</b> đã được giao thành công. Cảm ơn bạn đã mua hàng tại LDMobile, chúng tôi xin gửi tặng bạn mã giảm giá... <a href="taikhoan/thongbao">Chi tiết</a>
                                </div>
                                <div class="d-flex justify-content-end align-items-center mr-5">
                                    <div class="dot-green mr-5"></div>
                                    <div class="fst-italic fw-lighter fz-12">Bây giờ</div>
                                </div>
                            </div>
                        <div>`
                    break
                // hủy đơn hàng
                case 'cancelled':
                    toast =
                        `<div id="alert-toast" class="alert-toast-2">
                            <span class="close-toast-btn"><i class="fal fa-times-circle"></i></span>
                            <div class="alert-toast-icon white fz-36"><i class="fas fa-truck"></i></div>
                            <div class="alert-toast-2-content">
                                <div class="mb-10">Đơn hàng <b>#${notificationObject.id_dh}</b> của bạn đã bị hủy.</div>
                                <div class="d-flex justify-content-end align-items-center mr-5">
                                    <div class="dot-green mr-5"></div>
                                    <div class="fst-italic fw-lighter fz-12">Bây giờ</div>
                                </div>
                            </div>
                        <div>`
                    break
            }
        }
        // phản hồi
        else {
            const data = notificationObject.data

            toast =
             `<div id="alert-toast" class="alert-toast-2">
                <span class="close-toast-btn"><i class="fal fa-times-circle"></i></span>
                <div class="alert-toast-icon">
                    <img src="${data.avtURL}" class="circle-img">
                </div>
                <div class="alert-toast-2-content">
                    <div class="mb-10"><b>${data.userReply.hoten}</b> đã trả lời đánh giá của bạn. <a href="${data.link}">Chi tiết</a></div>
                    <div class="d-flex justify-content-end align-items-center mr-5">
                        <div class="dot-green mr-5"></div>
                        <div class="fst-italic fw-lighter fz-12">Bây giờ</div>
                    </div>
                </div>
            <div>`
        }

        $('body').prepend(toast)

        setTimeout(() => {
            $('#alert-toast').css('transform', 'translateY(0)')
        }, 500);
    }

    function closeToast(id){
        setTimeout(() => {
            $(id).remove();
        }, 100);
        $(id).css('transform', 'translateY(100px)');
    }

    function numberWithDot(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function applyVoucher(id){
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: 'apply-voucher',
                type: 'POST',
                data: {'id': id},
                success: function(data){
                    resolve(data)
                },
                error: function() {
                    reject()
                }
            });
        })
    }

    // kiểm tra họ tên
    function validateFullName(fullName){
        // nếu đã kiểm tra rồi thì return
        if(fullName.hasClass('required')){
            return;
        }

        const name = fullName.val().trim()

        // nếu chưa nhập họ tên
        if(name.length == 0){
            var required = $('<span class="required-text">Vui lòng nhập họ và tên</span>');
            fullName.addClass('required');
            fullName.after(required);
            return false;
        }

        return true;
    }

    // kiểm tra số điện thoại
    function validatePhoneNumber(tel){
        // nếu đã kiểm tra rồi thì return
        if(tel.hasClass('required')){
            return;
        }

        var length = tel.val().length;
        var phoneno = /^\d{10}$/;
        var required;

        // chưa nhập
        if(length == 0){
            tel.addClass('required');
            required = $('<span class="required-text">Vui lòng nhập số diện thoại</span>');
            tel.after(required);
            return false;
        } else if(!tel.val().match(phoneno)){ // không đúng định dạng
            required = $('<span class="required-text">Số diện thoại không hợp lệ</span>');
            tel.addClass('required');
            tel.after(required);
            return false;
        }

        return true;
    }

    // kiểm tra đã chọn quận huyện, phường xã chưa
    function validateDistrict_Wards(district, ward){
        const districtParent = district.parent()
        const wardParent = ward.parent()

        // nếu kiểm tra rồi thì return
        if(districtParent.hasClass('required') || wardParent.hasClass('required')){
            return;
        }

        // chưa chọn quận huyện
        if(!district.attr('data-flag')){
            districtParent.addClass('required');
            required = $('<span class="required-text">Vui lòng chọn Quận / Huyện</span>');
            districtParent.after(required);
            return false;
        }

        // chưa chọn phường xã
        if(!ward.attr('data-flag')){
            wardParent.addClass('required');
            required = $('<span class="required-text">Vui lòng chọn Phường / Xã</span>');
            wardParent.after(required);
            return false;
        }

        return true;
    }

    // kiểm tra nhập số nhà, tên đường
    function validateAddressInput(address){
        // nếu đã kiểm tra rồi thì return
        if(address.hasClass('required')){
            return;
        }

        // nếu chưa nhập
        if(address.val() == ''){
            address.addClass('required');
            required = $('<span class="required-text">Vui lòng nhập địa chỉ</span>');
            address.after(required);
            return false;
        }

        return true;
    }

    function removeRequried(element){
        if(element.hasClass('required')){
            element.removeClass('required');
            element.next().remove();
        }
    }

    function removeAccents(str) {
        var AccentsMap = [
          "aàảãáạăằẳẵắặâầẩẫấậ",
          "AÀẢÃÁẠĂẰẲẴẮẶÂẦẨẪẤẬ",
          "dđ", "DĐ",
          "eèẻẽéẹêềểễếệ",
          "EÈẺẼÉẸÊỀỂỄẾỆ",
          "iìỉĩíị",
          "IÌỈĨÍỊ",
          "oòỏõóọôồổỗốộơờởỡớợ",
          "OÒỎÕÓỌÔỒỔỖỐỘƠỜỞỠỚỢ",
          "uùủũúụưừửữứự",
          "UÙỦŨÚỤƯỪỬỮỨỰ",
          "yỳỷỹýỵ",
          "YỲỶỸÝỴ"    
        ];
        for (var i=0; i<AccentsMap.length; i++) {
          var re = new RegExp('[' + AccentsMap[i].substr(1) + ']', 'g');
          var char = AccentsMap[i][0];
          str = str.replace(re, char);
        }
        return str;
    }

    function chooseColor(id_sp){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': X_CSRF_TOKEN
            },
            url: '/ajax-choose-color',
            type: 'POST',
            data: {id_sp:id_sp},
            success:function(data){
                // yêu cầu đăng nhập
                if(data == false){
                    showToast('Vui lòng đăng nhập để thực hiện chức năng này');
                    return;
                }

                $('#choose-color-phone-name').text(data.tensp);
                $('#choose-color-promotion-price').html(numberWithDot(data.giakhuyenmai) + 'đ'.sup());
                if(data.khuyenmai != 0){
                    $('#choose-color-price').html(numberWithDot(data['gia']) + 'đ'.sup());
                }

                let html = ''
                let urlPhone = data.url_phone

                $.each(data.mausac, function(i, val) {
                    html += 
                        `<div type="button" data-id="${val.id}" class="choose-color-item">
                            <img src="${urlPhone + val.hinhanh}" alt="">
                            <div id="color-name" class="pt-5">${val.mausac}</div>
                        </div>`
                })
                
                $('#phone-color').append(html);
                
                $('#choose-color-modal').modal('show');
            },
            error: function() {
                showAlertTop(errorMessage)
            }
        });
    }

    // thời gian thanh toán
    async function checkoutTimeout(minute = 0, second = 0){
        return new Promise(resolve => {
            clearInterval(checkoutTimer);
            checkoutTimer = setInterval(() => {
                if(minute < 10){
                    $('.minute-number').text('0'+minute);
                } else {
                    $('.minute-number').text(minute);
                }
                if(second < 10){
                    $('.second-number').text('0'+second);
                } else {
                    $('.second-number').text(second);
                }
    
                sessionStorage.removeItem('minute');
                sessionStorage.removeItem('second');
                sessionStorage.setItem('minute', minute);
                sessionStorage.setItem('second', second);
                
                // hết thời gian
                if(!second && !minute){
                    clearInterval(checkoutTimer);
                    resolve()
                }
                // trừ phút
                else if(!second && minute){
                    minute -= 1;
                    second = 59;
                }
                // trừ giây
                else {
                    second--;
                }
            }, 1000);
        })
    }

    // xóa hàng đợi
    function removeQueue(id_tk){
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: 'ajax-remove-queue',
                type: 'POST',
                data: {'id_tk': id_tk},
                success: function() {
                    resolve()
                },
                error: function() {
                    reject()
                }
            });
        })
    }

    function showInfoVoucher(id, element){
        let infoVoucher = $('.voucher-content[data-id="'+id+'"]');

        if(!infoVoucher.is(':visible')){
            // vị trí icon
            const position = element.offset();
            // chiều cao màn hình
            const windowHeight = $(window).height();
            // cuộn màn hình
            const scrollTop = $(window).scrollTop();

            const number = 20;
            const top = position.top + number - scrollTop;
            const left = position.left + number;
    
            // độ kích thước info voucher
            const infoVoucherWidth = infoVoucher.outerWidth();
            const infoVoucherHeight = infoVoucher.outerHeight();
    
            // chiều cao khi show info voucher
            const shownInfoVoucherHeight = top +  infoVoucherHeight;
    
            // hiển thị info voucher nằm trong trang
            if(windowHeight > shownInfoVoucherHeight){
                infoVoucher.css({
                    'top': top,
                    'left': left - infoVoucherWidth,
                })
            } else {
                infoVoucher.css({
                    'top': top - infoVoucherHeight - number,
                    'left': left - infoVoucherWidth,
                })
            }
    
            infoVoucher.show()
        }
    }

    function hideInfoVoucher(id){
        let infoVoucher = $('.voucher-content[data-id="'+id+'"]');

        infoVoucher.hide()

        infoVoucher.removeAttr('style')
    }

    async function voucherCheck() {
        let bool = true

        const isApplied = await isAppliedVoucher()
            .then(boolean => boolean)
            .catch(() => {
                showAlertTop(errorMessage)
                return
            })

        if(isApplied) {
            if(page === 'giohang') {
                bool = await isExpiredVoucher()
                    .then(isExpired => {
                        if(isExpired) {
                            sessionStorage.setItem('alert-top-message', 'Mã giảm giá đã hết hạn');
                            return false
                        }

                        return true
                    })
            } else if(page === 'thanhtoan') {
                // kiểm tra thỏa điều kiện
                const isSatisfied = await isSatisfiedVoucher()
                    .then(boolean => {
                        // không thỏa điều kiện
                        if(!boolean) {
                            return removeVoucher()
                                .then(() => {
                                    sessionStorage.setItem('alert-top-message', 'Đã hủy mã giảm giá do chưa thỏa điều kiện')
                                    return false
                                })
                        } else {
                            return true
                        }
                    })

                // Không thỏa
                if(!isSatisfied) {
                    bool = false
                }
                // kiểm tra tiếp hết HSD
                else {
                    bool = await isExpiredVoucher()
                        .then(boolean => {
                            // hết HSD
                            if(boolean) {
                                sessionStorage.setItem('alert-top-message', 'Mã giảm giá đã hết hạn');
                                return false
                            }
    
                            return true
                        })
                }
            }
        }

        return bool
    }

    // kiểm tra có sử dụng voucher không
    function isAppliedVoucher() {
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: 'ajax-is-applied-voucher',
                success: function(data) {
                    resolve(data)
                },
                error: function() {
                    reject()
                }
            })
        })
    }

    // kiểm tra voucher còn HSD không
    function isExpiredVoucher(){
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: 'ajax-is-expired-voucher',
                cache: false,
                success: function(data){
                    // voucher hết HSD
                    resolve(data)
                },
                error: function() {
                    reject()
                }
            });
        })
    }

    // kiểm tra voucher có thỏa điều kiện không
    function isSatisfiedVoucher() {
        return new Promise((resolve, reject) => {
            const idList = JSON.parse(sessionStorage.getItem('checkoutList'))

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: 'ajax-check-satisfied-voucher',
                type: 'POST',
                data: {idList},
                cache: false,
                success: function(data) {
                    resolve(data)
                },
                error: function() {
                    reject()
                }
            })
        })
    }

    // hủy voucher
    function removeVoucher() {
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: 'ajax-remove-voucher',
                success: function () {
                    resolve()
                },
                error: function() {
                    reject()
                }
            });
        })
    }

    // lấy sl mẫu sp theo dung lượng
    async function getTotalQtyProByCap() {
        $.ajax({
            headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
            url: 'ajax-get-total-qty-pro-by-cap',
            success: function(data) {
                $('#qty-product').text(`${data.totalQty} điện thoại`)
            }
        })
    }

    function renderPhoneCard(data) {
        let html = ''
        let promotionBadge = ''
        let starRating = ''
        $.each(data, function(i, val) {
            // sao đánh giá
            starRating = ''
            if(val.danhgia.qty != 0) {
                for (let i = 1; i <= 5; i++){
                    if(val.danhgia.star >= i){
                        starRating += '<i class="fas fa-star checked"></i>';
                    } else {
                        starRating += '<i class="fas fa-star uncheck"></i>';
                    }
                }

                starRating += `<span class="fz-14 ml-10">${val.danhgia.qty} đánh giá</span>`
            } else {
                for (let i = 1; i <= 5; i++){
                    starRating += '<i class="fas fa-star uncheck"></i>';
                }
            }

            promotionBadge = ''
            if(val.khuyenmai != 0) {
                promotionBadge =
                `<div class="shop-promotion-tag">
                    <span class="shop-promotion-text">-${val.khuyenmai*100}%</span>
                </div>`
            }

            // hàng sắp về
            if(val.comingSoon) {
                html +=
                    `<div class="col-lg-3 col-md-4 col-sm-6 col-6">
                        <a href="dienthoai/${val.tensp_url}" class="shop-product-card-coming-soon">
                            ${promotionBadge}

                            <div class="relative">
                                <div class="coming-soon">HÀNG SẮP VỀ</div>
                                <img src="images/phone/${val.hinhanh}" class="shop-product-img-card">
                            </div>

                            <div class="shop-product-name">${val.tensp}</div>

                            <div class="text-center">
                                <div class="mb-5">
                                    <span class="fw-600 red">${numberWithDot(val.giakhuyenmai)}<sup>đ</sup></span>
                                    ${val.khuyenmai != 0 ?
                                        `<span class="ml-5 text-strike">${numberWithDot(val.gia)}<sup>đ</sup></span>` : ''
                                    }
                                </div>
                                <div>
                                    ${starRating}
                                </div>
                            </div>
                        </a>
                    </div>`
            } else {
                html +=
                    `<div class="col-lg-3 col-md-4 col-sm-6 col-6">
                        <div class="shop-product-card box-shadow">
                            ${promotionBadge}
                            <div class="shop-overlay-product"></div>
                            <div type="button" data-id="${val.id}" class="shop-cart-link"><i class="fas fa-cart-plus mr-10"></i>Thêm vào giỏ hàng</div>
                            <a href="dienthoai/${val.tensp_url}" class="shop-detail-link"><i class="far fa-search-plus mr-10"></i>Xem chi tiết</a>
                            
                            <img src="images/phone/${val.hinhanh}" class="shop-product-img-card">

                            <div class="shop-product-name">${val.tensp}</div>

                            <div class="text-center">
                                <div class="mb-5">
                                    <span class="fw-600 red">${numberWithDot(val.giakhuyenmai)}<sup>đ</sup></span>
                                    ${val.khuyenmai != 0 ?
                                        `<span class="ml-5 text-strike">${numberWithDot(val.gia)}<sup>đ</sup></span>` : ''
                                    }
                                </div>
                                <div>
                                    ${starRating}
                                </div>
                            </div>
                        </div>
                    </div>`
            }

        })

        return html
    }

    function renderNotification(data) {
        let html = ''
        $.each(data, (i, val) => {
            html += `
                <div id="noti-${val.id}" class="single-noti ${val.trangthaithongbao == 0 ? 'account-noti-wait' : 'account-noti-checked'} box-shadow mb-20">
                    <div class="d-flex align-items-center justify-content-between p-10 border-bottom">
                        <div class="d-flex align-items-center">
                            <div>`
                                switch(val.tieude) {
                                    case 'Đơn đã tiếp nhận':
                                        html += '<i class="fas fa-file-alt fz-28 info-color"></i>'
                                        break
                                    case 'Đơn đã xác nhận':
                                        html += '<i class="fas fa-file-check fz-28 success-color"></i>'
                                        break
                                    case 'Giao hàng thành công':
                                        html +='<i class="fas fa-box-check fz-28 success-color"></i>'
                                        break
                                    case 'Mã giảm giá':
                                        html += '<i class="fas fa-badge-percent fz-28 yellow"></i>'
                                        break
                                    case 'Phản hồi':
                                        html += '<i class="fas fa-reply fz-28 purple"></i>'
                                        break
                                } html +=`
                            </div>
                            <div class="fw-600 fz-18 ml-10">${val.tieude}</div>
                        </div>
                        <div class="d-flex align-items-end">
                            ${val.trangthaithongbao == 0 ?
                                `<div type="button" class="noti-btn-read main-color-text mr-10" data-id="${val.id}">Đánh dấu đã đọc</div>`
                                : ''
                            }
                            <div type="button" class="noti-btn-delete red" data-id="${val.id}">xóa</div>
                        </div>
                    </div>
                    <div class="d-flex pt-20 pb-20 pl-10 pr-10">
                        <div id="noti-content-${val.id}">
                            <div>${val.noidung}</div>
                            <div class="mt-10 fz-14">${val.thoigian}</div>
                        </div>
                    </div>
                </div>
            `
        })

        return html
    }

    function addCart(id_sp, sl) {
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: '/ajax-add-cart',
                type: 'POST',
                data: {id_sp:id_sp, sl:sl},
                success:function(data) {
                    resolve(data)
                },
                error: function() { reject() }
            });
        })
    }

    // thông báo thêm giỏ hàng thành công
    function renderAddCartSuccessfully() {
        if(window.innerWidth > 992){
            if($('.add-cart-success').length) {
                $('.add-cart-success').remove()
            }

            const addCartSuccess = 
                `<div class="add-cart-success">
                    <div class="d-flex align-items-center"><i class="fas fa-check-circle success-color mr-10"></i>Thêm giỏ hàng thành công!</div>
                    <a href="giohang" class="checkout-btn w-100 mt-20">Xem giỏ hàng và thanh toán</a>
                </div>`

            $('#add-cart-success').append(addCartSuccess);

            clearTimeout(timer);
            timer = setTimeout(() => {
                setTimeout(() => {
                    $('.add-cart-success').remove();
                }, 1000);
                $('.add-cart-success').hide('fade', 300);
            }, 5000);
        } else {
            showToast('Thêm giỏ hàng thành công');
        }
    }

    function getQtyInStockById(id_sp) {
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: '/ajax-get-qty-in-stock',
                type: 'POST',
                data: {'id_sp': id_sp},
                success:function(data){
                    resolve(data)
                },
                error: function() {
                    reject()
                }
            });
        })
    }

    function updateQtyCart(id, type) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': X_CSRF_TOKEN
            },
            type: 'POST',
            data: {id:id, type:type},
            url: '/ajax-update-cart',
            success:function(data){
                isClicked = false

                // slton kho không đủ
                if(data.status === 'not enough'){
                    showToast(`Chỉ còn ${data.qtyInStock} sản phẩm`)
                    return;
                }

                // cập nhật số lượng sản phẩm
                $(`.qty-item[data-id="${id}"]`).text(data.newQty);

                // cập nhật thành tiền sản phẩm
                $(`.provisional_item[data-id="${id}"]`).html(numberWithDot(data.newPrice) + 'đ'.sup());

                provisionalAndTotalOrder()
            },
            error: function() {
                showToast(errorMessage)
            }
        });
    }

    async function provisionalAndTotalOrder() {
        let idList = []

        // danh sách id_sp thanh toán
        $.each($('.cus-checkbox-checked'), (i, element) => {
            const id = $(element).attr('data-id')

            if(id !== 'all') {
                idList.push(id)
            }
        })

        getProvisionalOrder(idList)
            .then(data => {
                // tổng tiền
                let total = 0;
                // tạm tính
                const provisional = data.provisional;

                // có sử dụng voucher
                if(data.voucher){
                    // kiểm tra nếu tổng tiền < điều kiện của voucher thì hủy voucher
                    const condition = data.voucher.dieukien;
                    // nếu tạm tính < điều kiện voucher thì hủy voucher
                    if(provisional < condition){
                        removeVoucher()
                            .then(() => {
                                $('#cart-voucher').children().remove()
                                const chooseVoucherBtn =
                                    `<span id="choose-voucher-button" class="pointer-cs main-color-text">
                                        <i class="fas fa-ticket-alt mr-10"></i>Chọn Mã khuyến mãi
                                    </span>`
                                $('#cart-voucher').append(chooseVoucherBtn)
                                $('#voucher').parent().remove()

                                showToast('Đã hủy mã giảm giá do chưa thỏa điều kiện')
                            })
                    }

                    const discount = data.voucher.chietkhau;
                    total = provisional - (provisional * discount);
                } else {
                    total = provisional;
                }

                // cập nhật tạm tính và tổng tiền
                $('#provisional').html(numberWithDot(provisional) + 'đ'.sup());
                $('#total').html(numberWithDot(total) + 'đ'.sup());
            })
            .catch(error => {
                console.error(error)
                showToast(errorMessage)
            })
    }

    // lấy tạm tính của giỏ hàng
    function getProvisionalOrder(idList) {
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: 'ajax-get-provisional-order',
                type: 'POST',
                data: {idList},
                success: function(data) {
                    resolve(data)
                },
                error: function() {
                    reject()
                }
            })
        })
    }

    async function updateSatifiedVoucher(provisional) {
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: '/ajax-check-voucher-conditions',
                type: 'POST',
                data: {'cartTotal': provisional},
                success:function(data){
                    resolve(data)
                },
                error: function() {
                    reject()
                }
            });
        })
    }

    function renderVoucher(data) {
        const satisfied = data.map((val, i) => {
            if(val.status === 'satisfied') {
                return (
                   `
                   <div class="col-lg-8 col-md-10 col-12 mx-auto pb-30">
                        <div class="account-voucher">
                            <div class="voucher-left w-20 p-70">
                                ${val.sl != 1 ?
                                `<div class="voucher-qty">${val.sl}x</div>` : ''}
                                <div class="voucher-left-content fz-40">-${val.chietkhau*100}%</div>
                            </div>
                            <div class="voucher-right w-80">
                                <div class="voucher-right-content">
                                    <div class="d-flex justify-content-end">
                                        <div data-id="${val.id}" class="relative promotion-info-icon">
                                            <i class="fal fa-info-circle fz-20"></i>
                                            <div data-id="${val.id}" class="voucher-content">
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <td class="w-40">Mã</td>
                                                            <td><b>${val.code}</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="w-40">Nội dung</td>
                                                            <td>${val.noidung}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="w-40">
                                                                <div class="d-flex flex-column">
                                                                    <span>Điều kiện</span>
                                                                    ${val.dieukien != 0 ?
                                                                    `<ul class="mt-10">
                                                                        <li>Áp dụng cho đơn hàng từ ${numberWithDot(val.dieukien)}<sup>đ</sup></li>
                                                                    </ul>` : ''
                                                                    }
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="w-40">Hạn sử dụng</td>
                                                            <td>${val.ngayketthuc}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-fill">${val.noidung}</div>
                                    <div class="d-flex justify-content-between">
                                        <span class="d-flex align-items-end">HSD: ${val.ngayketthuc}</span>
                                        <div data-id="${val.id}" class="apply-voucher-btn main-btn p-10">Áp dụng</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>` 
                )
            }
        }).join('')

        const unsatisfied = data.map((val, i) => {
            if(val.status === 'unsatisfied') {
                return (
                    `<div class="col-lg-8 col-md-10 col-12 mx-auto pb-30">
                        <div class="account-voucher">
                            <div class="dis-voucher-left w-20 p-70">
                                ${val.sl != 1 ?
                                    `<div class="voucher-qty">${val.sl}x</div>` : ''}
                                    <div class="dis-voucher-left-content fz-40">-${val.chietkhau*100}%</div>
                            </div>
                            <div class="dis-voucher-right w-80">
                                <div class="voucher-right-content">
                                    <div class="d-flex justify-content-end">
                                        <div data-id="${val.id}" class="relative dis-promotion-info-icon">
                                            <i class="fal fa-info-circle fz-20"></i>
                                            <div data-id="${val.id}" class="voucher-content">
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <td class="w-40">Mã</td>
                                                            <td><b>${val.code}</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="w-40">Nội dung</td>
                                                            <td>${val.noidung}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="w-40">
                                                                <div class="d-flex flex-column">
                                                                    <span>Điều kiện</span>
                                                                    ${val.dieukien != 0 ?
                                                                    `<ul class="mt-10">
                                                                        <li>Áp dụng cho đơn hàng từ ${numberWithDot(val.dieukien)}<sup>đ</sup></li>
                                                                    </ul>` : ''
                                                                    }
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="w-40">Hạn sử dụng</td>
                                                            <td>${val.ngayketthuc}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-fill">
                                        <span>${val.noidung}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="d-flex align-items-end">HSD: ${val.ngayketthuc}</span>

                                        <div class="dis-condition-tag">Chưa thỏa điều kiện</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`
                )
            }
        }).join('')

        return satisfied + unsatisfied
    }

    function renderSmallVoucher(voucher) {
        const voucherElement = 
            `<div class='account-voucher'>
                <div class='voucher-left-small'>
                    <div class='voucher-left-small-content'>-${voucher.chietkhau*100}%</div>
                </div>
                <div class='voucher-right-small'>
                    <b class="fz-14">${voucher.code}</b>
                    <div class="d-flex align-items-center">
                        <div data-id="${voucher.id}" class="relative promotion-info-icon mr-10">
                            <i class="fal fa-info-circle main-color-text fz-20"></i>
                            <div data-id="${voucher.id}" class='voucher-content'>
                                <table class='table'>
                                    <tbody>
                                        <tr>
                                            <td class='w-40'>Mã</td>
                                            <td><b>${voucher.code}</b></td>
                                        </tr>
                                        <tr>
                                            <td class="w-40">Nội dung</td>
                                            <td>${voucher.noidung}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class='w-40'>
                                                <div class='d-flex flex-column'>
                                                    <span>Điều kiện</span>
                                                    ${voucher.dieukien != 0 ?
                                                        `<ul class='mt-10'>
                                                            <li>Áp dụng cho đơn hàng từ ${numberWithDot(voucher.dieukien)}<sup>đ</sup></li>
                                                        </ul>` : ''
                                                    }
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class='w-40'>Hạn sử dụng</td>
                                            <td>${voucher.ngayketthuc}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div data-id="${voucher.id}" class="apply-voucher-btn main-btn" style="padding: 5px">Bỏ chọn</div>
                    </div>
                </div>
            </div>`

        return voucherElement
    }

    async function getCartByIdProduct(idList) {
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                },
                url: 'ajax-get-cart-by-id-sp-list',
                type: 'POST',
                data: {idList: idList},
                success: function(data) {
                    resolve(data)
                },
                error: function() {
                    reject()
                }
            })
        })
    }

    async function renderCart(data) {
        const voucher = data.voucher
        const isVoucher = voucher ? true : false

        let voucherElement =
            `<tr id="cart-voucher">
                <td>
                    <div class="p-10">
                        <span id="choose-voucher-button" class="pointer-cs main-color-text">
                            <i class="fas fa-ticket-alt mr-10"></i>Chọn Mã khuyến mãi
                        </span>
                    </div>
                </td>
            </tr>`

        if(isVoucher) {
            voucherElement =
                `<tr id="cart-voucher">
                    <td class="p-0 d-flex">
                        <div class='w-30 p-10 bg-gray-4 d-flex justify-content-center align-items-center'>
                            <b>Mã giảm giá</b>
                        </div>
                        <div class="w-70 p-10 d-flex justify-content-center">
                            <div class="w-97">
                                ${renderSmallVoucher(voucher)}
                            </div>
                        </div>
                    </td>
                </tr>`
        }

        let productListElement = ''
        const productList = data.productList
        $.each(productList, (i, val) => {
            productListElement +=
                `<tr>
                    <td>
                        <div class='d-flex flex-row align-items-center justify-content-between'>
                            <div class='d-flex'>
                                <img src="images/phone/${val.hinhanh}" alt="" width="90px">
                                <div class='d-flex flex-column ml-5 fz-14'>
                                    <b>${val.tensp}</b>
                                    <span>Màu sắc: ${val.mausac}</span>
                                    <b>Số lượng: ${val.sl}</b>
                                </div>
                            </div>
                            <b class='d-flex align-items-center justify-content-end red'>${numberWithDot(val.thanhtien)}<sup>đ</sup></b>
                        </div>
                    </td>
                </tr>`
        })

        const qty = productList.length
        let total = data.total
        if(isVoucher) {
            total = data.total - (data.total * voucher.chietkhau)   
        }

        let cart =
            `<table class="table">
                <tbody>
                    <tr>
                        <td class='p-0'></td>
                    </tr>
                    <tr>
                        <td>
                            <a href="/giohang"><i class="fal fa-chevron-left mr-5"></i>Chỉnh sửa giỏ hàng</a>
                        </td>
                    </tr>
                    ${productListElement}
                    ${voucherElement}
                    <tr>
                        <td>
                            <div class='p-10 d-flex flex-column'>
                                <div id="provisional-text" class='d-flex justify-content-between'>
                                    <span id="provisional" data-provisional="${data.total}">Tạm tính (${qty} sản phẩm):</span>
                                    <div>${numberWithDot(data.total)}<sup>đ</sup></div>
                                </div>
                                ${isVoucher ?
                                    `<div id="voucher-discount-text" class='d-flex justify-content-between mt-20'>
                                        <span>Mã giảm giá:</span>
                                        <div id="voucher" data-discount="${voucher.chietkhau}" class='main-color-text'>-${voucher.chietkhau*100}%</div>
                                    </div>` : ''
                                }
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class='d-flex align-items-center justify-content-between p-10'>
                                <b>Tổng tiền:</b>
                                <b id="total" data-total="${total}" class='red fz-22'>${numberWithDot(total)}</b>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>`

        $('#collapse-cart').append(cart)
    }

    // lấy danh sách tỉnh/ thành
    function getProvince(accessFirstItem = false) {
        return new Promise((resolve, reject) => {
            xhrProvince = $.ajax({
                url: `${ADDRESS_URL}?depth=1`,
                timeout: 10000,
                success: function(provinceList) {
                    let firstItem = null

                    $('#list-tinh-thanh').children().remove()
            
                    // render danh sách tỉnh/thành
                    const list = provinceList.map(val => {
                        return (
                            `<div id="${val.code}" data-type='TinhThanh' data-name="${val.name}" class="option-tinhthanh select-single-option">${val.name}</div>`
                        )
                    }).join('')
            
                    $('#list-tinh-thanh').append(list)

                    $('#TinhThanh-selected .select-spinner').replaceWith('<i class="far fa-chevron-down fz-14"></i>')

                    if(accessFirstItem) {
                        firstItem = provinceList[0]

                        // gán dữ liệu
                        $('#TinhThanh-name').text(firstItem.name)
                        $('#TinhThanh_name_inp').val(firstItem.name)
                        resolve(firstItem.code)
                    } else {
                        resolve()
                    }
                },
                error: function() {
                    reject()
                }
            })
        })
    }

    // lấy danh sách quận/ huyện
    function getDistrict(id_province) {
        return new Promise((resolve, reject) => {
            xhrDistrict = $.ajax({
                url: `${ADDRESS_URL}p/${id_province}?depth=2`,
                timeout: 10000,
                success: function(data) {
                    const districtList = data.districts
                    
                    $('#list-quan-huyen').children().remove()
    
                    // render danh sách quận/huyện
                    const list = districtList.map(val => {
                        if(val.name !== 'Chưa rõ') {
                            return (
                                `<div id='${val.code}' data-type='QuanHuyen' data-name="${val.name}" class="option-quanhuyen select-single-option">${val.name}</div>`
                            )
                        }
                    }).join('')
            
                    $('#list-quan-huyen').append(list)

                    $('#QuanHuyen-selected .select-spinner').replaceWith('<i class="far fa-chevron-down fz-14"></i>')

                    resolve()
                },
                error: function() {
                    reject()
                }
            })
        })
    }

    // lấy danh sách phường/ xã
    function getWard(id_district) {
        return new Promise((resolve, reject) => {
            xhrWard = $.ajax({
                url: `${ADDRESS_URL}d/${id_district}?depth=2`,
                timeout: 10000,
                success: function(data) {
                    const wardList = data.wards

                    $('#list-phuong-xa').children().remove()

                    // render danh sách phường/ xã
                    const list = wardList.map(val => {
                        return (
                            `<div id="${val.code}" data-type="PhuongXa" data-name="${val.name}" class="option-phuongxa select-single-option">
                                ${val.name}
                            </div>`
                        )
                    }).join('')

                    $('#list-phuong-xa').append(list)

                    $('#PhuongXa-selected .select-spinner').replaceWith('<i class="far fa-chevron-down fz-14"></i>')

                    resolve()
                },
                error: function() {
                    reject()
                }
            })
        })
    }

    /*============================================================================================================
                                        Phần sử dụng chung cho nhiều trang
    ==============================================================================================================*/

    /*===========================================================
                                Địa chỉ
    =============================================================*/

    // modal thêm địa chỉ mới
    $('#new-address-show').click(function(){
        // lấy danh sách thành phố
        getProvince(true)
            .then(id_province => getDistrict(id_province))
            .catch(() => {
                sessionStorage.setItem('toast-message', 'Không thể lấy danh sách dữ liệu. Vui lòng thử lại')
                location.reload()
            })

        if($(this).data('default')){
            $('#set_default_address').attr('checked', true);
        }

        // tạo mới
        $('input[name="address_type"]').val('create');

        // tiêu đề modal
        $('#address-modal-title').text('Tạo địa chỉ mới');

        // thiết lập nút
        $('.address-action-btn').attr('data-type', 'create');
        $('.address-action-btn').text('Thêm');

        $('#address-modal').modal('show');
    });

    // modal chỉnh sửa địa chỉ
    $('.btn-edit-address').off('click').click(function(){
        var id = $(this).data('id');

        var defaultAdr = $(`#address-${id}`).data('default');
        editAddressModal(id, defaultAdr);
    });

    // thêm|sửa địa chỉ mới
    $('.address-action-btn').off('click').click(function(){
        if(page === 'diachigiaohang' || page === 'thanhtoan'){
            removeQueueFlag = false;
        }

        var valiFullname = validateFullName($('input[name="adr_fullname_inp"]'));
        var valiTel = validatePhoneNumber($('input[name="adr_tel_inp"]'));
        var valiQuanHuyenPhuongXa = validateDistrict_Wards($('#QuanHuyen-name'), $('#PhuongXa-name'));
        var valiAddressInp = validateAddressInput($('input[name="address_inp"]'));
        var setDefault = $('#set_default_address');
        setDefault.is(':checked') ? setDefault.val(1) : setDefault.val(0);

        if(valiFullname && valiTel && valiQuanHuyenPhuongXa && valiAddressInp){
            $('.loader').fadeIn()

            const data = {
                type: $('input[name="address_type"]').val(),
                hoten: $('input[name="adr_fullname_inp"]').val(),
                diachi: $('input[name="address_inp"]').val(),
                phuongxa: $('#PhuongXa-name').text(),
                quanhuyen: $('#QuanHuyen-name').text(),
                tinhthanh: $('#TinhThanh-name').text(),
                sdt: $('input[name="adr_tel_inp"]').val(),
                macdinh: $('#set_default_address').is(':checked') ? 1 : 0,
                tk_dc_id: $('input[name="tk_dc_id"]').val()
            }

            $.ajax({
                headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
                url: 'ajax-create-update-address',
                type: 'POST',
                data: data,
                success: function(data) {
                    sessionStorage.setItem('toast-message', data.message)
                    location.reload()
                },
                error: function() {
                    showToast(errorMessage)
                }
            })
        }
    });

    $('input[name="adr_fullname_inp"]').keyup(function(){
        if($(this).hasClass('required')){
            $(this).removeClass('required');
            $(this).next().remove();
        }
    });

    $('input[name="adr_tel_inp"]').keyup(function(){
        valiPhonenumberTyping($(this));
    });

    $('input[name="address_inp"]').keyup(function(){
        if($(this).hasClass('required')){
            $(this).removeClass('required');
            $(this).next().remove();
        }
    });

    // đặt làm mặc định
    $('.btn-set-default-btn').off('click').click(function(){
        const id = $(this).data('id');

        $('.loader').fadeIn()

        $.ajax({
            headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
            url: 'ajax-set-default-address',
            type: 'POST',
            data: {id},
            success: function(data) {
                sessionStorage.setItem('toast-message', data.message)
                location.reload()
            },
            error: function() {
                $('.loader').fadeOut()
                showToast(errorMessage)
            }
        })
    });

    // modal xóa 1 địa chỉ
    $('.btn-delete-address').off('click').click(function(){
        $('#delete-content').text('Bạn có muốn xóa địa chỉ này ?');
        $('#delete-btn').attr('data-id', $(this).data('id'));
        $('#delete-btn').attr('data-object', 'address');
        $('#delete-modal').modal('show');
    });

    // hiển thị tỉnh thành
    $('#TinhThanh-selected').click(function(){
        $('#TinhThanh-box').toggle('blind', 250);
        $('#QuanHuyen-box').hide('blind', 250);
        $('#PhuongXa-box').hide('blind', 250);
    });

    // hiển thị quận huyện
    $('#QuanHuyen-selected').click(function(){
        $('#QuanHuyen-box').toggle('blind', 250);
        $('#TinhThanh-box').hide('blind', 250);
        $('#PhuongXa-box').hide('blind', 250);
    });

    // hiển thị phường xã
    $('#PhuongXa-selected').click(function(){
        if($(this).attr('class') == 'select-disable'){
            return;
        }
        $('#PhuongXa-box').toggle('blind', 250);
        $('#QuanHuyen-box').hide('blind', 250);
        $('#TinhThanh-box').hide('blind', 250);
    });

    // tìm kiếm tỉnh/thành
    $('#search-tinh-thanh').keyup(function(){
        var val = $(this).val();
        var selectBox = $('#list-tinh-thanh');
        
        searchPlace(val, selectBox);
    });

    // tìm kiếm quận huyện
    $('#search-quan-huyen').keyup(function(){
        var val = $(this).val();
        var selectBox = $('#list-quan-huyen');
        
        searchPlace(val, selectBox);
    });

    // tìm kiếm phường xã
    $('#search-phuong-xa').keyup(function(){
        var val = $(this).val();
        var selectBox = $('#list-phuong-xa');
        
        searchPlace(val, selectBox);
    });

    // thay đổi tỉnh/thành
    $(document).on('click', '.option-tinhthanh', function(){
        var id = $(this).attr('id');
        var name = $(this).attr('data-name');
        var type = $(this).attr('data-type');

        choosePlace(id, name, type);
    });

    // thay đổi quận, huyện
    $(document).on('click', '.option-quanhuyen', function(){
        var id = $(this).attr('id');
        var name = $(this).attr('data-name');
        var type = $(this).attr('data-type');
        choosePlace(id, name, type);
    }); 

    // thay đổi phường, xã
    $(document).on('click', '.option-phuongxa', function(){
        var id = $(this).attr('id');
        var name = $(this).attr('data-name');
        var type = $(this).attr('data-type');
        choosePlace(id, name, type);
    });

    $('input[name="address_inp"]').focus(function(){
        $('#TinhThanh-box').hide('blind', 250);
        $('#QuanHuyen-box').hide('blind', 250);
        $('#PhuongXa-box').hide('blind', 250);
    });

    // reset modal địa chỉ
    $('#address-modal').on('shown.bs.modal', function() {
        // focus họ tên
        $('[name="adr_fullname_inp"]').focus()
    })
    $('#address-modal').on('hidden.bs.modal', function() {
        if(xhrProvince) xhrProvince.abort()
        if(xhrDistrict) xhrDistrict.abort()
        if(xhrWard) xhrWard.abort()

        $('input[name="adr_fullname_inp"]').val('')
        $('input[name="adr_tel_inp"]').val('')

        // reset tỉnh/thành, quận/huyện, phường/xã, địa chỉ
        $('#list-tinh-thanh').children().remove()
        $('#list-quan-huyen').children().remove()
        $('#list-phuong-xa').children().remove()


        $('#TinhThanh-name').text('Chọn Tỉnh / Thành phố')
        $('#QuanHuyen-name').text('Chọn Quận / Huyện')
        $('#PhuongXa-name').text('Chọn Phường / Xã')

        $('#TinhThanh-selected i').replaceWith('<div class="spinner-border select-spinner" role="status"></div>')
        $('#QuanHuyen-selected i').replaceWith('<div class="spinner-border select-spinner" role="status"></div>')

        $('#PhuongXa-selected').addClass('select-disable');
        $('#PhuongXa-selected').removeClass('select-selected');

        $('.select-box').hide()

        $('.required').removeClass('required')
        $('.required-text').remove()
    })

    /*===========================================================
                                Xóa
    =============================================================*/

    // xác nhận xóa
    $('#delete-btn').click(function(){
        // ngăn xóa hàng đợi
        if(page === 'diachigiaohang'){
            removeQueueFlag = false;
        }

        const id = $(this).attr('data-id');
        const object = $(this).attr('data-object');
        deleteElement(id, object);
    });

    /*===========================================================
                                toast
    =============================================================*/

    // đóng toast
    $(document).on('click', '.close-toast-btn', function(){
        closeToast('#alert-toast');
    });
    
    /*===========================================================
                        Giỏ hàng & Voucher
    =============================================================*/

    // nút mua ngay
    $('.buy-now').click(function(){
        var id_sp = $(this).attr('data-id');

        addCart(id_sp, 1)
            .then(data => {
                switch (data.status) {
                    case 'login required':
                        showToast('Vui lòng đăng nhập để thực hiện chức năng này')
                        break
                    case 'new one':
                        if($('.head-qty-cart').hasClass('none-dp')){
                            $('.head-qty-cart').removeClass('none-dp');
                        }
    
                        var qtyHeadCart = parseInt($('.head-qty-cart').text());
                        $('.head-qty-cart').text(++qtyHeadCart);
    
                        // thông báo thêm giỏ hàng thành công
                        renderAddCartSuccessfully()
                        break
                    case 'already have':
                        const qtyInstock = data.qtyInStock
                        if(qtyInstock > 5) {
                            showToast(`Đã có sản phẩm này trong giỏ hàng và số lượng mua tối đa là 5`)
                        } else {
                            showToast(`Đã có sản phẩm này trong giỏ hàng và số lượng mua tối đa là ${data.qtyInStock}`)
                        }
                        break
                    default:
                        // thông báo thêm giỏ hàng thành công
                        renderAddCartSuccessfully()
                }
            })
            .catch(() => showAlertTop(errorMessage))
    });

    // chọn màu
    $(document).on('click', '.choose-color-item', function() {
        $('#phone-color').removeClass('required');
        $('#phone-color').next().remove();
        $('.choose-color-item').removeClass('choose-color-selected');
        $(this).addClass('choose-color-selected');

        // lấy số lượng tồn kho
        const id_sp = $(this).data('id')
        $('.buy-now').attr('data-id', id_sp)

        // số lượng chọn hiện tại
        const qty = parseInt($('#qty').text())
        
        getQtyInStockById(id_sp)
            .then(data => {
                const qtyInStock = data.qtyInStock

                $('#max-qty').val(qtyInStock);

                if (qtyInStock <= 5) {
                    if(qty > qtyInStock) {
                        $('#qty').text(qtyInStock);
                    }

                    if(!$('#warning-message').length){
                        const message = `<div id="warning-message" class="required-text ml-10">*Chỉ còn ${qtyInStock} sản phẩm</div>`
                        $('#qty-div > .d-flex').append(message);
                    } else {
                        $('#warning-message').text(`*Chỉ còn ${qtyInStock} sản phẩm`)
                    }

                    $('#qty-and-add-cart').show('blind')
                }
                // còn hàng
                else{
                    $('#warning-message').remove();
                }
            })
            .catch(() => showAlertTop(errorMessage))
    });

    // thêm giỏ hàng
    $('#btn-add-cart').click(function(){
        var id_sp = $('.choose-color-selected').data('id');
        var sl = parseInt($('#qty').text());

        // chưa chọn màu
        if(!id_sp){
            $('#phone-color').addClass('required');
            var required = $('<span class="required-text">Vui lòng chọn 1 màu sắc</span>');
            $('#phone-color').after(required);
            return;
        }

        // mua quá số lượng
        const qty = parseInt($('#qty').text())
        const maxQty = $('#max-qty').val()
        if(qty > maxQty){
            showToast(`Chỉ còn ${maxQty} sản phẩm`);
            return;
        }

        addCart(id_sp, sl)
            .then(data => {                    
                // cập nhật số lượng sản phẩm trong giỏ hàng
                const status = data.status
                if(status === 'new one'){
                    if($('.head-qty-cart').hasClass('none-dp')){
                        $('.head-qty-cart').removeClass('none-dp');
                    }

                    var qtyHeadCart = parseInt($('.head-qty-cart').text());
                    $('.head-qty-cart').text(++qtyHeadCart);
                } else if(status === 'already have') {
                    const qtyInStock = data.qtyInStock

                    if(qtyInStock < 5) {
                        showToast(`Đã có sản phẩm này trong giỏ hàng và số lượng mua tối đa là ${data.qtyInStock}`)
                    } else {
                        showToast(`Đã có sản phẩm này trong giỏ hàng và số lượng mua tối đa là 5`)
                    }
                    return
                }

                $('#choose-color-modal').modal('hide');

                // thông báo thêm giỏ hàng thành công
                renderAddCartSuccessfully()
            })
            .catch(() => showAlertTop(errorMessage))
    });

    // chọn voucher
    $(document).on('click', '#choose-voucher-button', function() {
        let idList = []

        if(page === 'giohang') {
            $('.cus-checkbox-checked').each(function() {
                const id = $(this).attr('data-id')
                if(id !== 'all') {
                    idList.push(id)
                }
            })
        } else if(page === 'thanhtoan') {
            idList = JSON.parse(sessionStorage.getItem('checkoutList'))
        }

        getProvisionalOrder(idList)
            .then(response => {
                // tạm tính
                const provisional = response.provisional;

                // cập nhật voucher thỏa điều kiện
                return updateSatifiedVoucher(provisional)
            })
            .then(data => {
                let html =
                    `<div class="text-center pt-50 pb-50 fw-600">
                        Bạn chưa có mã khuyến mãi nào.
                    </div>`

                if(data.length) {
                    html = renderVoucher(data)
                }

                $('.choose-voucher-div').children().remove();
                $('.choose-voucher-div').append(html);
            })
            .then(() => $('#voucher-modal').modal('show'))
            .catch(() => showToast('Không thể tải mã giảm giá, vui lòng làm mới lại trang'))
    })

    // áp dụng / hủy voucher
    $(document).on('click', '.apply-voucher-btn', function(){
        $('.loader').fadeIn();

        var id_vc = $(this).data('id');

        applyVoucher(id_vc)
            .then(response => {
                const message = response.status === 'success' ?
                    'Đã áp dụng mã giảm giá' : 'Đã hủy mã giảm giá'

                $('.loader').fadeOut()
                $('#voucher-modal').modal('hide')

                if(response.status === 'success') {
                    // render voucher
                    let voucherElement = ''

                    if(page === 'giohang') {
                        voucherElement = renderSmallVoucher(response.voucher)
                    } else if(page === 'thanhtoan') {
                        voucherElement = 
                            `<td class="p-0 d-flex">
                                <div class='w-30 p-10 bg-gray-4 d-flex justify-content-center align-items-center'>
                                    <b>Mã giảm giá</b>
                                </div>
                                <div class="w-70 p-10 d-flex justify-content-center">
                                    <div class="w-97">
                                        ${renderSmallVoucher(response.voucher)}
                                    </div>
                                </div>
                            </td>`
                    }

                    const voucherDiscountText =
                        `<div id="voucher-discount-text" class="d-flex justify-content-between mt-20">
                            <div>Mã giảm giá</div>
                            <div id="voucher" class="main-color-text">-${response.voucher.chietkhau*100}%</div>
                        </div>`

                    $('#cart-voucher').children().remove()
                    $('#cart-voucher').append(voucherElement)
                    $('#provisional-text').after(voucherDiscountText)
                } else {
                    let chooseVoucherBtn = ''

                    if(page === 'giohang') {
                        chooseVoucherBtn =
                        `<span id="choose-voucher-button" class="pointer-cs main-color-text">
                            <i class="fas fa-ticket-alt mr-10"></i>Chọn Mã khuyến mãi
                        </span>`
                    } else if(page === 'thanhtoan') {
                        chooseVoucherBtn =
                            `<td>
                                <div class="p-10">
                                    <span id="choose-voucher-button" class="pointer-cs main-color-text">
                                        <i class="fas fa-ticket-alt mr-10"></i>Chọn Mã khuyến mãi
                                    </span>
                                </div>
                            </td>`
                    }

                    $('#cart-voucher').children().remove()
                    $('#cart-voucher').append(chooseVoucherBtn)
                    $('#voucher-discount-text').remove()
                }

                showToast(message)

                if(page === 'giohang') {
                    return provisionalAndTotalOrder()
                } else if(page === 'thanhtoan') {
                    if(response.status === 'success') {
                        const currentTotal = $('#total').attr('data-total')
                        const discount = response.voucher.chietkhau
                        const newTotal = currentTotal - (currentTotal * discount)

                        $('#total').attr('data-new-total', newTotal)
                        $('#total').html(numberWithDot(newTotal) + 'đ'.sup())
                    } else {
                        // hủy mã giảm giá ngay khi tải trang
                        if(!$('#total').attr('data-new-total')) {
                            const total = $('#provisional').attr('data-provisional')
                            $('#total').attr('data-total', total)
                            $('#total').html(numberWithDot(total) + 'đ'.sup())
                        }
                        // hủy mã giảm giá khi trước đó đã áp dụng mã tại trang
                        else {
                            $('#total').removeAttr('data-new-total')
                            const total = $('#total').attr('data-total')
                            $('#total').html(numberWithDot(total) + 'đ'.sup())
                        }
                    }
                }
            })
            .catch(error => {
                console.log(error)
                showAlertTop('Đã có lỗi xảy ra, vui lòng làm mới lại trang')
            })
    });
    
    // cập nhật số lượng
    let isClicked = false // ngăn người dùng nhấn liên tục
    $('.update-qty').off('click').click(function(){
        var component = $(this).data('component');

        if(component == 'color'){
            var qty = parseInt($('#qty').text());
            if($(this).hasClass('plus')){
                // kiểm tra nếu có số lượng tối đa
                if($('#max-qty').val() != ''){
                    // vượt số lượng tối đa
                    const maxQty = $('#max-qty').val()
                    if(qty >= maxQty) {
                        showToast(`Chỉ còn ${maxQty} sản phẩm`);
                        return;
                    } 
                }
                if(qty === 5){
                    return;
                }
                
                $('#qty').text(++qty);
            } else {
                if(qty == 1){
                    return;
                }

                $('#qty').text(--qty);
            }
        } else {
            if(!isClicked) {
                isClicked = true;

                var id = $(this).data('id');
                var qty = parseInt($(`.qty-item[data-id="${id}"]`).text());
                var type = $(this).hasClass('plus') ? 'plus' : 'minus';
    
                // giảm số lượng về 0
                if(type === 'minus' && qty === 1) {
                    $('#delete-content').text('Bạn có muốn xóa sản phẩm này?')
                    $('#delete-btn').attr('data-object', 'item-cart');
                    $('#delete-btn').attr('data-id', id);
                    $('#delete-modal').modal('show');
                    isClicked = false
                    return
                } else if(type === 'plus' && qty >= 5){
                    isClicked = false
                    return;
                }
    
                updateQtyCart(id, type)
            }
        }
    });

    // info voucher icon hover
    $('.dis-promotion-info-icon').mouseover(function(){
        const id = $(this).data('id');
        
        showInfoVoucher(id , $(this));
    }).mouseleave(function(){
        const id = $(this).data('id');

        hideInfoVoucher(id);
    })
    $('.promotion-info-icon').mouseover(function(){
        const id = $(this).data('id');
        
        showInfoVoucher(id , $(this));
    }).mouseleave(function(){
        const id = $(this).data('id');

        hideInfoVoucher(id);
    })

    $('body').bind('DOMSubtreeModified', function() {
        // info voucher icon hover
        $('.dis-promotion-info-icon').mouseover(function(){
            const id = $(this).data('id');
            
            showInfoVoucher(id , $(this));
        }).mouseleave(function(){
            const id = $(this).data('id');

            hideInfoVoucher(id);
        })

        $('.promotion-info-icon').mouseover(function(){
            const id = $(this).data('id');
            
            showInfoVoucher(id , $(this));
        }).mouseleave(function(){
            const id = $(this).data('id');

            hideInfoVoucher(id);
        })
    })

    // reset choose color modal
    $('#choose-color-modal').on('hidden.bs.modal', function(){
        $('#choose-color-phone-name').text('');
        $('#choose-color-promotion-price').text('');
        $('#choose-color-price').text('');
        $('#phone-color').text('');
        $('#phone-color').next().remove();
        $('#qty').text('1');
        $('#max-qty').val('');
        $('#warning-message').remove();
    });

    if($('#success-img').length){
        setTimeout(() => {
            $('#success-checkout').css('opacity', '1');
        },1200);
    }

    /*===========================================================
                        Thông báo real time
    =============================================================*/

    var pusher = new Pusher('ff06b4fc89c397c1495e', {
        cluster: 'ap1'
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('my-event', function (data) {
        const id_tk = $('#session-user').attr('data-id');

        const notification = data.notification

        if(notification.user.id == id_tk){
            // yêu cầu đăng xuất
            if(notification.type === 'logout') {
                sessionStorage.setItem('alert-top-message', 'Bạn đã đăng nhập tại nơi khác')
                window.location.href = '/logout?login_status=1'
                return
            }

            // cập nhật số lượng thông báo
            var notiQty = parseInt($($('.not-seen-qty')[0]).text());
            notiQty++;
            $('.not-seen-qty').text(notiQty);

            if(notification.type === 'order' && notification.orderStatus === 'success'){
                var processingQty = parseInt($($('.processing-qty')[0]).text());
                if(processingQty == 1){
                    $('.processing-qty').hide();
                } else {
                    processingQty--;
                    $('.processing-qty').text(processingQty);
                }
            }

            // render thông báo
            showToastRealtime(notification)
        }
    });
});