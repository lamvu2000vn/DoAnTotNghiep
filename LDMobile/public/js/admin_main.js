$(window).on('load', function(){
    $('.loader').fadeOut();
});

$(function() {
    if(window.innerWidth < 992) {
        pcRequire()
    }

    // auto cuộn
    setTimeout(() => {
        // cuộn lên đầu trang
        setTimeout(() => {
            loadMoreFlag = false;
        }, 500);
        $(window).scrollTop(0);
        
        // cuộn tới link đang chọn
        var position = $('.sidebar-link.sidebar-link-selected').position().top;
        if(position > 600){
            $('.sidebar.custom-scrollbar').animate({scrollTop: position});
        }
    }, 200);

    let page = window.location.pathname.split('/')[2];
    if(page === undefined){
        page = '';
    }
    const navigation = performance.getEntriesByType("navigation")[0].type;
    
    const SUCCESS = '#D2F4EA';
    const DANGER = '#F8D7DA';
    const CREATE_MESSAGE = 'Thêm thành công';
    const EDIT_MESSAGE = 'Chỉnh sửa thành công';
    const DELETE_MESSAGE = 'Xóa thành công';
    const errorMessage = 'Đã có lỗi xảy ra. Vui lòng thử lại'
    const maxSizeImageMessage = 'Hình ảnh có dung lượng tối đa là 5 MB'
    const TOAST_SUCCESS_TYPE = 1
    const TOAST_DELETE_TYPE = 2
    const X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content')
    const MAX_SIZE_IMAGE = 5 // 5 MB
    const BYTE = 1024
    const POST_SIZE_MAX = 8 // 8 MB

    let loadMoreFlag = false;
    let timer = null;

    // xử lý cuộn trang
    $(window).scroll(function(e){
        var scrollTop = $(window).scrollTop();
        var docHeight = $(document).height();
        var winHeight = $(window).height();
        var scrollPercent = (scrollTop) / (docHeight - winHeight);
        var scrollPercentRounded = Math.round(scrollPercent*100);

        // hiển thị button cuộn
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

        // load more
        if(scrollPercentRounded >= 80){
            if(!loadMoreFlag){
                loadMoreFlag = true;
                $('#loadmore').show();

                if(page === 'donhang'){
                    if($('#lst_data').attr('data-loadmore') !== 'done'){
                        var row = $('#lst_data').children().length;
                        var sort = $('input[name="sort"]:checked').val();
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/ajax-load-more',
                            type: 'POST',
                            data: {'page': page, 'row': row, 'sort': sort},
                            success:function(data){
                                loadMoreFlag = false;
                                if(data !== 'done'){
                                    const html = renderNewRow(page, data)
                                    $('#lst_data').append(html);
                                } else {
                                    $('#lst_data').attr('data-loadmore', 'done');
                                    $('#loadmore').hide();
                                }
                            }
                        });
                    } else {
                        $('#loadmore').hide();
                    }
                } else if(page === 'sanpham'){
                    if($('#lst_data').attr('data-loadmore') !== 'done'){
                        var row = $('#lst_data').children().length;
                        var sort = $('input[name="sort"]:checked').val();
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/ajax-load-more',
                            type: 'POST',
                            data: {'page': page, 'row': row, 'sort': sort},
                            success:function(data){
                                loadMoreFlag = false;
                                if(data !== 'done'){
                                    const html = renderNewRow(page, data);
                                    $('#lst_data').append(html);
                                } else {
                                    $('#lst_data').attr('data-loadmore', 'done');
                                    $('#loadmore').hide();
                                }
                            }
                        });
                    } else {
                        $('#loadmore').hide();
                    }
                } else if(page === 'imei'){
                    if($('#lst_data').attr('data-loadmore') !== 'done'){
                        var row = $('#lst_data').children().length;
                        var keyword = $('#search').val().toLocaleLowerCase();
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/ajax-load-more',
                            type: 'POST',
                            data: {'page': page, 'row': row, 'keyword': keyword},
                            success:function(data){
                                loadMoreFlag = false;
                                if(data !== 'done'){
                                    const html = renderNewRow(page, data)
                                    $('#lst_data').append(html);
                                } else {
                                    $('#lst_data').attr('data-loadmore', 'done');
                                    $('#loadmore').hide();
                                }
                            }
                        });
                    } else {
                        $('#loadmore').hide();
                    }
                } else {
                    // mảng các trang không cần load thêm dữ liệu
                    const noLoadMoreDataPages = ['', 'slideshow', 'banner']

                    if(noLoadMoreDataPages.indexOf(page) == -1) {
                        if($('#lst_data').attr('data-loadmore') !== 'done'){
                            var row = $('#lst_data').children().length;
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                                },
                                url: 'admin/ajax-load-more',
                                type: 'POST',
                                data: {'page': page, 'row': row},
                                success:function(data){
                                    loadMoreFlag = false;
                                    if(data !== 'done'){
                                        const html = renderNewRow(page, data)
                                        $('#lst_data').append(html);
                                    } else {
                                        $('#lst_data').attr('data-loadmore', 'done');
                                        $('#loadmore').hide();
                                    }
                                }
                            });
                        } else {
                            $('#loadmore').hide();
                        }
                    }
                }
            }
        }
    });

    // đóng alert top
    $(document).on('click', '.close-alert-top', function(){
        closeAlertTop();
    });
    $(document).on('click', '.close-alert-top-icon', function(){
        closeAlertTop();
    });
    
    // xử lý cuộn lên đầu trang
    $('#btn-scroll-top').on('click', function(){
        $(window).scrollTop(0);
    });

    /*=======================================================================================================================
                                                           Function
    =======================================================================================================================*/

    function pcRequire() {
        const html =
            `<div class="pc-require-background">
                <div class="pc-require-wrapper">
                    <img src="images/pc-require.png" alt="pc-require" class="pc-require-image">
                    <div class="pc-require-text"></div>
                </div>
            </div>`

        $('body').prepend(html)

        $('html body').css('overflow', 'hidden')
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
            $('.alert-top').remove();
            $('.backdrop').removeAttr('style');
        }, 500);
        $('.alert-top').css({
            '-ms-transform': 'translateY(-500px)',
            'transform': 'translateY(-500px)',
        });
        $('.backdrop').fadeOut();
    }

    // hiển thị toast
    function showToast(message, type){
        if($('#alert-toast').length) {
            $('#alert-toast').remove();
        }

        if(type === TOAST_SUCCESS_TYPE) {
            $('body').prepend(`<span id="alert-toast" class="alert-toast alert-toast-success">${message}</span>`)
        } else {
            $('body').prepend(`<span id="alert-toast" class="alert-toast alert-toast-danger">${message}</span>`)
        }

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

    function removeRequried(element){
        if(element.hasClass('required')){
            element.removeClass('required');
            element.next().remove();
        }
    }

    // kiểm nhập tên
    function validateName(Name){
        // nếu đã kiểm tra rồi thì return
        if(Name.hasClass('required')){
            $('.modal-body').animate({scrollTop: Name.position().top});
            return false;
        }

        const value = Name.val().trim()

        // nếu chưa nhập tên
        if(value.length == 0){
            var required = $('<span class="required-text">Vui lòng nhập tên</span>');
            Name.addClass('required');
            Name.after(required);
            $('.modal-body').animate({scrollTop: Name.position().top});
            return false;
        }

        // tên không hợp lệ
        if(!isNaN(value)){
            var required = $('<span class="required-text">Tên không hợp lệ</span>');
            Name.addClass('required');
            Name.after(required);
            $('.modal-body').animate({scrollTop: Name.position().top});
            return false;
        }

        return true;
    }

    // kiểm tra số điện thoại
    function validatePhoneNumber(tel){
        // nếu đã kiểm tra rồi thì return
        if(tel.hasClass('required')){
            return false;
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
    function valiPhonenumberTyping(tel){
        if(tel.hasClass('required')){
            tel.next().remove();
        }

        var phoneno = /^\d{10}$/;
        var required; 

        // chưa nhập
        if(tel.val() == ''){
            required = $('<span class="required-text">Vui lòng nhập số diện thoại</span>');
            tel.addClass('required');
            tel.after(required);
        }
        // không đúng định dạng | ký tự đầu k phải số 0 
        else if(!tel.val().match(phoneno) || tel.val().charAt(0) != 0){
            if(tel.next().hasClass('required-text')){
                return;
            }
            required = $('<span class="required-text">Số diện thoại không hợp lệ</span>');    
            tel.addClass('required');
            tel.after(required);
        } 
        // hợp lệ
        else {
            tel.removeClass('required');
            tel.next().remove();
        }
    }

    // bẫy lỗi địa chỉ
    function validateAddress(address) {
        if(address.hasClass('required')){
            $('.modal-body').animate({scrollTop: address.position().top});
            return false;
        }

        const value = address.val().trim()

        // chưa nhập
        if(value == ''){
            address.addClass('required');
            address.after('<span class="required-text">Vui lòng nhập địa chỉ</span>');
            $('.modal-body').animate({scrollTop: address.position().top});
            return false;
        }

        // không hợp lệ
        if(!isNaN(value)){
            address.addClass('required');
            address.after('<span class="required-text">Địa chỉ không hợp lệ</span>');
            $('.modal-body').animate({scrollTop: address.position().top});
            return false;
        }

        return true;
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

    // kiểm tra email
    function validateEmail(email) {
        if(email.hasClass('required')){
            return false;
        }

        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        const isValid =  re.test(String(email.val().trim()).toLowerCase());

        // không hợp lệ
        if(!isValid) {
            email.addClass('required');
            email.after('<span class="required-text">Email không hợp lệ<span>');
            return false;
        }

        return true;
    }

    // bẫy lỗi chiết khẩu
    function validateDiscount(discount) {
        if(discount.hasClass('required')){
            return false;
        }

        const value = discount.val()

        // chưa nhập
        if(value == ''){
            discount.addClass('required');
            discount.after('<span class="required-text">Vui lòng nhập chiết khẩu</span>');
            return false;
        }

        // không hợp lệ
        if(value < 1 || value > 100){
            discount.addClass('required');
            discount.after('<span class="required-text">Chiết khấu không hợp lệ</span>');
            return false;
        }

        return true;
    }

    // bẫy lỗi ngày bắt đầu
    function validateDateStart(date) {
        if(date.hasClass('required')){
            return false;
        }

        // chưa nhập
        if(date.val() == ''){
            date.addClass('required');
            date.after('<span class="required-text">Vui lòng chọn ngày bắt đầu</span>');
            return false;
        }

        return true;
    }

    // bẫy lỗi ngày kết thúc
    function validateDateEnd(dateEnd, dateStart) {
        if(dateEnd.hasClass('required')){
            return false;
        }

        // chưa chọn
        if(dateEnd.val() == ''){
            dateEnd.addClass('required');
            dateEnd.after('<span class="required-text">Vui lòng chọn ngày kết thúc</span>');
            return false;
        }

        // ngày kết thúc < ngày bắt đầu
        if(dateStart.val() != ''){
            if(dateEnd.val() < dateStart.val()){
                dateEnd.addClass('required');
                dateEnd.after('<span class="required-text">Ngày kết thúc không hợp lệ</span>');
                return false;
            }
        }

        return true;
    }

    function capitalize (text) {
        var textArray = text.split(' ');
        var capitalizedText = '';
        for (var i = 0; i < textArray.length; i++) {
          capitalizedText += textArray[i].charAt(0).toUpperCase() + textArray[i].slice(1) + ' '
        }
        return capitalizedText.trim();
    }

    function numberWithDot(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function orderStatusPercent(){
        // tổng số lượng đơn hàng
        var total = $('#total-order').val();
        // số lượng đơn tiếp nhận
        var receivedQty = $('#received-order').data('qty');
        // số lượng đơn xác nhận
        var confirmedQty = $('#confirmed-order').data('qty');
        // số lượng đơn thành công
        var successQty = $('#successfull-order').data('qty');
        // số lượng đơn đã hủy
        var cancelledQty = $('#cancelled-order').data('qty');
        console.log(total, receivedQty, confirmedQty, successQty, cancelledQty);

        var avg = 0;
        // progress bar tiếp nhận
        avg = (receivedQty / total) * 100;
        $('.received-progress-bar').css('width', avg + '%');

        // progress bar xác nhận
        avg = (confirmedQty / total) * 100;
        $('.confirmed-progress-bar').css('width', avg + '%');

        // progress bar thành công
        avg = (successQty / total) * 100;
        $('.success-progress-bar').css('width', avg + '%');

        // progress bar tiếp nhận
        avg = (cancelledQty / total) * 100;
        $('.cancelled-progress-bar').css('width', avg + '%');
    }

    function renderNewRow(page, data) {
        let html = ''
        switch (page) {
            case 'donhang':
                $.each(data, (i, val) => {
                    html += 
                    `<tr data-id="${val.id}">
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.id}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.thoigian}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.fullname}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.pttt}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.hinhthuc}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${numberWithDot(val.tongtien)}<sup>đ</sup></div>
                        </td>
                        <td class="vertical-center">
                            <div data-id="${val.id}" class="trangthaidonhang pt-10 pb-10">${val.trangthaidonhang}</div>
                        </td>
                        <td class="vertical-center w-5">
                            <div class="d-flex justify-content-start">
                            ${val.trangthaidonhang != 'Thành công' && val.trangthaidonhang != 'Đã hủy' ?
                                    val.trangthaidonhang == 'Đã tiếp nhận' ? 
                                        `<div data-id="${val.id}" class="confirm-btn">
                                            <i class="fas fa-file-check"></i>
                                        </div>` :
                                        `<div data-id="${val.id}" class="success-btn">
                                            <i class="fas fa-box-check"></i>
                                        </div>`
                                    : ''
                            } 
                            <div data-id="${val.id}" class="info-btn"><i class="fas fa-info"></i></div>
                            ${val.trangthaidonhang != 'Đã hủy' && val.trangthaidonhang != 'Thành công' ?
                                `<div data-id="${val.id}" class="delete-btn"><i class="fas fa-trash"></i></div>`
                                : ''
                            }
                            </div>
                        </td>
                    </tr>`
                })
                break
            case 'sanpham':
                $.each(data, (i, val) => {
                    html += 
                    `<tr data-id="${val.id}">
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.id}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.tensp}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.mausac}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.ram}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.dungluong}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${numberWithDot(val.gia)}<sup>đ</sup></div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.promotion}</div>
                        </td>
                        <td class="vertical-center">
                            <div data-id="${val.id}" class="trangthai pt-10 pb-10">${val.trangthai == 1 ? 'Kinh doanh' : 'Ngừng kinh doanh'}</div>
                        </td>
                        <td class="vertical-center w-10">
                            <div class="d-flex justify-content-start">
                                <div data-id="${val.id}" class="info-btn"><i class="fas fa-info"></i></div>
                                <div data-id="${val.id}" class="edit-btn"><i class="fas fa-pen"></i></div>
                                ${val.trangthai == 1 ?
                                    `<div data-id="${val.id}" data-name="${val.tensp} ${val.dungluong} - ${val.ram} Ram - ${val.mausac}" class="delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </div>`
                                    :
                                    `<div data-id="${val.id}" data-name="${val.tensp} ${val.dungluong} - ${val.ram} Ram - ${val.mausac}" class="undelete-btn">
                                        <i class="fas fa-trash-undo"></i>
                                    </div>`
                                }
                            </div>
                        </td>
                    </tr>`
                })
                break
            case 'imei':
                $.each(data, (i, val) => {
                    html += 
                        `<tr data-id="${val.id}">
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.id}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="d-flex pt-10 pb-10">
                                    <img src="images/phone/${val.product.hinhanh}" alt="" width="70px">
                                    <div class="ml-10">
                                        <div class="d-flex align-items-center fw-600">
                                            ${val.product.tensp}
                                            <i class="fas fa-circle fz-5 ml-5 mr-5"></i>
                                            ${val.product.mausac}
                                        </div>
                                        <div>Ram: ${val.product.ram}</div>
                                        <div>Dung lượng: ${val.product.dungluong}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.imei}</div>
                            </td>
                            <td class="vertical-center">
                                <div data-id="${val.id}" class="trangthai pt-10 pb-10">${val.trangthai == 1 ? 'Đã kích hoạt' : 'Chưa kích hoạt'}</div>
                            </td>
                        </tr>`
                })
                break
            case 'mausanpham':
                $.each(data, (i, val) => {
                    html +=
                     `<tr data-id="${val.id}">
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.id}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.tenmau}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.supplierName}</div>
                        </td>
                        <td class="vertical-center">
                            <div class="pt-10 pb-10">${val.baohanh ? val.baohanh : 'Không có'}</div>
                        </td>
                        <td class="vertical-center w-30">
                            <div class="pt-10 pb-10">${val.diachibaohanh ? val.diachibaohanh : ''}</div>
                        </td>
                        <td class="vertical-center w-15">
                            <div data-id="${val.id}" class="trangthai pt-10 pb-10">${val.trangthai == '1' ? 'Kinh doanh' : 'Ngừng kinh doanh'}</div>
                        </td>
                        <td class="vertical-center w-10">
                            <div class="d-flex justify-content-start">
                                <div data-id="${val.id}" class="info-btn"><i class="fas fa-info"></i></div>
                                <div data-id="${val.id}" class="edit-btn"><i class="fas fa-pen"></i></div>
                                ${val.trangthai != 0 ?
                                    `<div data-id="${val.id}" class="delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </div>`
                                    :
                                    `<div data-id="${val.id}" class="undelete-btn"><i class="fas fa-trash-undo"></i></div>`
                                }
                            </div>
                        </td>
                    </tr>`
                })
                break
            case 'khuyenmai':
                $.each(data, (i, val) => {
                    html +=
                        `<tr data-id="${val.id}">
                            <td class="vertical-center w-5">
                                <div class="pt-10 pb-10">${val.id}</div>
                            </td>
                            <td class="vertical-center w-15">
                                <div class="pt-10 pb-10">${val.tenkm}</div>
                            </td>
                            <td class="vertical-center w-24">
                                <div class="pt-10 pb-10">${val.noidung}</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="pt-10 pb-10">${val.chietkhau*100}%</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="pt-10 pb-10">${val.ngaybatdau}</div>
                            </td>
                            <td class="vertival-center w-11">
                                <div class="pt-10 pb-10">${val.ngayketthuc}</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div data-id="${val.id}" class="trangthai pt-10 pb-10">${val.status}</div>
                            </td>
                            <td class="vertical-center w-15">
                                <div class="d-flex justify-content-start">
                                    <div data-id="${val.id}" class="info-khuyenmai-btn info-btn"><i class="fas fa-info"></i></div>
                                    <div data-id="${val.id}" class="edit-khuyenmai-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                                    <div data-id="${val.id}" data-object="khuyenmai" class="delete-khuyenmai-btn delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </div>
                                </div>
                            </td>
                        </tr>`
                })
                break
            case 'nhacungcap':
                $.each(data, (i, val) => {
                    html +=
                        `<tr data-id="${val.id}">
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.id}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.tenncc}</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="pt-10 pb-10">
                                    <img src="images/logo/${val.anhdaidien}?${new Date().getMilliseconds()}" alt="">
                                </div>
                            </td>
                            <td class="vertical-center w-25">
                                <div class="pt-10 pb-10">${val.diachi}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.sdt}</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="pt-10 pb-10">${val.email}</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div data-id="${val.id}" class="trangthai pt-10 pb-10">${val.trangthai == 1 ? 'Hoạt động' : 'Ngừng kinh doanh'}</div>
                            </td>
                            <td class="vertical-center w-5">
                                <div class="d-flex justify-content-start">
                                    <div data-id="${val.id}" class="info-btn"><i class="fas fa-info"></i></div>
                                    <div data-id="${val.id}" class="edit-btn"><i class="fas fa-pen"></i></div>
                                    ${val.trangthai == 1 ?
                                        `<div data-id="${val.id}" data-name="${val.tenncc}" class="delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </div>`
                                        :
                                        `<div data-id="${val.id}" data-name="${val.tenncc}" class="undelete-btn">
                                            <i class="fas fa-trash-undo"></i>
                                        </div>`
                                    }
                                </div>
                            </td>
                        </tr>`
                })
                break
            case 'slideshow-msp':
                $.each(data, (i, val) => {
                    html +=
                        `<tr data-id="${val.id}">
                            <td class="vertical-center w-50">
                                <div class="pt-10 pb-10">${val.tenmau}</div>
                            </td>
                            <td class="vertical-center">
                            <div data-id="${val.id}" class="qty-image pt-10 pb-10">${val.slideQty} hình</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="d-flex justify-content-start">
                                    <div data-id="${val.id}" class="info-btn"><i class="fas fa-info"></i></div>
                                    <div data-id="${val.id}" class="edit-btn"><i class="fas fa-pen"></i></div>
                                    ${val.slideQty !== 0 ?
                                    `<div data-id="${val.id}" data-name="${val.tenmau}" class="delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </div>` : ''}
                                </div>
                            </td>
                        </tr>`
                })
                break
            case 'hinhanh':
                $.each(data, (i, val) => {
                    html +=
                        `<tr data-id="${val.id}">
                            <td class="vertical-center w-50">
                                <div class="pt-10 pb-10">${val.tenmau}</div>
                            </td>
                            <td class="vertical-center">
                                <div data-id="${val.id}" class="qty-image pt-10 pb-10">${val.imageQty} Hình</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="d-flex justify-content-start">
                                    <div data-id="${val.id}" class="info-btn"><i class="fas fa-info"></i></div>
                                    <div data-id="${val.id}" class="edit-btn"><i class="fas fa-pen"></i></div>
                                    ${val.imageQty !== 0 ?
                                    `<div data-id="${val.id}" data-name="${val.tenmau}" class="delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </div>` : ''}
                                </div>
                            </td>
                        </tr>`
                })
                break
            case 'kho':
                $.each(data, (i, val) => {
                    html +=
                        `<tr data-id="${val.id}">
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.id}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.branchAddress}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="d-flex pt-10 pb-10">
                                    <img src="images/phone/${val.product.hinhanh}" alt="" width="80px">
                                    <div class="ml-5 fz-14">
                                        <div class="d-flex align-items-center fw-600">
                                            ${val.product.tensp}<i class="fas fa-circle ml-5 mr-5 fz-5"></i>${val.product.mausac}
                                        </div>
                                        <div>Ram: ${val.product.ram}</div>
                                        <div>Dung lượng: ${val.product.dungluong}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.slton} Chiếc</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="d-flex justify-content-start">
                                    <div data-id="${val.id}" class="edit-btn"><i class="fas fa-pen"></i></div>
                                    <div data-id="${val.id}" data-branch="${val.branchAddress}" class="delete-btn"><i class="fas fa-trash"></i></div>
                                </div>
                            </td>
                        </tr>`
                })
                break
            case 'chinhanh':
                $.each(data, (i, val) => {
                    html +=
                        `<tr data-id="${val.id}">
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.id}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.diachi}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.sdt}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.cityName}</div>
                            </td>
                            <td class="vertical-center">
                                <div data-id="${val.id}" class="trangthai pt-10 pb-10">${val.trangthai == 1 ? 'Hoạt động' : 'Ngừng hoạt động'}</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="d-flex justify-content-start">
                                    <div data-id="${val.id}" class="edit-btn"><i class="fas fa-pen"></i></div>
                                    ${val.trangthai != 0 ?
                                        `<div data-id="${val.id}" class="delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </div>`
                                        :
                                        `<div data-id="${val.id}" class="undelete-btn">
                                            <i class="fas fa-trash-undo"></i>
                                        </div>`
                                    }
                                </div>
                            </td>
                        </tr>`
                })
                break
            case 'tinhthanh':
                $.each(data, (i, val) => {
                    html +=
                        `<tr data-id="${val.id}">
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.id}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.tentt}</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="d-flex justify-content-start">
                                    <div data-id="${val.id}" class="edit-btn"><i class="fas fa-pen"></i></div>
                                    <div data-id="${val.id}" data-name="${val.tentt}" class="delete-btn"><i class="fas fa-trash"></i></div>
                                </div>
                            </td>
                        </tr>`
                })
                break
            case 'voucher':
                $.each(data, (i, val) => {
                    html +=
                        `<tr data-id="${val.id}">
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.id}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.code}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.chietkhau*100}%</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.ngaybatdau}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.ngayketthuc}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.sl}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.status}</div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="d-flex justify-content-start">
                                    <div data-id="${val.id}" class="info-btn"><i class="fas fa-info"></i></div>
                                    <div data-id="${val.id}" class="edit-btn"><i class="fas fa-pen"></i></div>
                                    <div data-id="${val.id}" class="delete-btn"><i class="fas fa-trash"></i></div>
                                </div>
                            </td>
                        </tr>`
                })
                break
            case 'baohanh':
                $.each(data, (i, val) => {
                    html +=
                        `<tr data-id="${val.id}">
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.id}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.imei}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.ngaymua}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.ngayketthuc}</div>
                            </td>
                            <td class="vertical-center w-5">
                                <div class="d-flex justify-content-start">
                                    <div data-id="${val.id}" class="info-btn"><i class="fas fa-info"></i></div>
                                </div>
                            </td>
                        </tr>`
                })
                break
            case 'slideshow':
                $.each(data, (i, val) => {
                    html +=
                        `<tr data-id="${val.id}">
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.id}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.link}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">
                                    <img src="images/slideshow/${val.hinhanh}" alt="" width="300px">
                                </div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="d-flex justify-content-start">
                                    <div data-id="${val.id}" class="info-btn"><i class="fas fa-info"></i></div>
                                    <div data-id="${val.id}" class="edit-btn"><i class="fas fa-pen"></i></div>
                                    <div data-id="${val.id}" class="delete-btn"><i class="fas fa-trash"></i></div>
                                </div>
                            </td>
                        </tr>`
                })
                break
            case 'banner':
                $.each(data, (i, val) => {
                    html +=
                        `<tr data-id="${val.id}">
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.id}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${val.link}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">
                                    <img src="images/banner/${val.hinhanh}" alt="" width="300px">
                                </div>
                            </td>
                            <td class="vertical-center w-10">
                                <div class="d-flex justify-content-start">
                                    <div data-id="${val.id}" class="info-btn"><i class="fas fa-info"></i></div>
                                    <div data-id="${val.id}" class="edit-btn"><i class="fas fa-pen"></i></div>
                                    <div data-id="${val.id}" class="delete-btn"><i class="fas fa-trash"></i></div>
                                </div>
                            </td>
                        </tr>`
                })
                break
        }

        return html
    }

    function toastAndHighlight(messageToast, id) {         
        switch (messageToast) {
            case CREATE_MESSAGE:
            case EDIT_MESSAGE:
                showToast(messageToast, TOAST_SUCCESS_TYPE);
                highlightRow(id, SUCCESS)
                break
            case DELETE_MESSAGE:
                showToast(messageToast, TOAST_DELETE_TYPE);
                highlightRow(id, DANGER)
                break
        }
    }

    function pushBase64ToArray(type) {
        return new Promise(resolve => {
            let array = []
            const childs = $('.image-preview-div > .row').children()

            if(type === 'create') {
                const length = $('.image-preview-div > .row').children().length

                childs.each((i, child) => {
                    const id = $(child).attr('data-id')
                    const imageSRC = $(`.image_preview_img[data-id="${id}"]`).attr('src')
                    getBase64FromUrl(imageSRC)
                        .then(base64 => {
                            array.push(base64)
                            if(array.length === length) {
                                resolve(array)
                            }
                        })
                })
            } else {
                let newImageQty = 0
                childs.each((i, child) => {
                    if(!$(child).attr('data-name')) {
                        newImageQty++
                    }
                })

                if(newImageQty === 0) {
                    resolve(array)
                } else {
                    childs.each((i, child) => {
                        if(!$(child).attr('data-name')) {
                            const id = $(child).attr('data-id')
                            const imageSRC = $(`.image_preview_img[data-id="${id}"]`).attr('src')
                            getBase64FromUrl(imageSRC)
                                .then(base64 => {
                                    array.push(base64)
                                    if(array.length === newImageQty) {
                                        resolve(array)
                                    }
                                })
                        }
                    })
                }
            }
        })
    }

    function renderImage(slideList, url) {
        return new Promise(resolve => {
            $('.image-preview-div > .row').children().remove();

            let slide = slideList.map((val, i) => {
                return (
                    `<div id="image-${i + 1}" data-id="${i + 1}" data-name="${val.hinhanh}" class="col-lg-4 col-6">
                        <div class="image-preview">
                            <div class="overlay-image-preview"></div>
                            <div data-id="${i + 1}" class="delete-image-preview"><i class="far fa-times-circle fz-40"></i></div>
                            <img data-id="${i + 1}" class="image_preview_img" src="${url + val.hinhanh}" alt="${val.hinhanh}">
                        </div>
                    </div>`
                )
            }).join('')

            idx = slideList.length + 1

            $('.image-preview-div > .row').append(slide)

            resolve()
        })
    }

    function assignModel(modelList, id) {
        return new Promise((resolve, reject) => {
            $('#model').children().remove();

            let option = modelList.map((val) => {
                return (
                    `<option value="${val.id}">${val.tenmau}</option>`
                )
            })

            $('#model').append(option)

            $(`#model option[value="${id}"]`).prop('selected', true);

            resolve()
        })
    }

    function highlightRow(id, color) {
        const row = $('tr[data-id="'+id+'"]'); 

        if(!row) return

        setTimeout(() => {
            setTimeout(() => {
                row.removeAttr('style');    
            }, 1000);
            row.css({
                'background-color': 'white',
            });    
        }, 3000);
        $('html, body').animate({scrollTop: row.position().top});
        row.css({
            'background-color': color,
            'transition': '.5s'
        });
    }
    
    /*=======================================================================================================================
                                                           Header
    =======================================================================================================================*/
    
    // đóng/mở sidebar menu
    $('.sidebar').hover(function(){
        clearTimeout(timer);
        timer = setTimeout(() => {
            setTimeout(() => {
                $('.sidebar-content').css({
                    'width': '180px',
                    'display': 'flex',
                    'align-items': 'center',
                });
            }, 250);
            
            $('.sidebar').css({
                'width': '250px',
                'box-shadow': '20px 0 20px rgb(0, 0, 0, 0.13)'
            });
        }, 200);
    }, function(){
        clearTimeout(timer);
        timer = setTimeout(() => {
            $('.sidebar-content').css({
                'width': '0',
                'display': 'none',
            });
            
            $('.sidebar').removeAttr('style');
        }, 200);
    });
    
    // đóng/mở tùy chọn tài khoản
    $('#btn-expand-account').click(function(){
        $('.account-option').toggle('blind', 300);
    });

    /*=======================================================================================================================
                                                           Page
    =======================================================================================================================*/

    switch(page) {
        /*=======================================================================================================================
                                                            Dashboard
        =======================================================================================================================*/
        case '': {
            // phần trăm trạng thái đơn hàng
            orderStatusPercent();

            // area chart
            var arrSalesData = $('#sales-data').val().split('-');
            var donutData = $('#donut-data').val();
        
            var arr = [];
            $.each(JSON.parse(donutData), function (i, key){ 
                arr.push( { label: i.replace('Việt Nam', ''), value: key })
            })

            var salesChart = new Chart($('#sales-chart')[0], {
                type: 'line',
                data: {
                    labels: [
                        'T1',
                        'T2',
                        'T3',
                        'T4',
                        'T5',
                        'T6',
                        'T7',
                        'T8',
                        'T9',
                        'T10',
                        'T11',
                        'T12',
                    ],
                    datasets: [{
                        label: 'Doanh thu',
                        backgroundColor: '#9EF1F4',
                        borderColor: '#9EF1F4',
                        fill: {
                            target: 'origin',
                            above: '#9EF1F4',
                            below: '#9EF1F4'
                        },
                        data: arrSalesData
                    }]
                }, options: {
                }
            });

            // thay đổi năm thống kê
            $('#sales-year').change(function(){
                var year = $(this).val();
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/ajax-get-sales-of-year',
                    type: 'POST',
                    data: {'year': year},
                    success: function(data){
                        console.log(data);
                        // không có dữ liệu
                        if(data == ''){
                            setTimeout(() => {
                                if(!$('#sales-chart').next().length){
                                    var elmnt = $('<div class="fz-20 text-center">Không có dữ liệu</div>');
                                    elmnt.show('fade');
                                    $('#sales-chart').after(elmnt);
                                }
                            }, 300);
                            $('#sales-chart').hide();
                        } else {
                            $('#sales-chart').next().remove();
                            $('#sales-chart').show();

                            data = data.split('-');
                            salesChart.data.datasets[0].data = data;
                            salesChart.reset();
                            salesChart.update();
                        }
                        
                    }
                })
            });
            $('#branch-year').change(function(){
                var year = $(this).val();
                var today = new Date();
                var currentYear = today.getFullYear();
                var dateFirstOfYear = year +"-01-01";
                var date = "";
                if(currentYear != year){
                    date = year + "12-31";
                }else {
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = today.getFullYear();
                    date = yyyy + '-' + mm + '-' + dd;
                }
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/ajax-get-supplier-of-year',
                    type: 'POST',
                    data: {'dateFirstOfYear': dateFirstOfYear,'currentDate': date},
                    success: function(data){
                        console.log(data);
                        // không có dữ liệu
                        if(data.length==0){
                            $('#no-data').attr('hidden', true);
                            setTimeout(() => {
                                if(!$('#branch-chart').next().length){
                                    var elmnt = $('<div class="pt-50 fz-20 text-center" style="padding-bottom: 35%">Không có dữ liệu</div>');
                                    elmnt.show('fade');
                                    $('#branch-chart').after(elmnt);
                                }
                            }, 300);
                            
                            $('#branch-chart').hide();
                        } else {
                            $('#branch-chart').next().remove();
                            $('#branch-chart').show();
                            window.areaChart.data = data;
                        }
                        
                    }
                })
            });
            /*Donut chart*/
            window.areaChart = Morris.Donut({
                element: 'branch-chart',
                redraw: true,
                data: arr,
                colors: ['#34495E', '#eb4a00', '#004ddb', '#db0000', '#dba800', '#db005b', '#06972a']
            });

            break
        }
        /*=======================================================================================================================
                                                            Mẫu sp
        =======================================================================================================================*/
        case 'mausanpham': {
            if(navigation === 'reload' || navigation === 'back_forward'){
                loadMoreFlag = true;
            }
    
            // hiển thị modal tạo mới mẫu sp
            $('.create-btn').off('click').click(function(){
                // gán dữ liệu cho modal
                $('#modal-title').text('Tạo mới mẫu sản phẩm');
    
                // trạng thái = 1
                $('#mausp_status').hide();
                $('label[for="mausp_status"]').hide();
    
                // thiết lập nút gửi là thêm mới
                $('#action-btn').attr('data-type', 'create');
                $('#action-btn').text('Thêm');
    
                // hiển thị modal
                $('#modal').modal('show');
            });
    
            // modal xem chi tiết mẫu sp
            $(document).on('click', '.info-btn', function(){
                var id = $(this).attr('data-id');
                $('#modal-title').text('Chi tiết mẫu sản phẩm');
                bindMausp(id, true);
            });
    
            // hiển thị modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chỉnh sửa mẫu sản phẩm');
                bindMausp(id);
            });
    
            // hiển thị modal xóa
            $(document).on('click', '.delete-btn', function(){
                // gán dữ liệu cho modal xóa
                $('#delete-content').text('Xóa mẫu sản phẩm này?');
                $('#delete-btn').attr('data-id', $(this).data('id'));
                $('#delete-modal').modal('show');
            });
    
            // thêm|sửa
            $(document).on('click', '#action-btn', function(){
                // bẫy lỗi
                var valiName = validateName($('#mausp_name'));
    
                // bẫy lỗi xong kiểm tra loại
                if(valiName){
                    var tenmau = $('#mausp_name').val();
                    var id_ncc = $('#mausp_supplier').val();
                    var id_youtube = $('#mausp_youtube').val();
                    var baohanh = $('#mausp_warranty').val();
                    var diachibaohanh = $('#mausp_warranty_address').val().trim();
                    var trangthai = $('#mausp_status').val();
    
                    var data = {
                        'tenmau': tenmau,
                        'id_ncc': id_ncc,
                        'id_youtube': id_youtube,
                        'baohanh': baohanh,
                        'diachibaohanh': diachibaohanh,
                        'trangthai': trangthai,
                    };
                    
                    $('.loader').fadeIn();
    
                    // thêm mới
                    if($(this).attr('data-type') === 'create'){
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/mausanpham',
                            type: 'POST',
                            data:data,
                            success:function(data){
                                $('.loader').fadeOut();
    
                                // trùng tên
                                if(data === 'invalid name'){
                                    $('#mausp_name').addClass('required');
                                    $('#mausp_name').after('<span class="required-text">Tên mẫu sản phẩm đã tồn tại</span>');
                                    return;
                                }
    
                                $('#modal').modal('hide');
    
                                // render dòng mới vào view
                                const html = renderNewRow(page, data.data)
                                $('#lst_data').prepend(html);
    
                                // toast + highlight row
                                toastAndHighlight(CREATE_MESSAGE, data.id)
                                
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                    // chỉnh sửa
                    else {
                        var id = $(this).attr('data-id');
    
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/mausanpham/'+id,
                            type: 'PUT',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                $('#modal').modal('hide');
    
                                // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                                const html = renderNewRow(page, data)
                                $(`tr[data-id="${id}"]`).replaceWith(html);
                                
                                // toast + highlight row
                                toastAndHighlight(EDIT_MESSAGE, id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
    
                    $('.loader').fadeOut();
                }
            });
    
            // xóa
            $(document).on('click', '#delete-btn', function(){
                var id = $(this).attr('data-id');
    
                $('.loader').fadeIn();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/mausanpham/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');
    
                        // nút khôi phục
                        var restoreBtn = $(`<div data-id="${id}" class="undelete-btn"><i class="fas fa-trash-undo"></i></div>`);
                        $(`.delete-btn[data-id="${id}"]`).replaceWith(restoreBtn);
    
                        // cập nhật trạng thái
                        $(`.trangthai[data-id="${id}"]`).text('Ngừng kinh doanh');
    
                        // toast + highlight row
                        toastAndHighlight(DELETE_MESSAGE, id)
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            });
    
            // phục hồi
            $(document).on('click', '.undelete-btn', function(){
                var id = $(this).attr('data-id');
                restore(id);
            });
    
            $('#mausp_name').keyup(function(){
                removeRequried($(this));
            });
    
            $('#mausp_youtube').keyup(function(){
                if($(this).val() == ''){
                    setTimeout(() => {
                        $('#youtube_iframe').hide();
                    }, 500);
                    return;
                }
                showYoutubeVideo($(this).val());
            });
    
            // reset modal mausp
            $('#modal').on('hidden.bs.modal', function(){
                $('#mausp-form').trigger('reset');
                $('input, textarea').attr('readonly', false);
                $('select').attr('disabled', false);
                $('#youtube_iframe').attr('src', '');
                $('#youtube_iframe').hide();
                removeRequried($('#mausp_name'));
                $('#action-btn').show();
            });
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    if(Object.keys(arrFilter).length != 0){
                        return filter();
                    }
                    const keyword = $(this).val().toLocaleLowerCase();
    
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'admin/mausanpham/ajax-search',
                        type: 'POST',
                        data: {'keyword': keyword},
                        success:function(data){
                            if($('#lst_data').attr('data-loadmore') === 'done') {
                                $('#lst_data').removeAttr('data-loadmore')
                            }
    
                            $('#loadmore').hide();
                            const searchResult = renderNewRow(page, data)
                            $('#lst_data').append(searchResult);
    
                            loadMoreFlag = keyword == '' ? false : true;
                        },
                        error: function() {
                            $('#loadmore').hide();
                            showAlertTop(errorMessage)
                        }
                    });
                },300);
    
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });
            $('#search').focus(function() {
                $('.filter-div').hide('blind');
            })
    
            var arrFilter = {};
            // show lọc
            $('#filter-mausp').click(function(){
                $('.filter-div').toggle('blind');
            });
    
            // lọc 
            $('[name="filter"]').change(function(){
                var object = $(this).data('object');
    
                if(object == 'supplier'){
                    // thêm
                    if($(this).is(':checked')){
                        if(arrFilter.supplier == null){
                            arrFilter.supplier = [];
                        }
                        arrFilter.supplier.push($(this).val());
                    }
                    // gỡ chọn
                    else {
                        var i = arrFilter.supplier.indexOf($(this).val());
                        arrFilter.supplier.splice(i, 1);
                        if(arrFilter.supplier.length == 0){
                            delete arrFilter.supplier;
                        }
                    }
                } else {
                    // thêm
                    if($(this).is(':checked')){
                        if(arrFilter.status == null){
                            arrFilter.status = [];
                        }
                        arrFilter.status.push($(this).val());
                    }
                    // gỡ chọn
                    else {
                        var i = arrFilter.status.indexOf($(this).val());
                        arrFilter.status.splice(i, 1);
                        if(arrFilter.status.length == 0){
                            delete arrFilter.status;
                        }
                    }
                }
                filter();
            });
    
            function bindMausp(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/admin/mausanpham/ajax-get-mausp',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);
                        $('select').attr('disabled', bool);
    
                        // gán dữ liệu cho modal
                        $('#mausp_name').val(data.tenmau);
    
                        $(`#mausp_supplier option[value="${data.id_ncc}"]`).prop('selected', true);
    
                        $('#mausp_youtube').val(data.id_youtube);
                        showYoutubeVideo(data.id_youtube);
    
                        $(`#mausp_warranty option[value="${data.baohanh}"]`).prop('selected', true);
                        
                        $('#mausp_warranty_address').val(data.diachibaohanh);
    
                        $('#mausp_status').show();
                        $('label[for="mausp_status"]').show();
                        $(`#mausp_status option[value="${data.trangthai}"]`).prop('selected', true);
    
                        // thiết lập nút gửi là cập nhật
                        $('#action-btn').attr('data-type', 'edit');
                        $('#action-btn').text('Cập nhật');
                        $('#action-btn').attr('data-id', id);
    
                        // ẩn/hiện nút thêm (cập nhật);
                        bool == false ? $('#action-btn').show() : $('#action-btn').hide();
    
                        // hiển thị modal
                        $('#modal').modal('show');
                    },
                    error: function() {
                        showAlertTop(errorMessage)
                    }
                });
            }
    
            function showYoutubeVideo(youtubeId) {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    setTimeout(() => {
                        $('#youtube_iframe').show('blind');
                    }, 500);
                    $('#youtube_iframe').attr('src', `https://www.youtube.com/embed/${youtubeId}`);
                }, 500);
            }
    
            function restore(id) {
                $('.loader').fadeIn();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/mausanpham/ajax-restore',
                    type: 'POST',
                    data: {'id': id},
                    success:function(){
                        $('.loader').fadeOut();
    
                        // hiện nút xóa
                        var deleteBtn = $(`<div data-id="${id}" class="delete-btn"><i class="fas fa-trash"></i></div>`);
                        $(`.undelete-btn[data-id="${id}"]`).replaceWith(deleteBtn);
    
                        // cập nhật trạng thái
                        $(`.trangthai[data-id="${id}"]`).text('Kinh doanh');
    
                        // toast + highlight row
                        toastAndHighlight(EDIT_MESSAGE, id)
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            }
    
            function filter(){
                $('#lst_data').children().remove();
                $('#loadmore').show();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/mausanpham/ajax-filter',
                    type: 'POST',
                    data: {'arrFilter': arrFilter, 'keyword': $('#search').val().toLocaleLowerCase()},
                    success: function(data){
                        $('#loadmore').hide();
    
                        const html = renderNewRow(page, data)
                        $('#lst_data').append(html);
    
                        if(Object.keys(arrFilter).length == 0){
                            if($('#lst_data').attr('data-loadmore') === 'done') {
                                $('#lst_data').removeAttr('data-loadmore')
                            }
    
                            $('.filter-badge').hide();    
                            loadMoreFlag = false;
                        } else {
                            $('.filter-badge').text(Object.keys(arrFilter).length);
                            $('.filter-badge').show();
                            loadMoreFlag = true;
                        }
                    },
                    error: function() {
                        $('#loadmore').hide();
                        showAlertTop(errorMessage)
                    }
                });
    
            }

            break
        }
        /*=======================================================================================================================
                                                           Khuyến mãi
        =======================================================================================================================*/
        case 'khuyenmai': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            // hiển thị modal tạo mới khuyến mãi
            $('.create-btn').off('click').click(function(){
                // gán dữ liệu cho modal
                $('#modal-title').text('Tạo mới khuyến mãi');
    
                // thiết lập nút gửi là thêm mới
                $('#action-khuyenmai-btn').attr('data-type', 'create');
                $('#action-khuyenmai-btn').text('Thêm');
    
                // hiển thị modal
                $('#khuyenmai-modal').modal('show');
            });
    
            // modal xem chi tiết mẫu sp
            $(document).on('click', '.info-btn', function(){
                var id = $(this).attr('data-id');
                $('#modal-title').text('Chi tiết khuyến mãi');
                bindKhuyenMai(id, true);
            });
    
            // modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chỉnh sửa khuyến mãi');
                bindKhuyenMai(id);
            });
    
            // hiển thị modal xóa
            $(document).on('click', '.delete-btn', function(){
                // gán dữ liệu cho modal xóa
                $('#delete-content').text('Xóa khuyến mãi này?')
                $('#delete-btn').attr('data-object', 'khuyenmai');
                $('#delete-btn').attr('data-id', $(this).data('id'));
                $('#delete-modal').modal('show');
            });
    
            // thêm|sửa
            $('#action-khuyenmai-btn').click(function(){
                // bẫy lỗi
                var valiName = validateName($('#khuyenmai_name'));
                var valiContent = validatePromotionContent($('#khuyenmai_content'));
                var valiDiscount = validateDiscount($('#khuyenmai_discount'));
                var valiStart = validateDateStart($('#khuyenmai_start'));
                var valiEnd = validateDateEnd($('#khuyenmai_end'), $('#khuyenmai_start'));
    
                // bẫy lỗi xong kiểm tra loại
                if(valiName && valiContent && valiDiscount && valiStart && valiEnd){
                    var tenkm = $('#khuyenmai_name').val();
                    var noidung = $('#khuyenmai_content').val();
                    var chietkhau = $('#khuyenmai_discount').val() / 100;
                    var ngaybatdau = $('#khuyenmai_start').val();
                    var ngayketthuc = $('#khuyenmai_end').val();
    
                    var data = {
                        'tenkm': tenkm,
                        'noidung': noidung,
                        'chietkhau': chietkhau,
                        'ngaybatdau': ngaybatdau,
                        'ngayketthuc': ngayketthuc,
                    };
                    
                    $('.loader').fadeIn();
    
                    // thêm mới
                    if($(this).attr('data-type') == 'create'){
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/khuyenmai',
                            type: 'POST',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
    
                                // đã tồn tại khuyến mãi
                                if(data === 'already exist'){
                                    $('#khuyenmai_name').addClass('required');
                                    $('#khuyenmai_name').after('<span class="required-text">Khuyến mãi này đã tồn tại</span>')
                                    return;
                                }
    
                                $('#khuyenmai-modal').modal('hide');
    
                                // render dòng mới vào view
                                const html = renderNewRow(page, data.data)
                                $('#lst_data').prepend(html);
    
                                // toast + highlight row
                                toastAndHighlight(CREATE_MESSAGE, data.id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                    // chỉnh sửa
                    else {
                        var id = $(this).attr('data-id');
    
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/khuyenmai/' + id,
                            type: 'PUT',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
    
                                // đã tồn tại khuyến mãi
                                if(data === 'already exist'){
                                    $('#khuyenmai_name').addClass('required');
                                    $('#khuyenmai_name').after('<span class="required-text">Khuyến mãi này đã tồn tại</span>')
                                    return;
                                }
                                
                                $('#khuyenmai-modal').modal('hide');
    
                                // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                                const newRow = renderNewRow(page, data)
                                $(`tr[data-id="${id}"]`).replaceWith(newRow);
                                
                                // toast + highlight row
                                toastAndHighlight(EDIT_MESSAGE, id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage);
                            }
                        });
                    }
                }
            });
    
            // xóa
            $(document).on('click', '#delete-btn', function(){
                var id = $(this).attr('data-id');
    
                $('.loader').fadeIn();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/khuyenmai/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');
    
                        // xóa dòng
                        $(`tr[data-id="${id}"]`).remove();
    
                        // toast
                        showToast(DELETE_MESSAGE, TOAST_DELETE_TYPE);
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            });
    
            $('#khuyenmai_name').keyup(function(){
                removeRequried($(this));
            });
    
            $('#khuyenmai_content').keyup(function(){
                removeRequried($(this));
            });
    
            $('#khuyenmai_discount').change(function(){
                removeRequried($(this));
            });
    
            $('#khuyenmai_start').click(function(){
                removeRequried($(this));
            });
    
            $('#khuyenmai_end').click(function(){
                removeRequried($(this));
            });
    
            // reset modal
            $('#khuyenmai-modal').on('hidden.bs.modal', function(){
                $('#khuyenmai-form').trigger('reset')
                $('input, textarea').attr('readonly', false)
                $('select').attr('disabled', false)
                $('.required').removeClass('required')
                $('.required-text').remove()
                $('#action-khuyenmai-btn').show();
            });
            
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    var keyword = $(this).val().toLocaleLowerCase();
    
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'admin/khuyenmai/ajax-search',
                        type: 'POST',
                        data: {'keyword': keyword},
                        success:function(data){
                            if($('#lst_data').attr('data-loadmore') === 'done') {
                                $('#lst_data').removeAttr('data-loadmore');
                            }
    
                            const html = renderNewRow(page, data)
                            $('#lst_data').append(html);
    
                            loadMoreFlag = keyword == '' ? false : true;
                            $('#loadmore').hide();
                        },
                        error: function() {
                            $('#loadmore').hide();
                            showAlertTop(errorMessage)
                        }
                    });
                },300);
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });
    
            function bindKhuyenMai(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/admin/khuyenmai/ajax-get-khuyenmai',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);
                        $('select').attr('disabled', bool);
    
                        // gán dữ liệu cho modal
                        $('#khuyenmai_name').val(data.tenkm);
                        $('#khuyenmai_content').val(data.noidung);
                        $('#khuyenmai_discount').val(data.chietkhau * 100);
                        $('#khuyenmai_start').val(data.ngaybatdau);
                        $('#khuyenmai_end').val(data.ngayketthuc);
    
                        // thiết lập nút gửi là cập nhật
                        $('#action-khuyenmai-btn').attr('data-type', 'edit');
                        $('#action-khuyenmai-btn').text('Cập nhật');
                        $('#action-khuyenmai-btn').attr('data-id', id);
    
                        // ẩn/hiện nút thêm (cập nhật);
                        bool == false ? $('#action-khuyenmai-btn').show() : $('#action-khuyenmai-btn').hide();
    
                        // hiển thị modal
                        $('#khuyenmai-modal').modal('show');
                    },
                    error: function () {
                        showAlertTop(errorMessage)
                    }
                });
            }
    
            // bẫy lỗi nội dung khuyến mãi
            function validatePromotionContent(promotion) {
                if(promotion.hasClass('required')){
                    return false;
                }
    
                const value = promotion.val().trim()
    
                // chưa nhập
                if(value == ''){
                    promotion.addClass('required');
                    promotion.after('<span class="required-text">Vui lòng nhập nội dung khuyến mãi</span>');
                    return false;
                }
    
                return true;
            }

            break
        }
        /*=======================================================================================================================
                                                            Sản phẩm
        =======================================================================================================================*/
        case 'sanpham': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            // modal tạo mới
            $('.create-btn').off('click').click(function(){
                // gán dữ liệu cho modal
                $('#modal-title').text('Tạo mới sản phẩm');

                bindSanPham(false, false, 'create')
            });
    
            // modal xem chi tiết
            $(document).on('click', '.info-btn', function(){
                var id = $(this).attr('data-id');
                $('#modal-title').text(`Chi tiết sản phẩm #${id}`);
                bindSanPham(id, true);
            });
    
            // modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).attr('data-id');
                $('#modal-title').text(`Chỉnh sửa sản phẩm #${id}`);
                bindSanPham(id);
            });
    
            // modal xóa
            $(document).on('click', '.delete-btn', function(){
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');
    
                // gán dữ liệu cho modal xóa
                $('#delete-content').html(`Xóa <b>${name}?</b>`);
                $('#delete-btn').attr('data-id', id);
                $('#delete-btn').attr('data-name', name);
                $('#delete-modal').modal('show');
            });
    
            // thêm|sửa
            $('#action-btn').click(function(){
                // bẫy lỗi
                let valiColor = validateColor($('#sanpham_color'));
                let valiPrice = validatePrice($('#sanpham_price'));
                let valiImage = validateProductImage();
    
                // bẫy lỗi xong kiểm tra loại
                if(valiColor & valiPrice & valiImage){
                    let tensp = $('#sanpham_model').find(':selected').text();
                    let id_msp = $('#sanpham_model').val();
                    let mausac = $('#sanpham_color').val();
                    let ram = $('#sanpham_ram').val();
                    let dungluong = $('#sanpham_capacity').val();
                    let gia = $('#sanpham_price').val();
                    let id_km = $('#sanpham_promotion').val();
                    let trangthai = $('#sanpham_status').val();
                    let hinhanh = $('.single-image-selected').attr('data-name').split('?')[0]
                    var cauhinhName = $('#sanpham_specifications').val();
                    var cauhinh = {
                        "thong_so_ky_thuat": {
                            "man_hinh": {
                                "cong_nghe_mh": $('#cong_nghe_mh').val(),
                                "do_phan_giai": $('#do_phan_giai').val(),
                                "ty_le_mh": $('#ty_le_mh').val(),
                                "kinh_cam_ung": $('#kinh_cam_ung').val()
                            },
                            "camera_sau": {
                                "do_phan_giai": $('#cam_sau_do_phan_giai').val(),
                                "quay_phim": [],
                                "den_flash": $('#cam_sau_den_flash').val(),
                                "tinh_nang": []
                            },
                            "camera_truoc": {
                                "do_phan_giai": $('#cam_truoc_do_phan_giai').val(),
                                "tinh_nang": []
                            },
                            "HDH_CPU": {
                                "HDH": $('#HDH').val(),
                                "CPU": $('#CPU').val(),
                                "CPU_speed": $('#CPU_speed').val(),
                                "GPU": $('#GPU').val()
                            },
                            "luu_tru": {
                                "RAM": $('#RAM').val(),
                                "bo_nho_trong": $('#bo_nho_trong').val(),
                                "bo_nho_con_lai": $('#bo_nho_con_lai').val(),
                                "the_nho": $('#the_nho').val()
                            },
                            "ket_noi": {
                                "mang_mobile": $('#mang_mobile').val(),
                                "SIM": $('#SIM').val(),
                                "wifi": [],
                                "GPS": [],
                                "bluetooth": [],
                                "cong_sac": $('#cong_sac').val(),
                                "jack_tai_nghe": $('#jack_tai_nghe').val(),
                                "ket_noi_khac": []
                            },
                            "thiet_ke_trong_luong": {
                                "thiet_ke": $('#thiet_ke').val(),
                                "chat_lieu": $('#chat_lieu').val(),
                                "kich_thuoc": $('#kich_thuoc').val(),
                                "khoi_luong": $('#khoi_luong').val()
                            },
                            "pin": {
                                "loai": $('#loai').val(),
                                "dung_luong": $('#dung_luong').val(),
                                "cong_nghe": []
                            },
                            "tien_ich": {
                                "bao_mat": [],
                                "tinh_nang_khac": [],
                                "ghi_am": $('#ghi_am').val(),
                                "xem_phim": [],
                                "nghe_nhac": []
                            }
                        },
                        "thong_tin_khac": {
                            "thoi_diem_ra_mat": ''
                        }
                    };
    
                    if($('#thoi_diem_ra_mat').val() == ''){
                        cauhinh.thong_tin_khac.thoi_diem_ra_mat = '';    
                    } else {
                        var thoi_diem_ra_mat = $('#thoi_diem_ra_mat').val().split('-'); 
                        cauhinh.thong_tin_khac.thoi_diem_ra_mat = thoi_diem_ra_mat[1] + '/' + thoi_diem_ra_mat[0];
                    }
                
                    // camera sau
                    var cam_sau_quay_phim = $('#cam_sau_quay_phim').val().split('\n');
                    for(var i = 0; i < cam_sau_quay_phim.length; i++){
                        if(cam_sau_quay_phim[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.camera_sau.quay_phim.push({"chat_luong": cam_sau_quay_phim[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.camera_sau.quay_phim.length == 0){
                        cauhinh.thong_so_ky_thuat.camera_sau.quay_phim.push({"chat_luong": ''});
                    }
                    
                    var cam_sau_tinh_nang = $('#cam_sau_tinh_nang').val().split('\n');
                    for(var i = 0; i < cam_sau_tinh_nang.length; i++){
                        if(cam_sau_tinh_nang[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.camera_sau.tinh_nang.push({"name": cam_sau_tinh_nang[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.camera_sau.tinh_nang.length == 0) {
                        cauhinh.thong_so_ky_thuat.camera_sau.tinh_nang.push({"name": ''});
                    }
                    
                    // camera truoc
                    var cam_truoc_tinh_nang = $('#cam_truoc_tinh_nang').val().split('\n');
                    for(var i = 0; i < cam_truoc_tinh_nang.length; i++){
                        if(cam_truoc_tinh_nang[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.camera_truoc.tinh_nang.push({"name": cam_truoc_tinh_nang[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.camera_truoc.tinh_nang.length == 0) {
                        cauhinh.thong_so_ky_thuat.camera_truoc.tinh_nang.push({"name": ''});
                    }
    
                    // kết nối
                    var wifi = $('#wifi').val().split('\n');
                    for(var i = 0; i < wifi.length; i++){
                        if(wifi[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.ket_noi.wifi.push({"name": wifi[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.ket_noi.wifi.length == 0) {
                        cauhinh.thong_so_ky_thuat.ket_noi.wifi.push({"name": ''});
                    }
                    
                    var GPS = $('#GPS').val().split('\n');
                    for(var i = 0; i < GPS.length; i++){
                        if(GPS[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.ket_noi.GPS.push({"name": GPS[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.ket_noi.GPS.length == 0) {
                        cauhinh.thong_so_ky_thuat.ket_noi.GPS.push({"name": ''});
                    }
                    
                    var bluetooth = $('#bluetooth').val().split('\n');
                    for(var i = 0; i < bluetooth.length; i++){
                        if(bluetooth[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.ket_noi.bluetooth.push({"name": bluetooth[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.ket_noi.bluetooth.length == 0) {
                        cauhinh.thong_so_ky_thuat.ket_noi.bluetooth.push({"name": ''});
                    }
                    
                    var ket_noi_khac = $('#ket_noi_khac').val().split('\n');
                    for(var i = 0; i < ket_noi_khac.length; i++){
                        if(ket_noi_khac[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.ket_noi.ket_noi_khac.push({"name": ket_noi_khac[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.ket_noi.ket_noi_khac.length == 0){
                        cauhinh.thong_so_ky_thuat.ket_noi.ket_noi_khac.push({"name": ''});
                    }
    
                    // pin
                    var cong_nghe = $('#cong_nghe').val().split('\n');
                    for(var i = 0; i < cong_nghe.length; i++){
                        if(cong_nghe[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.pin.cong_nghe.push({"name": cong_nghe[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.pin.cong_nghe.length == 0){
                        cauhinh.thong_so_ky_thuat.pin.cong_nghe.push({"name": ''});
                    }
    
                    // tiện ích
                    var bao_mat = $('#bao_mat').val().split('\n');
                    for(var i = 0; i < bao_mat.length; i++){
                        if(bao_mat[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.tien_ich.bao_mat.push({"name": bao_mat[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.tien_ich.bao_mat.length == 0){
                        cauhinh.thong_so_ky_thuat.tien_ich.bao_mat.push({"name": ''});
                    }
                    var tinh_nang_khac = $('#tinh_nang_khac').val().split('\n');
                    for(var i = 0; i < tinh_nang_khac.length; i++){
                        if(tinh_nang_khac[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.tien_ich.tinh_nang_khac.push({"name": tinh_nang_khac[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.tien_ich.tinh_nang_khac.length == 0){
                        cauhinh.thong_so_ky_thuat.tien_ich.tinh_nang_khac.push({"name": ''});
                    }
                    var xem_phim = $('#xem_phim').val().split('\n');
                    for(var i = 0; i < xem_phim.length; i++){
                        if(xem_phim[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.tien_ich.xem_phim.push({"name": xem_phim[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.tien_ich.xem_phim.length == 0){
                        cauhinh.thong_so_ky_thuat.tien_ich.xem_phim.push({"name": ''});
                    }
                    var nghe_nhac = $('#nghe_nhac').val().split('\n');
                    for(var i = 0; i < nghe_nhac.length; i++){
                        if(nghe_nhac[i] == ''){
                            continue;
                        }
                        cauhinh.thong_so_ky_thuat.tien_ich.nghe_nhac.push({"name": nghe_nhac[i]});
                    }
                    if(cauhinh.thong_so_ky_thuat.tien_ich.nghe_nhac.length == 0){
                        cauhinh.thong_so_ky_thuat.tien_ich.nghe_nhac.push({"name": ''});
                    }
    
                    $('.loader').fadeIn();
    
                    var data = {
                        tensp,
                        id_msp,
                        hinhanh,
                        mausac,
                        ram,
                        dungluong,
                        gia,
                        id_km,
                        cauhinhName,
                        cauhinh,
                        trangthai,
                    };
    
                    // thêm mới
                    if($(this).attr('data-type') === 'create'){
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/sanpham',
                            type: 'POST',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                // sản phẩm đã tồn tại
                                if(data === 'exists'){
                                    showAlertTop('Sản phẩm này đã tồn tại');
                                    return;
                                }
    
                                $('#modal').modal('hide');
    
                                // render dòng mới vào view
                                const newRow = renderNewRow(page, data.data)
                                $('#lst_data').prepend(newRow);
    
                                // toast + highlight row
                                toastAndHighlight(CREATE_MESSAGE, data.id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                    // chỉnh sửa
                    else {
                        var id = $(this).attr('data-id');
    
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/sanpham/' + id,
                            type: 'PUT',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                $('#modal').modal('hide');
    
                                // thay thế dòng mới
                                const newRow = renderNewRow(page, data)
                                $(`tr[data-id="${id}"]`).replaceWith(newRow);
    
                                // toast + highlight row
                                toastAndHighlight(EDIT_MESSAGE, id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                }
            });
    
            // xóa
            $('#delete-btn').click(function(){
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/sanpham/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');
    
                        // ẩn nút xóa
                        var restoreBtn = $(`<div data-id="${id}" data-name="${name}" class="undelete-btn"><i class="fas fa-trash-undo"></i></div>`);
                        $(`.delete-btn[data-id="${id}"]`).replaceWith(restoreBtn);
    
                        // cập nhật trạng thái
                        $(`.trangthai[data-id="${id}"]`).text('Ngừng kinh doanh');
    
                        // toast + highlight row
                        toastAndHighlight(DELETE_MESSAGE, id)
                    }
                });
            });
    
            // phục hồi
            $(document).on('click', '.undelete-btn', function(){
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');
                restore(id, name);            
            });
    
            // chọn hình từ mẫu sản phẩm
            $(document).on('click', '.single-image', function(){
                const buttonType = $('#action-btn').attr('data-type')

                if(buttonType !== 'readonly') {
                    removeRequried($('.image-list'));
                    $('.single-image').removeClass('single-image-selected');
                    $(this).addClass('single-image-selected');
                }
            });
    
            // chọn mẫu sản phẩm, thay đổi màu sắc
            $('#sanpham_model').change(function() {
                getModelImage()
            });
    
            // chọn file cấu hình
            $('#sanpham_specifications').change(function(){
                $(this).val() != 'create' ? $('#create-specifications-div').hide('blind') : $('#create-specifications-div').show('blind');
            });
    
            $('#sanpham_color').keyup(function(){
                removeRequried($(this));
            });
            $('#sanpham_price').keyup(function(){
                removeRequried($(this));
            });
    
            // reset modal
            $('#modal').on('hidden.bs.modal', function(){
                $('#sanpham-form').trigger('reset');
                $('input, textarea').attr('readonly', false);
                $('select').attr('disabled', false);
                $('.required').removeClass('required')
                $('.required-text').remove()
                $('#create-specifications-div').show('blind');
                $('#sanpham_specifications').children().remove()
                $('#sanpham_model').children().remove();
                $('.image-list').children().remove();
                // main button
                $('#action-btn').show();
                $('#action-btn').removeAttr('data-type')
                $('#action-btn').removeAttr('data-id')
                $('#action-btn').removeAttr('style')
            });
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    // có lọc
                    if(Object.keys(arrFilterSort.filter).length != 0){
                        return filterSort();
                    } else {
                        var keyword = $(this).val().toLocaleLowerCase();
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/sanpham/ajax-search',
                            type: 'POST',
                            data: {'keyword': keyword},
                            success:function(data){
                                if($('#lst_data').attr('data-loadmore') === 'done') {
                                    $('#lst_data').removeAttr('data-loadmore');
                                }
    
                                const html = renderNewRow(page, data)
                                $('#lst_data').append(html);
    
                                loadMoreFlag = keyword == '' ? false : true;
                                $('#loadmore').hide();
                            },
                            error: function() {
                                $('#loadmore').hide();
                                showAlertTop(errorMessage);
                            }
                        });
                    }
                }, 300);
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });
            $('#search').focus(function() {
                $('.filter-div').hide('blind');
                $('.sort-div').hide('blind');
            })
    
            // show bộ lọc
            $('#filter-sanpham').click(function(){
                $('.filter-div').toggle('blind');
                $('.sort-div').hide('blind');
            });
    
            // show sắp xếp
            $('#sort-sanpham').click(function(){
                $('.filter-div').hide('blind');
                $('.sort-div').toggle('blind');
            });
    
            var arrFilterSort = {
                filter: {},
                sort: $('[name="sort"]:checked').val(),
            };
            // thêm bộ lọc
            $('[name="filter"]').change(function(){
                var obj = $(this).data('object');
    
                if(obj == 'ram'){
                    if($(this).is(':checked')){
                        if(arrFilterSort.filter.ram == null){
                            arrFilterSort.filter.ram = [];
                        }
    
                        arrFilterSort.filter.ram.push($(this).val());
                    } else {
                        var i = arrFilterSort.filter.ram.indexOf($(this).val());
                        arrFilterSort.filter.ram.splice(i, 1);
                        if(arrFilterSort.filter.ram.length == 0){
                            delete arrFilterSort.filter.ram;
                        }
                    }
                } else if(obj == 'capacity'){
                    if($(this).is(':checked')){
                        if(arrFilterSort.filter.capacity == null){
                            arrFilterSort.filter.capacity = [];
                        }
    
                        arrFilterSort.filter.capacity.push($(this).val());
                    } else {
                        var i = arrFilterSort.filter.capacity.indexOf($(this).val());
                        arrFilterSort.filter.capacity.splice(i, 1);
                        if(arrFilterSort.filter.capacity.length == 0){
                            delete arrFilterSort.filter.capacity;
                        }
                    }
                } else {
                    if($(this).is(':checked')){
                        if(arrFilterSort.filter.status == null){
                            arrFilterSort.filter.status = [];
                        }
    
                        arrFilterSort.filter.status.push($(this).val());
                    } else {
                        var i = arrFilterSort.filter.status.indexOf($(this).val());
                        arrFilterSort.filter.status.splice(i, 1);
                        if(arrFilterSort.filter.status.length == 0){
                            delete arrFilterSort.filter.status;
                        }
                    }
                }
                filterSort();
            });
    
            // chọn sắp xếp
            $('[name="sort"]').change(function(){
                var sort = $(this).val();
    
                arrFilterSort.sort = sort;
                filterSort()
            });
    
            // thay đổi chọn hình ảnh
            $('#sanpham_image_from').change(function(){
                // hình từ mẫu sản phẩm
                if($(this).val() === 'model'){
                    removeRequried($('.image-from-model'));
                    removeRequried($('#sanpham_review_image'));
                    $('.image-from-model').show();
                    $('.new-image').hide();
                }
                // chọn hình mới
                else {
                    removeRequried($('.image-from-model'));
                    removeRequried($('#sanpham_review_image'));
                    $('.image-from-model').hide();
                    $('.new-image').show();
                }
            });

            // lấy danh sách mẫu sản phẩm
            function getModelList(skipFalseStatus = false) {
                return new Promise(resolve => {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
                        url: '/admin/sanpham/ajax-get-model-list',
                        type: 'POST',
                        data: {skip: skipFalseStatus},
                        success: function(data) {
                            if(data.length === 0) {
                                showToast('Bạn chưa có mẫu sản phẩm. Vui lòng tạo mới mẫu sản phẩm trước.')
                                return
                            }
    
                            let options = ''
                            $.each(data, (i, val) => {
                                options += `<option value=${val.id}>${val.tenmau}</option>`
                            })
    
                            $('#sanpham_model').append(options)

                            resolve(data[0].id)
                        },
                        error: function() {
                            showToast('Không thể lấy danh sách mẫu sản phẩm. Vui lòng thử lại')
                        }
                    })
                })
            }

            // lấy danh sách file cấu hình
            function getSpecificationsList() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': X_CSRF_TOKEN },
                        url: '/admin/sanpham/ajax-get-specifications-list',
                        type: 'POST',
                        success: function(data) {
                            const specificationsList = $('#sanpham_specifications')
    
                            const newfileOption = '<option value="create" class="main-color-text">Tạo file mới</option>'
                            specificationsList.append(newfileOption)
    
                            // render specifications options
                            let options = ''
                            $.each(data, (i, val) => {
                                options += `<option value="${val}">${val}</option>`
                            })
    
                            specificationsList.append(options)

                            resolve()
                        },
                        error: function() {
                            reject()
                        }
                    })
                })
            }
    
            // lấy hình ảnh mẫu sp
            function getModelImage(selectedImage = null){
                return new Promise((resolve, reject) => {
                    removeRequried($('.image-from-model'));
    
                    const id_msp = $('#sanpham_model option:selected').val()
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'admin/sanpham/ajax-get-model-image',
                        type: 'POST',
                        data: {id_msp},
                        success: function(data){
                            const parent = $('.image-list')
                            parent.children().remove()

                            if(!data.length){
                                parent.css('grid-template-columns', '1fr')
    
                                parent.append('<div class="fw-600 text-center">Mẫu sản phẩm chưa có hình ảnh. Vui lòng tạo mới hình ảnh</div>')
                            } else {
                                parent.css('grid-template-columns', '1fr 1fr')
    
                                let html = '';
                                let imageName = ''
                                $.each(data, (i, val) => {
                                    imageName = val.hinhanh.split('?')[0]
                                    if(imageName === selectedImage) {
                                        html +=
                                            `<div data-name="${val.hinhanh}" class="single-image single-image-selected">
                                                <img src="images/phone/${val.hinhanh}" alt="">
                                            </div>`
                                    } else {
                                        html +=
                                            `<div data-name="${val.hinhanh}" class="single-image">
                                                <img src="images/phone/${val.hinhanh}" alt="">
                                            </div>`
                                    }
                                });
        
                                parent.append(html);
                            }

                            resolve()
                        },
                        error: function() {
                            reject()
                        }
                    });
                })
            }

            function createForm() {
                getModelImage()
                getSpecificationsList()

                // trạng thái = 1
                $('#sanpham_status').hide();
                $('#sanpham_status option[value="1"]').prop('selected', true)
                $('label[for="sanpham_status"]').hide();

                $('#action-btn').attr('data-type', 'create');
                $('#action-btn').text('Thêm');

                $('#modal').modal('show');
            }
    
            async function bindSanPham(id, bool = false, buttonType = 'edit') {
                // lấy danh sách mẫu sp
                const isSkip = buttonType === 'edit' ? false : true

                await getModelList(isSkip)
                    .catch(message => {
                        showToast(message)
                        return
                    })

                // create
                if(buttonType === 'create') {
                    createForm()
                    return
                }
                
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/admin/sanpham/ajax-get-sanpham',
                    type: 'POST',
                    data: {id},
                    cache: false,
                    success: function(data) {
                        var product = data.product;
                        var specifications = data.specifications;

                        // hiển thị modal
                        $('#modal').modal('show');
    
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);
                        $('select').attr('disabled', bool);

                        // gán dữ liệu cho modal
                        $('#sanpham_model').val(product.id_msp);
                        $('#sanpham_color').val(product.mausac);
                        $('#sanpham_ram').val(product.ram);
                        $('#sanpham_capacity').val(product.dungluong);
                        $('#sanpham_price').val(product.gia);
                        $('#sanpham_promotion').val(product.id_km);

                        // ẩn / hiện trạng thái
                        if(product.trangthaimausp || bool){
                            $('#sanpham_status').show();
                            $('label[for="sanpham_status"]').show();
                        } else {
                            $('#sanpham_status').hide();
                            $('label[for="sanpham_status"]').hide();
                        }
                        
                        $(`#sanpham_status option[value="${product.trangthai}"]`).prop('selected', true);

                        // hình ảnh mẫu sp
                        const selectedImage = product.hinhanh
                        getModelImage(selectedImage)
                            .then(() => {
                                // cuộn tới màu sắc đang xem
                                const imgItem = $(`.single-image.single-image-selected`)
                                const parentPadding = 20
                                const position = imgItem.position().top - parentPadding
                                $('.image-list').animate({scrollTop: position})
                            })
                            .catch(() => showToast('Hình ảnh đã bị chỉnh sửa. Vui lòng chọn lại hình ảnh'))
                        

                        // danh sách file cấu hình
                        getSpecificationsList()
                            .then(() => $('#sanpham_specifications').val(product.cauhinh))
                            .catch(() => showToast('Không thể lấy danh sách file cấu hình. Vui lòng làm mới lại trang'))
        
                        // file thông số
                        $('#sanpham_specifications option[value="create"]').hide();
                        // màn hình
                        $('#cong_nghe_mh').val(specifications.thong_so_ky_thuat.man_hinh.cong_nghe_mh);
                        $('#do_phan_giai').val(specifications.thong_so_ky_thuat.man_hinh.do_phan_giai);
                        $('#ty_le_mh').val(specifications.thong_so_ky_thuat.man_hinh.ty_le_mh);
                        $('#kinh_cam_ung').val(specifications.thong_so_ky_thuat.man_hinh.kinh_cam_ung);
    
                        // camera sau
                        $('#cam_sau_do_phan_giai').val(specifications.thong_so_ky_thuat.camera_sau.do_phan_giai);
                        var str = '';
                        if(specifications.thong_so_ky_thuat.camera_sau.quay_phim[0].chat_luong != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.camera_sau.quay_phim.length; i++){
                                var obj = specifications.thong_so_ky_thuat.camera_sau.quay_phim[i].chat_luong;
                                if(i == specifications.thong_so_ky_thuat.camera_sau.quay_phim.length - 1){
                                    str += obj
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#cam_sau_quay_phim').val(str);
                        $('#cam_sau_den_flash').val(specifications.thong_so_ky_thuat.camera_sau.den_flash);
                        str = '';
                        if(specifications.thong_so_ky_thuat.camera_sau.tinh_nang[0].name != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.camera_sau.tinh_nang.length; i++){
                                var obj = specifications.thong_so_ky_thuat.camera_sau.tinh_nang[i].name;
                                if(i == specifications.thong_so_ky_thuat.camera_sau.tinh_nang.length - 1){
                                    str += obj    
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#cam_sau_tinh_nang').val(str);
    
                        // camera trước
                        $('#cam_truoc_do_phan_giai').val(specifications.thong_so_ky_thuat.camera_truoc.do_phan_giai);
                        str = '';
                        if(specifications.thong_so_ky_thuat.camera_truoc.tinh_nang[0].name != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.camera_truoc.tinh_nang.length; i++){
                                var obj = specifications.thong_so_ky_thuat.camera_truoc.tinh_nang[i].name;
                                if(i == specifications.thong_so_ky_thuat.camera_truoc.tinh_nang.length - 1){
                                    str += obj    
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#cam_truoc_tinh_nang').val(str);
    
                        // HDH & CPU
                        $('#HDH').val(specifications.thong_so_ky_thuat.HDH_CPU.HDH);
                        $('#CPU').val(specifications.thong_so_ky_thuat.HDH_CPU.CPU);
                        $('#CPU_speed').val(specifications.thong_so_ky_thuat.HDH_CPU.CPU_speed);
                        $('#GPU').val(specifications.thong_so_ky_thuat.HDH_CPU.GPU);
    
                        // lưu trữ
                        $('#RAM').val(specifications.thong_so_ky_thuat.luu_tru.RAM);
                        $('#bo_nho_trong').val(specifications.thong_so_ky_thuat.luu_tru.bo_nho_trong);
                        $('#bo_nho_con_lai').val(specifications.thong_so_ky_thuat.luu_tru.bo_nho_con_lai);
                        $('#the_nho').val(specifications.thong_so_ky_thuat.luu_tru.the_nho);
    
                        // kết nối
                        $('#mang_mobile').val(specifications.thong_so_ky_thuat.ket_noi.mang_mobile);
                        $('#SIM').val(specifications.thong_so_ky_thuat.ket_noi.SIM);
                        str = '';
                        if(specifications.thong_so_ky_thuat.ket_noi.wifi[0].name != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.ket_noi.wifi.length; i++){
                                var obj = specifications.thong_so_ky_thuat.ket_noi.wifi[i].name;
                                if(i == specifications.thong_so_ky_thuat.ket_noi.wifi.length - 1){
                                    str += obj    
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#wifi').val(str);
                        str = '';
                        if(specifications.thong_so_ky_thuat.ket_noi.GPS[0].name != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.ket_noi.GPS.length; i++){
                                var obj = specifications.thong_so_ky_thuat.ket_noi.GPS[i].name;
                                if(i == specifications.thong_so_ky_thuat.ket_noi.GPS.length - 1){
                                    str += obj    
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#GPS').val(str);
                        str = '';
                        if(specifications.thong_so_ky_thuat.ket_noi.bluetooth[0].name != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.ket_noi.bluetooth.length; i++){
                                var obj = specifications.thong_so_ky_thuat.ket_noi.bluetooth[i].name;
                                if(i == specifications.thong_so_ky_thuat.ket_noi.bluetooth.length - 1){
                                    str += obj    
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#bluetooth').val(str);
                        $('#cong_sac').val(specifications.thong_so_ky_thuat.ket_noi.cong_sac);
                        $('#jack_tai_nghe').val(specifications.thong_so_ky_thuat.ket_noi.jack_tai_nghe);
                        str = '';
                        if(specifications.thong_so_ky_thuat.ket_noi.ket_noi_khac[0].name != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.ket_noi.ket_noi_khac.length; i++){
                                var obj = specifications.thong_so_ky_thuat.ket_noi.ket_noi_khac[i].name;
                                if(i == specifications.thong_so_ky_thuat.ket_noi.ket_noi_khac.length - 1){
                                    str += obj    
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#ket_noi_khac').val(str);
    
                        // thiết kế trọng lượng
                        $('#thiet_ke').val(specifications.thong_so_ky_thuat.thiet_ke_trong_luong.thiet_ke);
                        $('#chat_lieu').val(specifications.thong_so_ky_thuat.thiet_ke_trong_luong.chat_lieu);
                        $('#kich_thuoc').val(specifications.thong_so_ky_thuat.thiet_ke_trong_luong.kich_thuoc);
                        $('#khoi_luong').val(specifications.thong_so_ky_thuat.thiet_ke_trong_luong.khoi_luong);
    
                        // pin
                        $('#loai').val(specifications.thong_so_ky_thuat.pin.loai);
                        $('#dung_luong').val(specifications.thong_so_ky_thuat.pin.dung_luong);
                        str = '';
                        if(specifications.thong_so_ky_thuat.pin.cong_nghe[0].name != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.pin.cong_nghe.length; i++){
                                var obj = specifications.thong_so_ky_thuat.pin.cong_nghe[i].name;
                                if(i == specifications.thong_so_ky_thuat.pin.cong_nghe.length - 1){
                                    str += obj    
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#cong_nghe').val(str);
    
                        // tiện ích
                        str = '';
                        if(specifications.thong_so_ky_thuat.tien_ich.bao_mat[0].name != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.tien_ich.bao_mat.length; i++){
                                var obj = specifications.thong_so_ky_thuat.tien_ich.bao_mat[i].name;
                                if(i == specifications.thong_so_ky_thuat.tien_ich.bao_mat.length - 1){
                                    str += obj    
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#bao_mat').val(str);
                        str = '';
                        if(specifications.thong_so_ky_thuat.tien_ich.tinh_nang_khac[0].name != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.tien_ich.tinh_nang_khac.length; i++){
                                var obj = specifications.thong_so_ky_thuat.tien_ich.tinh_nang_khac[i].name;
                                if(i == specifications.thong_so_ky_thuat.tien_ich.tinh_nang_khac.length - 1){
                                    str += obj    
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#tinh_nang_khac').val(str);
                        $('#ghi_am').val(specifications.thong_so_ky_thuat.tien_ich.ghi_am);
                        str = '';
                        if(specifications.thong_so_ky_thuat.tien_ich.xem_phim[0].name != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.tien_ich.xem_phim.length; i++){
                                var obj = specifications.thong_so_ky_thuat.tien_ich.xem_phim[i].name;
                                if(i == specifications.thong_so_ky_thuat.tien_ich.xem_phim.length - 1){
                                    str += obj    
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#xem_phim').val(str);
                        str = '';
                        if(specifications.thong_so_ky_thuat.tien_ich.nghe_nhac[0].name != null){
                            for(var i = 0; i < specifications.thong_so_ky_thuat.tien_ich.nghe_nhac.length; i++){
                                var obj = specifications.thong_so_ky_thuat.tien_ich.nghe_nhac[i].name;
                                if(i == specifications.thong_so_ky_thuat.tien_ich.nghe_nhac.length - 1){
                                    str += obj    
                                } else {
                                    str += obj + '\n';
                                }
                            }
                        }
                        $('#nghe_nhac').val(str);
    
                        // thông tin khác
                        var thoidiemramat;
                        if(specifications.thong_tin_khac.thoi_diem_ra_mat != null){
                            var temp = specifications.thong_tin_khac.thoi_diem_ra_mat.split('/');
                            thoidiemramat = `${temp[1]}-${temp[0]}`;
                        } else {
                            thoidiemramat = '';
                        }
                        
                        $('#thoi_diem_ra_mat').val(thoidiemramat);

                        // ẩn hiện nút action
                        if(bool) {
                            $('#action-btn').attr('data-type', 'readonly')
                            $('#action-btn').hide()
                        } else {
                            $('#action-btn').attr('data-type', 'edit');
                            $('#action-btn').text('Cập nhật');
                            $('#action-btn').attr('data-id', id);
                            $('#action-btn').show()
                        }
                    },
                    error: function() {
                        showAlertTop(errorMessage)
                    }
                });
            }
    
            // bẫy lỗi màu sắc
            function validateColor(color) {
                if(color.hasClass('required')){
                    $('.modal-body').animate({scrollTop: color.position().top});
                    return false;
                }
    
                const value = color.val().trim()
    
                // chưa nhập
                if(value == ''){
                    color.addClass('required');
                    color.after('<span class="required-text">Nhập màu</span>');
                    $('.modal-body').animate({scrollTop: color.position().top});
                    return false;
                }
    
                // không hợp lệ
                if(!isNaN(value)){
                    color.addClass('required');
                    color.after('<span class="required-text">Không hợp lệ</span>');
                    $('.modal-body').animate({scrollTop: color.position().top});
                    return false;
                }
    
                return true;
            }
    
            // bẫy lỗi giá
            function validatePrice(price) {
                if(price.hasClass('required')){
                    $('.modal-body').animate({scrollTop: price.position().top});
                    return false;
                }
    
                // chưa nhập
                if(price.val() == ''){
                    price.addClass('required');
                    price.after('<span class="required-text">Nhập giá</span>');
                    $('.modal-body').animate({scrollTop: price.position().top});
                    return false;
                }
    
                // 1.000.000 <= giá <= 100.000.000
                if(price.val() < 1000000 || price.val() > 1000000000){
                    price.addClass('required');
                    price.after('<span class="required-text">Giá từ 1.000.000<sup>đ</sup> - 100.000.000<sup>đ</sup></span>');
                    $('.modal-body').animate({scrollTop: price.position().top});
                    return false;
                }
    
                return true;
            }
    
            // bẫy lỗi hình ảnh
            function validateProductImage() {
                const imageList = $('.image-list')

                if(imageList.hasClass('required')) {
                    return
                }

                const selectedItem = $('.single-image-selected')

                if(!selectedItem.length) {
                    imageList.addClass('required')
                    imageList.after('<span class="required-text">Vui lòng chọn hình ảnh</span>');
                    $('.modal-body').animate({scrollTop: imageList.position().top});
                    return false;
                }
                
                return true
            }
    
            function restore(id, name) {
                $('.loader').fadeIn();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/sanpham/ajax-restore',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        $('.loader').fadeOut();
    
                        if(data == 'false'){
                            showAlertTop('Không thể khôi phục do mẫu sản phẩm đã xóa trước đó.');
                            return;
                        }
                        // hiện nút xóa
                        var deleteBtn = $(`<div data-id="${id}" data-name="${name}" class="delete-btn"><i class="fas fa-trash"></i></div>`);
                        $(`.undelete-btn[data-id="${id}"]`).replaceWith(deleteBtn);
    
                        // cập nhật trạng thái
                        $(`.trangthai[data-id="${id}"]`).text('Kinh doanh');
    
                        // toast + highlight row
                        toastAndHighlight(EDIT_MESSAGE, id)
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            }
    
            // danh sách kết quả lọc & sắp xếp
            function filterSort() {
                $('#lst_data').children().remove();
                $('#loadmore').show();
                const keyword = $('#search').val().toLocaleLowerCase()
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/sanpham/ajax-filtersort',
                    type: 'POST',
                    data: {'arrFilterSort': arrFilterSort, 'search': keyword},
                    success: function(data){
                        $('#lst_data').children().remove();
    
                        const html = renderNewRow(page, data)
                        $('#lst_data').append(html);
     
                        if(Object.keys(arrFilterSort.filter).length === 0){
                            if($('#lst_data').attr('data-loadmore') === 'done') {
                                $('#lst_data').removeAttr('data-loadmore')
                            }
    
                            $('.filter-badge').hide();
                            loadMoreFlag = keyword == '' ? false : true
                        } else {
                            loadMoreFlag = true
                            $('.filter-badge').text(Object.keys(arrFilterSort.filter).length);
                            $('.filter-badge').show();
                        }
                        $('#loadmore').hide();
                    },
                    error: function() {
                        $('#loadmore').hide();
                        showAlertTop(errorMessage)
                    }
                });
            }

            break
        }
        /*=======================================================================================================================
                                                            Nhà cung cấp
        =======================================================================================================================*/
        case 'nhacungcap': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            // modal tạo mới
            $('.create-btn').off('click').click(function(){
                // gán dữ liệu cho modal
                $('#modal-title').text('Tạo mới nhà cung cấp');
    
                // trạng thái = 1
                $('#ncc_status').hide();
                $('label[for="ncc_status"]').hide();
    
                // thiết lập nút gửi là thêm mới
                $('#action-ncc-btn').attr('data-type', 'create');
                $('#action-ncc-btn').text('Thêm');
    
                $('#product-color-carousel').parent().hide();
    
                // hiển thị modal
                $('#ncc-modal').modal('show');
            });
    
            // modal xem chi tiết
            $(document).on('click', '.info-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chi tiết nhà cung cấp');
                bindNCC(id, true);
            });
    
            // modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chỉnh sửa nhà cung cấp');
                bindNCC(id);
            });
    
            // modal xóa
            $(document).on('click', '.delete-btn', function(){
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');
    
                // gán dữ liệu cho modal xóa
                $('#delete-content').html('Xóa nhà cung cấp <b>'+name+'?</b>');
                $('#delete-btn').attr('data-id', id);
                $('#delete-btn').attr('data-name', name);
                $('#delete-modal').modal('show');
            });
    
            // thêm|sửa
            $('#action-ncc-btn').click(function(){
                // bẫy lỗi
                var valiName = validateName($('#ncc_name'));
                var valiAddress = validateAddress($('#ncc_address'));
                var valitel = validatePhoneNumber($('#ncc_tel'));
                var valiEmail = validateEmail($('#ncc_email'));
                var valiImage = validateSupplierImage($('#ncc_image_inp'))
    
                // bẫy lỗi xong kiểm tra loại
                if(valiName & valiAddress & valitel & valiEmail && valiImage){
                    $('.loader').fadeIn();
    
                    var data = {
                        'tenncc': $('#ncc_name').val(),
                        'anhdaidien': $('#ncc_image_base64').val(),
                        'diachi': $('#ncc_address').val(),
                        'sdt': $('#ncc_tel').val(),
                        'email': $('#ncc_email').val(),
                        'trangthai': $('#ncc_status').val(),
                    };
    
                    // thêm mới
                    if($(this).attr('data-type') == 'create'){
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/nhacungcap',
                            type: 'POST',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
    
                                // đã tồn tại
                                if(data == 'exists'){
                                    $('.loader').fadeOut();
                                    $('#ncc_name').addClass('required');
                                    $('#ncc_name').after('<span class="required-text">Nhà cung cấp này đã tồn tại</span>');
                                    return;
                                }
    
                                $('#ncc-modal').modal('hide');
    
                                // render vào view
                                const newRow = renderNewRow(page, data.data);
                                $('#lst_data').prepend(newRow);
    
                                // toast + highlight row
                                toastAndHighlight(CREATE_MESSAGE, data.id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage);
                            }
                        });
                    }
                    // chỉnh sửa
                    else {
                        var id = $(this).attr('data-id');
    
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/nhacungcap/' + id,
                            type: 'PUT',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                $('#ncc-modal').modal('hide');
    
                                // thay thế
                                const newRow = renderNewRow(page, data)
                                $(`tr[data-id="${id}"]`).replaceWith(newRow); 
                                
                                // toast + highlight row
                                toastAndHighlight(EDIT_MESSAGE, id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage);
                            }
                        });
                    }
                }
            });
    
            // xóa
            $('#delete-btn').click(function(){
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');
    
                $('.loader').fadeIn()
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/nhacungcap/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');
    
                        // thay nút
                        var restoreBtn = $(`<div data-id="${id}" data-name="${name}" class="undelete-btn"><i class="fas fa-trash-undo"></i></div>`)
                        $(`.delete-btn[data-id="${id}"]`).replaceWith(restoreBtn);
    
                        // cập nhật trạng thái
                        $(`.trangthai[data-id="${id}"]`).text('Ngừng kinh doanh');
    
                        // toast + highlight row
                        toastAndHighlight(DELETE_MESSAGE, id)
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            });
    
            // khôi phục
            $(document).on('click', '.undelete-btn', function(){
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');
    
                restore(id, name);
            });
    
            // show dialog chọn hình ảnh
            $('#ncc_choose_image').click(function(){
                $('#ncc_image_inp').click();
            });
    
            // chọn hình ảnh
            $('#ncc_image_inp').change(function(){
                // hủy chọn hình
                if($(this).val() == ''){
                    return;
                }
    
                removeRequried($('#ncc_review_image'));
    
                // kiểm tra file hình
                var fileName = this.files[0].name.split('.');
                var extend = fileName[fileName.length - 1];
    
                if(extend == 'jpg' || extend == 'jpeg' || extend == 'png'){
                    // Byte => KB => MB
                    const size = (this.files[0].size / BYTE) / BYTE
                    if(size > MAX_SIZE_IMAGE) {
                        showAlertTop(maxSizeImageMessage)
                        return
                    }
                    // xem trước hình ảnh
                    $('#ncc_review_image').attr('src', URL.createObjectURL(this.files[0]))
    
                    // image -> base64
                    const urlIMG = URL.createObjectURL(this.files[0])
                    getBase64FromUrl(urlIMG)
                        .then(dataUrl => $('#ncc_image_base64').val(dataUrl));
                }
                // không phải hình ảnh
                else{
                    $('#ncc_review_image').attr('src', 'images/320x320.png');
                    $(this).val('');
                    showAlertTop('Bạn chỉ có thể upload hình ảnh');
                }
            });
    
            $('#ncc_name').keyup(function(){
                removeRequried($(this));
            });
            $('#ncc_address').keyup(function(){
                removeRequried($(this));
            });
            $('#ncc_tel').keyup(function(){
                valiPhonenumberTyping($(this));
            });
            $('#ncc_email').keyup(function(){
                removeRequried($(this));
            })
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    var keyword = $(this).val().toLocaleLowerCase();
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/nhacungcap/ajax-search',
                            type: 'POST',
                            data: {'keyword': keyword},
                            success:function(data){
                                const lst_data = $('#lst_data')
    
                                if(lst_data.attr('data-loadmore') === 'done') {
                                    lst_data.removeAttr('data-loadmore')
                                }
    
                                lst_data.children().remove();
    
                                const html = renderNewRow(page, data)
                                lst_data.append(html);
    
                                loadMoreFlag = keyword == '' ? false : true;
                                $('#loadmore').hide();
                            },
                            error: function() {
                                $('#loadmore').hide();
                                showAlertTop(errorMessage)
                            }
                        });
                },300);
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });
    
            // reset modal
            $('#ncc-modal').on('hidden.bs.modal', function(){
                $('#ncc-form').trigger('reset');
                $('input, textarea').attr('readonly', false);
                $('select').attr('disabled', false);
                $('.required').removeClass('required')
                $('.required-text').remove()
                $('#ncc_choose_image').show();
                $('#ncc_review_image').attr('src', 'images/320x320.png');
                $('#ncc_image_base64').val('');
                $('#action-ncc-btn').show();
            });
    
            function bindNCC(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/admin/nhacungcap/ajax-get-ncc',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);
                        $('select').attr('disabled', bool);
    
                        // gán dữ liệu cho modal
                        $('#ncc_review_image').attr('src', 'images/logo/' + data.anhdaidien);
                        $('#ncc_name').val(data.tenncc);
                        $('#ncc_address').val(data.diachi);
                        $('#ncc_tel').val(data.sdt);
                        $('#ncc_email').val(data.email);
                        $('#ncc_status option[value="'+data.trangthai+'"]').prop('selected', true);
                        $('label[for="ncc_status"]').show();
                        $('#ncc_status').show();
    
                        if(bool) {
                            $('#ncc_choose_image').hide()
                            $('#action-ncc-btn').hide()
                        } else {
                            $('#ncc_choose_image').show()
                            $('#action-ncc-btn').show()
                        }
    
                        // thiết lập nút gửi là cập nhật
                        $('#action-ncc-btn').attr('data-type', 'edit');
                        $('#action-ncc-btn').text('Cập nhật');
                        $('#action-ncc-btn').attr('data-id', id);
    
                        // hiển thị modal
                        $('#ncc-modal').modal('show');
                    },
                    error: function () {
                        $('.loader').fadeOut()
                        showAlertTop(errorMessage)
                    }
                });
            }
    
            // bẫy lỗi hình ảnh
            function validateSupplierImage(image) {
                if($('#ncc_review_image').hasClass('required')){
                    $('.modal-body').animate({scrollTop: image.position().top});
                    return false;
                }
    
                // chưa chọn
                if(image.val() == '' && $('#ncc_review_image').attr('src') == 'images/320x320.png'){
                    $('#ncc_review_image').addClass('required');
                    $('#ncc_review_image').after('<span class="required-text">Vui lòng chọn hình ảnh</span>');
                    $('.modal-body').animate({scrollTop: image.position().top});
                    return false;
                }
    
                return true;
            }
    
            function restore(id, name) {
                $('.loader').fadeIn();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/nhacungcap/ajax-restore',
                    type: 'POST',
                    data: {'id': id},
                    success:function(){
                        $('.loader').fadeOut();
    
                        // thay nút
                        var deleteBtn = $(`<div data-id="${id}" data-name="${name}" class="delete-btn"><i class="fas fa-trash"></i></div>'`)
                        $(`.undelete-btn[data-id="${id}"]`).replaceWith(deleteBtn);
    
                        // cập nhật trạng thái
                        $(`.trangthai[data-id="${id}"]`).text('Hoạt động');
    
                        // toast + highlight row
                        toastAndHighlight(EDIT_MESSAGE, id)
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            }

            break
        }
        /*=======================================================================================================================
                                                            Slideshow msp
        =======================================================================================================================*/
        case 'slideshow-msp': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            var idx = 1;
            var arrayDelete = [];
    
            // modal tạo mới
            $('.create-btn').off('click').click(function(){
                // lấy danh sách mẫu sp chưa có hình ảnh
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/slideshow-msp/ajax-get-model-havenot-slideshow',
                    type: 'POST',
                    data: {'data': ''},
                    success:function(data){
                        if(data.length == 0){
                            showToast('Tất cả mẫu sản phẩm đã có hình ảnh', TOAST_SUCCESS_TYPE);
                        } else {
                            // gán dữ liệu cho modal
                            $('#modal-title').text('Tạo mới slideshow mẫu sản phẩm');
                            $('#model').children().remove();
    
                            let options = ''
                            $.each(data, (i, val) => {
                                options +=
                                    `<option value="${val.id}">${val.tenmau}</option>`
                            }).join('')
                            
                            $('#model').append(options)
    
                            // thiết lập nút gửi là thêm mới
                            $('#action-btn').attr('data-type', 'create');
                            $('#action-btn').text('Thêm');
    
                            // hiển thị modal
                            $('#modal').modal('show');
                        }
                    },
                    error: function() {
                        showAlertTop(errorMessage)
                    }
                });
            });
    
            // modal chi tiết
            $(document).on('click', '.info-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chi tiết slideshow mẫu sản phẩm');
                bindSlideshowMSP(id, true);
            });
    
            // modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chỉnh sửa slideshow mẫu sản phẩm');
                bindSlideshowMSP(id);
            });
    
            // modal xóa
            $(document).on('click', '.delete-btn', function(){
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');
    
                // gán dữ liệu cho modal xóa
                $('#delete-content').html('Xóa slideshow <b>'+name+'?</b>');
                $('#delete-btn').attr('data-id', id);
                $('#delete-btn').attr('data-name', name);
                $('#delete-modal').modal('show');
            });
    
            // thêm|sửa
            $('#action-btn').click(function(){
                addOrUpdate($(this))
            });
    
            // xóa
            $('#delete-btn').click(function(){
                var id = $(this).attr('data-id');
    
                $('.loader').fadeIn();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/slideshow-msp/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');
    
                        // ẩn nút xóa
                        $(`.delete-btn[data-id="${id}"]`).remove();
    
                        // cập nhật số lượng hình
                        $(`.qty-image[data-id="${id}"]`).text('0 Hình');
    
                        // toast + highlight row
                        toastAndHighlight(DELETE_MESSAGE, id)
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            });
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    var keyword = $(this).val().toLocaleLowerCase();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'admin/slideshow-msp/ajax-search',
                        type: 'POST',
                        data: {'keyword': keyword},
                        success: function(data){
                            const lst_data = $('#lst_data')
    
                            if(lst_data.attr('data-loadmore') === 'done') {
                                lst_data.removeAttr('data-loadmore')
                            }
    
                            const html = renderNewRow(page, data)
                            lst_data.append(html);
    
                            loadMoreFlag = keyword == '' ? false : true;
                            $('#loadmore').hide();
                        },
                        error: function() {
                            $('#loadmore').hide();
                            showAlertTop(errorMessage)
                        }
                    });
                }, 300);
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });
    
            // show dialog chọn hình ảnh
            $('#choose_image').click(function(){
                $('#image_inp').click();
            });
    
            // chọn hình ảnh
            $('#image_inp').change(function(){
                // hủy chọn hình
                if($(this).val() == ''){
                    return;
                }
    
                removeRequried($('.image-preview-div'));
                $('#qty-image').show();
    
                // số hình trong input file
                var length = this.files.length;
    
                // tổng số hình hiện tại
                var qty = $('.image-preview-div > .row').children().length;
                let imageElement = ''
                let size = 0
    
                for(var i = 0; i < length; i++){
                    if(qty >= 30){
                        showAlertTop('Hình ảnh upload tối đa là 30');
                        break;
                    }
    
                    // kiểm tra file hình
                    var fileName = this.files[i].name.split('.');
                    var extend = fileName[fileName.length - 1];
    
                    // kiểm tra có phải là hình ảnh không
                    if(extend == 'jpg' || extend == 'jpeg' || extend == 'png'){
                        // tối đa 5 MB
                        size = (this.files[i].size / BYTE) / BYTE
                        if(size > 5) {
                            showAlertTop(maxSizeImageMessage)
                            break
                        }
                        // tạo hình
                        imageElement += 
                            `<div id="image-${idx}" data-id="${idx}" class="col-lg-4 col-6">
                                <div class="image-preview">
                                    <div class="overlay-image-preview"></div>
                                    <div data-id="${idx}" class="delete-image-preview"><i class="far fa-times-circle fz-40"></i></div>
                                    <img data-id="${idx}" class="image_preview_img" src="${URL.createObjectURL(this.files[i])}" alt="">
                                </div>
                            </div>`
                    }
                    // không phải hình ảnh
                    else{
                        showAlertTop('Bạn chỉ có thể upload hình ảnh');
                        break;
                    }
                    idx++;
                    qty++;
                }
    
                $('.image-preview-div > .row').append(imageElement)
                $('#qty-image').text(`(${qty})`);
            });
    
            // xóa hình
            $(document).on('click', '.delete-image-preview', function(){
                let id = $(this).data('id');
                let image  = $(`#image-${id}`)
                let imageName = image.attr('data-name')

                if(imageName !== undefined){
                    arrayDelete.push(imageName.split('?')[0]);
                }
    
                image.remove();
    
                var qty = $('.image-preview-div > .row').children().length;
    
                if(qty === 0){
                    $('#qty-image').hide();
                    $('#image_inp').val('');
                } else {
                    $('#qty-image').text(`(${qty})`);
                }
            });
    
            $('#model').change(function(){
                const action = $('#action-btn').attr('data-type')
                
                if(action === 'edit') {
                    const id = $(this).val();
                    bindSlideshowMSP(id);
                }
            });
    
            // reset modal
            $('#modal').on('hidden.bs.modal', function(){
                $('#form').trigger('reset');
                $('select').attr('disabled', false);
                $('#model').children().remove();
                $('.image-preview-div > .row').children().remove();
                $('.required').removeClass('required')
                $('.required-text').remove()
                $('#choose_image').show();
                $('#action-btn').show();
                idx = 1;
                arrayDelete = [];
                $('#qty-image').text('');
            });
    
            function bindSlideshowMSP(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/slideshow-msp/ajax-get-slideshow-msp',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);
                        $('select').attr('disabled', bool);
    
                        var lst_slide = data.lst_slide;
                        var lst_model = data.lst_model;
    
                        const urlSlide = 'images/phone/slideshow/'
    
                        // gán hình ảnh + gán mẫu sp
                        Promise.all([renderImage(lst_slide, urlSlide), assignModel(lst_model, id)])
                            .catch(() => showAlertTop(errorMessage))                
    
                        if(bool) {
                            $('.overlay-image-preview').remove();
                            $('.delete-image-preview').remove();
                            $('#action-btn').hide();
                            $('#choose_image').hide();
                        } else {
                            $('#action-btn').show();
                            $('#choose_image').show();
                        }
    
                        $('#qty-image').text(`(${lst_slide.length})`);
    
                        // thiết lập nút gửi là cập nhật
                        $('#action-btn').attr('data-type', 'edit');
                        $('#action-btn').text('Cập nhật');
                        $('#action-btn').attr('data-id', id);
    
                        // hiển thị modal
                        $('#modal').modal('show');
                    },
                    error: function() { showAlertTop(errorMessage) }
                });
            }
    
            // bẫy lỗi hình ảnh
            function validateSlideshowImage() {
                var image = $('.image-preview-div');
                if(image.hasClass('required')){
                    $('.modal-body').animate({scrollTop: image.position().top});
                    return false;
                }
    
                // chưa chọn
                if($('.image-preview-div > .row').children().length == 0){
                    image.addClass('required');
                    image.after('<span class="required-text">Vui lòng chọn hình ảnh</span>');
                    $('.modal-body').animate({scrollTop: image.position().top});
                    return false;
                }
    
                return true;
            }
    
            async function addOrUpdate(button) {
                // bẫy lỗi
                var valiImage = validateSlideshowImage()
    
                // bẫy lỗi xong kiểm tra loại
                if(valiImage){
                    $('.loader').fadeIn();

                    const id_msp = $('#model').val()
                    // tổng dung lượng file upload: MB
                    let totalSize = 0
                    
                    const type = button.attr('data-type')
                    let arrayBase64 = []
    
                    await pushBase64ToArray(type)
                            .then(base64 => {
                                arrayBase64 = base64
                                // byte => KB => MB
                                totalSize = (new Blob([base64.join('')]).size / BYTE) / BYTE + 1
                            })

                    // upload files quá kích thước cho phép
                    if(totalSize > POST_SIZE_MAX) {
                        // upload từng file một
                        if(type === 'create') {
                            const length = arrayBase64.length - 1
                            $.each(arrayBase64, (i, val) => {
                                if(i === length) {
                                    addSingleFile(id_msp, val, true)
                                        .then(data => {
                                            $('.loader').fadeOut();
                                            $('#modal').modal('hide');

                                            // render vào view
                                            $('#lst_data').children().remove();
                                            const html = renderNewRow(page, data.data)
                                            $('#lst_data').append(html);

                                            // toast + highlight row
                                            toastAndHighlight(CREATE_MESSAGE, data.id)
                                            return
                                        })
                                        .catch(() => showToast(errorMessage))
                                } else {
                                    addSingleFile(id_msp, val)
                                        .catch(() => showToast(errorMessage))
                                }
                            })
                        } else {
                            const length = arrayBase64.length - 1
                            $.each(arrayBase64, (i, val) => {
                                if(i === length) {
                                    updateSingleFile(id_msp, val, arrayDelete, true)
                                        .then(data => {
                                            $('.loader').fadeOut();
                                            $('#modal').modal('hide');

                                            // thay thế
                                            const newRow = renderNewRow(page, data)
                                            $(`tr[data-id="${id_msp}"]`).replaceWith(newRow);

                                            // toast + highlight row
                                            toastAndHighlight(EDIT_MESSAGE, id_msp)
                                            return
                                        })
                                        .catch(() => showToast(errorMessage))
                                } else {
                                    updateSingleFile(id_msp, val)
                                        .catch(() => showToast(errorMessage))
                                }
                            })
                        }
                    } else {
                        // thêm mới
                        if(type === 'create'){
                            var data = {
                                id_msp,
                                'lst_base64_slideshow': arrayBase64,
                            };
        
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                                },
                                url: 'admin/slideshow-msp',
                                type: 'POST',
                                data: data,
                                success:function(data){
                                    $('.loader').fadeOut();
                                    $('#modal').modal('hide');
        
                                    // render vào view
                                    $('#lst_data').children().remove();
                                    const html = renderNewRow(page, data.data)
                                    $('#lst_data').append(html);
        
                                    // toast + highlight row
                                    toastAndHighlight(CREATE_MESSAGE, data.id)
                                },
                                error: function() {
                                    $('.loader').fadeOut();
                                    showAlertTop(errorMessage)
                                }
                            });
                        }
                        // chỉnh sửa
                        else {
                            var id = button.attr('data-id');
        
                            var data = {
                                'lst_base64_slideshow': arrayBase64,
                                'lst_delete': arrayDelete,
                            };
                            
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                                },
                                url: 'admin/slideshow-msp/' + id,
                                type: 'PUT',
                                data: data,
                                success:function(data){
                                    $('.loader').fadeOut();
                                    $('#modal').modal('hide');
        
                                    // thay thế
                                    const newRow = renderNewRow(page, data)
                                    $(`tr[data-id="${id}"]`).replaceWith(newRow);
        
                                    // toast + highlight row
                                    toastAndHighlight(EDIT_MESSAGE, id)
                                },
                                error: function() {
                                    $('.loader').fadeOut();
                                    showAlertTop(errorMessage)
                                }
                            });
                        }
                    }
    
                }
            }

            function addSingleFile(id_msp, base64String, lastItem = false) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
                        url: 'admin/slideshow-msp/ajax-add-single-file',
                        type: 'POST',
                        data: {
                            id_msp,
                            base64String,
                            lastItem
                        },
                        success: function (data) {
                            resolve(data)
                        },
                        error: function (error) {
                            console.log(error)
                            reject()
                        }
                    })
                })
            }

            function updateSingleFile(id_msp, base64String, arrayDelete = [], lastItem = false) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
                        url: 'admin/slideshow-msp/ajax-update-single-file',
                        type: 'POST',
                        data: {
                            id_msp,
                            base64String,
                            arrayDelete,
                            lastItem
                        },
                        success: function(data) {
                            resolve(data)
                        },
                        error: function(error) {
                            console.error(error)
                            reject()
                        }
                    })
                })
            }

            break
        }
        /*=======================================================================================================================
                                                            Hình ảnh
        =======================================================================================================================*/
        case 'hinhanh': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            var idx = 1;
            var arrayDelete = [];
    
            // modal tạo mới
            $('.create-btn').off('click').click(function(){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/hinhanh/ajax-get-model-havenot-image',
                    type: 'POST',
                    data: {'data': ''},
                    success:function(data){
                        if(data.length == 0){
                            showToast('Tất cả mẫu sản phẩm đã có hình ảnh', TOAST_SUCCESS_TYPE);
                        } else {
                            // gán dữ liệu cho modal
                            $('#modal-title').text('Tạo mới hình ảnh mẫu sản phẩm');
                            $('#model').children().remove();
    
                            let options = ''
                            $.each(data, (i, val) => {
                                options +=
                                    `<option value="${val.id}">${val.tenmau}</option>`
                            })
    
                            $('#model').append(options)
    
                            // thiết lập nút gửi là thêm mới
                            $('#action-btn').attr('data-type', 'create');
                            $('#action-btn').text('Thêm');
    
                            // hiển thị modal
                            $('#modal').modal('show');
                        }
                    },
                    error: function() {
                        showAlertTop(errorMessage)
                    }
                });
            });
    
            // modal chi tiết
            $(document).on('click', '.info-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chi tiết hình ảnh mẫu sản phẩm');
                bindHinhAnh(id, true);
            });
    
            // modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chỉnh sửa hình ảnh mẫu sản phẩm');
                bindHinhAnh(id);
            });
    
            // modal xóa
            $(document).on('click', '.delete-btn', function(){
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');
    
                // gán dữ liệu cho modal xóa
                $('#delete-content').html('Xóa hình ảnh <b>'+name+'?</b>');
                $('#delete-btn').attr('data-id', id);
                $('#delete-btn').attr('data-name', name);
                $('#delete-modal').modal('show');
            });
    
            // thêm|sửa
            $('#action-btn').click(function(){
                addOrUpdate($(this))
            });
    
            // xóa
            $('#delete-btn').click(function(){
                var id = $(this).attr('data-id');
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/hinhanh/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');
    
                        // ẩn nút xóa
                        $(`.delete-btn[data-id="${id}"]`).remove();
    
                        // cập nhật số lượng hình
                        $(`.qty-image[data-id="${id}"]`).text('0 Hình');
    
                        // toast + highlight row
                        toastAndHighlight(DELETE_MESSAGE, id)
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            });
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    var keyword = $(this).val().toLocaleLowerCase();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"').attr('content')
                        },
                        url: 'admin/hinhanh/ajax-search',
                        type: 'POST',
                        data: {'keyword': keyword},
                        success: function(data){
                            const lst_data = $('#lst_data')
    
                            const html = renderNewRow(page, data)
                            lst_data.append(html);
    
                            if(lst_data.attr('data-loadmore') === 'done') {
                                lst_data.removeAttr('data-loadmore')
                            }
    
                            loadMoreFlag = keyword == '' ? false : true;
                            $('#loadmore').hide();
                        },
                        error: function() {
                            $('#loadmore').hide();
                            showAlertTop(errorMessage)
                        }
                    });
                }, 300);
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });
    
            // show dialog chọn hình ảnh
            $('#choose_image').click(function(){
                $('#image_inp').click();
            });
    
            // chọn hình ảnh
            $('#image_inp').change(function(){
                // hủy chọn hình
                if($(this).val() == ''){
                    return;
                }
    
                removeRequried($('.image-preview-div'));
                $('#qty-image').show();
    
                // số hình trong input file
                var length = this.files.length;
    
                // tổng số hình hiện tại
                var qty = $('.image-preview-div > .row').children().length;
                let imageElement = ''
                let size = 0
    
                for(var i = 0; i < length; i++) {
                    if(qty >= 20){
                        showAlertTop('Hình ảnh upload tối đa là 20');
                        break;
                    }
    
                    // kiểm tra file hình
                    const fileName = this.files[i].name
                    const array = fileName.split('.');
                    const extend = array[array.length - 1];
    
                    // kiểm tra có phải là hình ảnh không
                    if(extend === 'jpg' || extend === 'jpeg' || extend === 'png') {
                        // tối đa 5 MB
                        size = (this.files[i].size / BYTE) / BYTE
                        if(size > 5) {
                            showAlertTop(maxSizeImageMessage)
                            break
                        }

                        // url hình ảnh
                        const urlIMG = URL.createObjectURL(this.files[i])
                        // kiểm tra kích thước hình ảnh
                        isCorrectFrameRate(urlIMG)
                            .then(bool => {
                                if(bool) {
                                    // tạo hình
                                    imageElement = 
                                        `<div id="image-${idx}" data-id="${idx}" class="col-lg-4 col-6">
                                            <div class="image-preview">
                                                <div class="overlay-image-preview"></div>
                                                <div data-id="${idx}" class="delete-image-preview"><i class="far fa-times-circle fz-40"></i></div>
                                                <img data-id="${idx}" class="image_preview_img" src="${urlIMG}" alt="">
                                            </div>
                                        </div>`


                                    idx++;
                                    qty++;

                                    $('.image-preview-div > .row').append(imageElement)
                                    $('#qty-image').text(`(${qty})`);
                                } else {
                                    showToast('Một số hình ảnh không đúng tỉ lệ', TOAST_DELETE_TYPE)
                                }
                            })   
                    }
                    // không phải hình ảnh
                    else{
                        showAlertTop('Bạn chỉ có thể upload hình ảnh');
                        break;
                    }
                }

                $(this).val('')
            });
    
            // xóa hình
            $(document).on('click', '.delete-image-preview', function(){
                var id = $(this).data('id');
                var imageDelete = $('#image-' + id);
    
                if(imageDelete.attr('data-name') !== undefined){
                    const imageName = imageDelete.attr('data-name').split('?') [0]
                    arrayDelete.push(imageName);
                }
                imageDelete.remove();
    
                var qty = $('.image-preview-div > .row').children().length;
    
                if(qty === 0){
                    $('#qty-image').hide();
                    $('#image_inp').val('');
                } else {
                    $('#qty-image').text(`(${qty})`);
                }
            });
    
            $('#model').change(function(){
                const action = $('#action-btn').attr('data-type')
                
                if(action === 'edit') {
                    const id = $(this).val();
                    bindHinhAnh(id);
                }
            });
    
            // reset modal
            $('#modal').on('hidden.bs.modal', function(){
                $('#form').trigger('reset');
                $('select').attr('disabled', false);
                $('.image-preview-div > .row').children().remove();
                $('.required').removeClass('required')
                $('.required-text').remove()
                $('#choose_image').show();
                $('#action-btn').show();
                idx = 1;
                arrayDelete = [];
                $('#qty-image').text('');
                $('#image_inp').val('');
            });
    
            function bindHinhAnh(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/admin/hinhanh/ajax-get-hinhanh',
                    type: 'POST',
                    data: {'id': id},
                    cache: false,
                    success:function(data){
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);
                        $('select').attr('disabled', bool);
    
                        var lst_image = data.lst_image;
                        var lst_model = data.lst_model;
    
                        const urlImage = 'images/phone/' 
    
                        // gán hình ảnh + gán mẫu sp
                        Promise.all([renderImage(lst_image, urlImage), assignModel(lst_model, id)])
                            .catch(() => showAlertTop(errorMessage))
    
                        if(bool) {
                            $('.overlay-image-preview').remove();
                            $('.delete-image-preview').remove();
                            $('#action-btn').hide();
                            $('#choose_image').hide();
                        } else {
                            $('#action-btn').show()
                            $('#choose_image').show()
                        }
    
                        $('#qty-image').text(`(${lst_image.length})`);
    
                        // thiết lập nút gửi là cập nhật
                        $('#action-btn').attr('data-type', 'edit');
                        $('#action-btn').text('Cập nhật');
                        $('#action-btn').attr('data-id', id);
    
                        // hiển thị modal
                        $('#modal').modal('show');
                    },
                    error: function() { showAlertTop(errorMessage) }
                });
            }
    
            // bẫy lỗi hình ảnh
            function validateImage() {
                var image = $('.image-preview-div');
                if(image.hasClass('required')){
                    $('.modal-body').animate({scrollTop: image.position().top});
                    return false;
                }
    
                // chưa chọn
                if($('.image-preview-div > .row').children().length == 0){
                    image.addClass('required');
                    image.after('<span class="required-text">Vui lòng chọn hình ảnh</span>');
                    $('.modal-body').animate({scrollTop: image.position().top});
                    return false;
                }
    
                return true;
            }
    
            async function addOrUpdate(button) {
                // bẫy lỗi
                var valiImage = validateImage();
    
                if(valiImage){
                    $('.loader').fadeIn();

                    const id_msp = $('#model').val()
                    // tổng dung lượng file upload: MB
                    let totalSize = 0
    
                    const type = button.attr('data-type')
                    let arrayBase64 = []
    
                    await pushBase64ToArray(type)
                        .then(base64 => {
                            arrayBase64 = base64
                            // byte => KB => MB
                            totalSize = (new Blob([base64.join('')]).size / BYTE) / BYTE + 1
                        })

                    // upload files quá kích thước cho phép
                    if(totalSize > POST_SIZE_MAX) {
                        // upload từng file một
                        const length = arrayBase64.length - 1
                        if(type === 'create') {
                            $.each(arrayBase64, (i, val) => {
                                if(i === length) {
                                    addSingleFile(id_msp, val, true)
                                        .then(data => {
                                            $('.loader').fadeOut();
                                            $('#modal').modal('hide');
                
                                            // render vào view
                                            $('#lst_data').children().remove();
                                            const html = renderNewRow(page, data.data)
                                            $('#lst_data').append(html);
                
                                            // toast + highlight row
                                            toastAndHighlight(CREATE_MESSAGE, data.id)
                                            return
                                        })
                                        .catch(() => showToast(errorMessage))
                                } else {
                                    addSingleFile(id_msp, val)
                                        .catch(() => showToast(errorMessage))
                                }
                            })
                        } else {
                            $.each(arrayBase64, (i, val) => {
                                if(i === length) {
                                    updateSingleFile(id_msp, val, i, arrayDelete, true)
                                        .then(data => {
                                            $('.loader').fadeOut();
                                            $('#modal').modal('hide');
                
                                            // thay thế
                                            const newRow = renderNewRow(page, data)
                                            $(`tr[data-id="${id_msp}"]`).replaceWith(newRow);
                
                                            // toast + highlight row
                                            toastAndHighlight(EDIT_MESSAGE, id_msp)
                                            return
                                        })
                                        .catch(() => showToast(errorMessage))
                                } else {
                                    updateSingleFile(id_msp, val, i)
                                        .catch(() => showToast(errorMessage))
                                }
                            })
                        }
                    } else {
                        // thêm mới
                        if(type === 'create'){
                            var data = {
                                id_msp,
                                'lst_base64': arrayBase64,
                            };
                            
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                                },
                                url: 'admin/hinhanh',
                                type: 'POST',
                                data: data,
                                success:function(data){
                                    $('.loader').fadeOut();
                                    $('#modal').modal('hide');
        
                                    // render vào view
                                    $('#lst_data').children().remove();
                                    const html = renderNewRow(page, data.data)
                                    $('#lst_data').append(html);
        
                                    // toast + highlight row
                                    toastAndHighlight(CREATE_MESSAGE, data.id)
                                },
                                error: function() {
                                    $('.loader').fadeOut();
                                    showAlertTop(errorMessage)
                                }
                            });
                        }
                        // chỉnh sửa
                        else {
                            var id = $(button).attr('data-id');
        
                            var data = {
                                'lst_base64': arrayBase64,
                                'lst_delete': arrayDelete,
                            };
        
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': X_CSRF_TOKEN
                                },
                                url: 'admin/hinhanh/' + id,
                                type: 'PUT',
                                data: data,
                                success:function(data){
                                    $('.loader').fadeOut();
                                    $('#modal').modal('hide');
        
                                    // thay thế
                                    const newRow = renderNewRow(page, data)
                                    $(`tr[data-id="${id}"]`).replaceWith(newRow);
        
                                    // toast + highlight row
                                    toastAndHighlight(EDIT_MESSAGE, id)
                                },
                                error: function() {
                                    $('.loader').fadeOut();
                                    showAlertTop(errorMessage)
                                }
                            });
                        }
                    }
                }
            }

            function addSingleFile(id_msp, base64String, lastItem = false) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
                        url: 'admin/hinhanh/ajax-add-single-file',
                        type: 'POST',
                        data: {
                            id_msp,
                            base64String,
                            lastItem
                        },
                        success: function (data) {
                            resolve(data)
                        },
                        error: function (error) {
                            console.log(error)
                            reject()
                        }
                    })
                })
            }

            function updateSingleFile(id_msp, base64String, idx, arrayDelete = [], lastItem = false) {
                return new Promise((resolve, reject) => {
                    const index = idx + 1;
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
                        url: 'admin/hinhanh/ajax-update-single-file',
                        type: 'POST',
                        data: {
                            id_msp,
                            base64String,
                            index,
                            arrayDelete,
                            lastItem
                        },
                        success: function(data) {
                            resolve(data)
                        },
                        error: function(error) {
                            console.error(error)
                            reject()
                        }
                    })
                })
            }

            function isCorrectFrameRate(url) {
                return new Promise(resolve => {
                    const image = new Image()
                    image.src = url
                    image.onload = function() {
                        const width = this.width
                        const height = this.height
                        
                        resolve(width === height)
                    }
                })
            }

            break
        }
        /*=======================================================================================================================
                                                            Kho
        =======================================================================================================================*/
        case 'kho': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            // modal tạo mới
            $('.create-btn').off('click').click(function(){
                var id_cn = $('#branch').val();
    
                changeBranch(id_cn)
                    .then((data) => {
                        if(data.length === 0) {
                            var elmnt = $('<span>Tất cả sản phẩm đã được thêm vào kho.</span>');
                            elmnt.appendTo($('#product'));
                            $('#action-btn').hide();
                        } else {
                            renderProductOption(data, id_cn)
                            $('#action-btn').show()
                        }

                        // gán dữ liệu cho modal
                        $('#modal-title').text('Thêm vào kho');
    
                        // thiết lập nút gửi là thêm mới
                        $('#action-btn').attr('data-type', 'create');
                        $('#action-btn').text('Thêm');
    
                        // hiển thị modal
                        $('#modal').modal('show');
                    })
                    .catch(() => showAlertTop(errorMessage))
            });
    
            // modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chỉnh sửa kho');
                bindKho(id);
            });
    
            // modal xóa
            $(document).on('click', '.delete-btn', function(){
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-branch');
    
                // gán dữ liệu cho modal xóa
                $('#delete-content').html('Xóa sản phẩm này khỏi kho tại chi nhánh <b>'+name+'?<b></b>');
                $('#delete-btn').attr('data-id', id);
                $('#delete-modal').modal('show');
            });
    
            // thêm|sửa
            $('#action-btn').click(function(){
                // bẫy lỗi
                var valiQtyInStock = validateQtyInStock($('#qty_in_stock'));
    
                // bẫy lỗi xong kiểm tra loại
                if(valiQtyInStock){
                    $('.loader').fadeIn();
    
                    var data = {
                        'id_cn': $('#branch').val(),
                        'id_sp': $('#product_id_inp').val(),
                        'slton': $('#qty_in_stock').val(),
                    };
                    // thêm mới
                    if($(this).attr('data-type') === 'create'){
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/kho',
                            type: 'POST',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                $('#modal').modal('hide');
    
                                // render vào view đầu danh sách
                                const newRow = renderNewRow(page, data.data)
                                $('#lst_data').prepend(newRow);
    
                                // toast + highlight row
                                toastAndHighlight(CREATE_MESSAGE, data.id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                    // chỉnh sửa
                    else {
                        var id = $(this).attr('data-id');
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/kho/' + id,
                            type: 'PUT',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                $('#modal').modal('hide');
    
                                // thay thế
                                const newRow = renderNewRow(page, data)
                                $(`tr[data-id="${id}"]`).replaceWith(newRow); 
                                
                                // toast + highlight row
                                toastAndHighlight(EDIT_MESSAGE, id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                }
            });
    
            // xóa
            $('#delete-btn').click(function(){
                var id = $(this).attr('data-id');
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/kho/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');
    
                        // xóa dòng
                        $(`tr[data-id="${id}"]`).remove(); 
    
                        // toast
                        showToast(DELETE_MESSAGE, TOAST_DELETE_TYPE);
                    }
                });
            });
    
            // thay đổi chi nhánh thêm|sửa kho
            $('#branch').change(function(){
                const id_cn = $(this).val();
                const action = $('#action-btn').attr('data-type')

                if(action === 'create') {
                    changeBranch(id_cn)
                        .then((data) => {
                            if(data.length === 0) {
                                var elmnt = $('<span>Tất cả sản phẩm đã được thêm vào kho.</span>');
                                elmnt.appendTo($('#product'));
                                $('#action-btn').hide();
                            } else {
                                renderProductOption(data, id_cn);
                                $('#action-btn').show();
                            }
                        })
                        .catch(() => showAlertTop(errorMessage))
                } else {
                    const id_sp = $('#product_id_inp').val()
                    $('#action-btn').show();

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': X_CSRF_TOKEN},
                        url: 'admin/kho/ajax-get-kho',
                        type: 'POST',
                        data: {
                            id_cn,
                            id_sp
                        },
                        success: function(data) {
                            return bindKho(data.id);
                        },
                        error: function() {
                            showAlertTop(errorMessage)
                        }
                    })
                }
            });
    
            // show options sản phẩm
            $('#product-selected').click(function(){
                $('#product-box').toggle('blind', 250);
            });
    
            // tìm kiếm sp thêm|sửa kho
            $('#search-product').keyup(function(){
                var value = $(this).val().toLocaleLowerCase();
    
                if(value == ''){
                    $('#list-product').children().show();
                    return;
                }
    
                var length = $('#list-product').children().length;
    
                for(var i = 0; i < length; i++){
                    var child = $($('#list-product').children()[i]);
                    var name = child.data('name').toLocaleLowerCase();
    
                    if(name.includes(value)){
                        child.show();
                    } else {
                        child.hide();
                    }
                }
            });
    
            // chọn sản phẩm thêm|sửa kho
            $(document).on('click', '.product-option', function(){
                var id_sp = $(this).data('id');
                var id_cn = $(this).data('branch');
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/kho/ajax-get-product-by-id',
                    type: 'POST',
                    data: {'id_sp': id_sp, 'id_cn': id_cn},
                    success:function(data){
                        $('#product').children().remove();
                        renderProductOption(data.product, id_cn)
    
                        $('#product-box').hide('blind', 250);
    
                        if(data.warehouse != null) {
                            $('#action-btn').attr('data-id', data.warehouse.id);
                            $('#qty_in_stock').val(data.warehouse.slton);
                        }
                    }
                });
            });
    
            $('#qty_in_stock').change(function(){
                removeRequried($(this));
            });
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    if(arrFilter.length != 0){
                        return filter()
                    } else {
                        const keyword = $(this).val().toLocaleLowerCase();
    
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/kho/ajax-search',
                            type: 'POST',
                            data: {'keyword': keyword},
                            success: function(data){
                                const lst_data = $('#lst_data')
    
                                const searchResult = renderNewRow(page, data)
                                lst_data.append(searchResult);
    
                                if(lst_data.attr('data-loadmore') === 'done') {
                                    lst_data.removeAttr('data-loadmore')
                                }
    
                                loadMoreFlag = keyword == '' ? false : true;
                                $('#loadmore').hide();
                            },
                            error: function() {
                                $('#loadmore').hide();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                }, 500);
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });
    
            // show lọc
            $('#filter-kho').click(function(){
                $('.filter-div').toggle('blind');
            });
    
            var arrFilter = [];
            // thêm|hủy lọc
            $('[name="filter"]').change(function(){
                if($(this).is(':checked')){
                    arrFilter.push($(this).val())
                } else {
                    var i = arrFilter.indexOf($(this).val());
                    arrFilter.splice(i, 1);
                }
    
                filter();
            });
    
            // reset modal
            $('#modal').on('hidden.bs.modal', function(){
                $('#form').trigger('reset');
                $('select').attr('disabled', false);
                $('input, textarea').attr('readonly', false);
                $('#product').children().remove();
                $('#list-product').children().remove();
                $('#product-box').hide();
            });
    
            function renderProductOption(productList, id_cn, selectedProduct = false) {
                // sản phẩm đang chọn
                let selected = ''
    
                // không có sản phẩm đang chọn
                if(!selectedProduct) {
                    selected =
                        `<img src="images/phone/${productList[0].hinhanh}" alt="product image" width="70px">
                        <div class="ml-10 fz-14">
                            <div class="d-flex align-items-center fw-600">
                                ${productList[0].tensp}<i class="fas fa-circle ml-5 mr-5 fz-5"></i>${productList[0].mausac}
                            </div>
                            <div>Ram: ${productList[0].ram}</div>
                            <div>Dung lượng: ${productList[0].dungluong}</div>
                        </div>`
    
                        $('#product_id_inp').val(productList[0].id);
                } else {
                    selected =
                        `<img src="images/phone/${selectedProduct.hinhanh}" alt="product image" width="70px">
                        <div class="ml-10 fz-14">
                            <div class="d-flex align-items-center fw-600">
                                ${selectedProduct.tensp}<i class="fas fa-circle ml-5 mr-5 fz-5"></i>${selectedProduct.mausac}
                            </div>
                            <div>Ram: ${selectedProduct.ram}</div>
                            <div>Dung lượng: ${selectedProduct.dungluong}</div>
                        </div>`
    
                    $('#product_id_inp').val(selectedProduct.id);
                }
    
                $('#product').append(selected)
    
                // danh sách sản phẩm
                const products = productList.map(val => {
                    return (
                        `<div data-id="${val.id}" data-name="${val.tensp}" data-branch="${id_cn}" class="product-option select-single-option">
                            <div class="d-flex">
                                <img src="images/phone/${val.hinhanh}" alt="product image" width="70px">
                                <div class="ml-10 fz-14">
                                    <div class="d-flex align-items-center fw-600">
                                        ${val.tensp}<i class="fas fa-circle ml-5 mr-5 fz-5"></i>${val.mausac}
                                    </div>
                                    <div>Ram: ${val.ram}</div>
                                    <div>Dung lượng: ${val.dungluong}</div>
                                </div>
                            </div>
                        </div>`
                    )
                }).join('')
    
                $('#list-product').append(products)
            }
    
            function bindKho(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/admin/kho/ajax-get-kho',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);
                        $('select').attr('disabled', bool);

                        $('#product').children().remove();
                        $('#list-product').children().remove();
    
                        // gán dữ liệu cho modal
                        var id_cn = data.chinhanh.id;
    
                        $(`#branch option[value="${data.chinhanh.id}"]`).prop('selected', true);
                        $('#qty_in_stock').val(data.slton);
    
                        renderProductOption(data.lst_product, id_cn, data.selected_product);
                    
                        // thiết lập nút gửi là cập nhật
                        $('#action-btn').attr('data-type', 'edit');
                        $('#action-btn').text('Cập nhật');
                        $('#action-btn').attr('data-id', id);
                        $('#action-btn').show();
    
                        // hiển thị modal
                        $('#modal').modal('show');
                    },
                    error: function() {
                        showAlertTop(errorMessage)
                    }
                });            
            }
    
            // bẫy lỗi số lượng
            function validateQtyInStock(qty) {
                if(qty.hasClass('required')){
                    return false;
                }
    
                const value = qty.val()
    
                // chưa nhập
                if(value == ''){
                    qty.addClass('required');
                    qty.after('<span class="required-text">Vui lòng nhập số lượng</span>');
                    return false;
                }
    
                // số lượng không hợp lệ
                if(value < 0) {
                    qty.addClass('required');
                    qty.after('<span class="required-text">Số lượng không hợp lệ</span>');
                    return false;
                } else if(value > 100) {
                    qty.addClass('required');
                    qty.after('<span class="required-text">Số lượng tối đa là 100</span>');
                    return false;
                }
    
                return true;
            }
    
            function filter() {
                $('#lst_data').children().remove();
                $('#loadmore').show();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/kho/ajax-filter',
                    type: 'POST',
                    data: {'arrFilter': arrFilter, 'keyword': $('#search').val().toLocaleLowerCase()},
                    success: function(data){
                        const lst_data = $('#lst_data')
    
                        const result = renderNewRow(page, data)
                        lst_data.append(result);
    
                        if(arrFilter.length === 0) {
                            if(lst_data.attr('data-loadmore') === 'done') {
                                lst_data.removeAttr('data-loadmore')
                            }
    
                            loadMoreFlag = false
                        } else {
                            loadmoreFlag = true
                        }
    
                        $('#loadmore').hide();
                    },
                    error: function() {
                        $('#loadmore').hide();
                        showAlertTop(errorMessage)
                    }
                });
            }
    
            function changeBranch(id_cn) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'admin/kho/ajax-get-product-isnot-in-stock',
                        type: 'POST',
                        data: {id_cn},
                        success:function(data){
                            $('#product').children().remove();
                            $('#list-product').children().remove();

                            resolve(data)
                        },
                        error: function() { reject() }
                    });
                })
            }

            break
        }
        /*=======================================================================================================================
                                                            Chi nhánh
        =======================================================================================================================*/
        case 'chinhanh': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            // modal tạo mới
            $('.create-btn').off('click').click(function(){
                // gán dữ liệu cho modal
                $('#modal-title').text('Tạo mới chi nhánh');
                $('#status').hide();
                $('label[for="status"]').hide();
    
                // thiết lập nút gửi là thêm mới
                $('#action-btn').attr('data-type', 'create');
                $('#action-btn').text('Thêm');
    
                // hiển thị modal
                $('#modal').modal('show');
            });
    
            // modal chi tiết
            $(document).on('click', '.info-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chi tiết chi nhánh');
                bindChiNhanh(id, true);
            });
    
            // modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chỉnh sửa chi nhánh');
                bindChiNhanh(id);
            });
    
            // modal xóa
            $(document).on('click', '.delete-btn', function(){
                var id = $(this).attr('data-id');
    
                // gán dữ liệu cho modal xóa
                $('#delete-content').html('Xóa chi nhánh?</b>');
                $('#delete-btn').attr('data-id', id);
                $('#delete-modal').modal('show');
            });
    
            // thêm|sửa
            $('#action-btn').click(function(){
                // bẫy lỗi
                var valiAddress = validateAddress($('#address'));
                var valiTel = validatePhoneNumber($('#tel'));
    
                // bẫy lỗi xong kiểm tra loại
                if(valiAddress && valiTel){
                    $('.loader').fadeIn();
    
                    var data = {
                        'diachi': $('#address').val().trim(),
                        'sdt': $('#tel').val(),
                        'id_tt': $('#province').val(),
                        'trangthai': $('#status').val(),
                    };
                    // thêm mới
                    if($(this).attr('data-type') == 'create'){
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/chinhanh',
                            type: 'POST',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                $('#modal').modal('hide');
    
                                // thêm vào đầu danh sách
                                const newRow = renderNewRow(page, data.data)
                                $('#lst_data').prepend(newRow);
    
                                // toast + highlight row
                                toastAndHighlight(CREATE_MESSAGE, data.id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                    // chỉnh sửa
                    else {
                        var id = $(this).attr('data-id');
    
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/chinhanh/' + id,
                            type: 'PUT',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                $('#modal').modal('hide');
    
                                // thay thế
                                const newRow = renderNewRow(page, data)
                                $(`tr[data-id="${id}"]`).replaceWith(newRow); 
                                
                                // toast + highlight row
                                toastAndHighlight(EDIT_MESSAGE, id)
                            }
                        });
                    }
                }
            });
    
            $('#address').keyup(function(){
                removeRequried($(this));
            });
            $('#tel').keyup(function(){
                valiPhonenumberTyping($(this));
            });
    
            // reset modal
            $('#modal').on('hidden.bs.modal', function(){
                $('#form').trigger('reset');
                $('select').attr('disabled', false);
                $('input, textarea').attr('readonly', false);
                $('.required').removeClass('required')
                $('.required-text').remove()
                $('#status').show();
                $('label[for="status"]').show();
            });
    
            // xóa
            $('#delete-btn').click(function(){
                var id = $(this).attr('data-id');
    
                $('.loader').fadeIn();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/chinhanh/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');
    
                        // thay nút
                        var restoreBtn = $(`<div data-id="${id}" class="undelete-btn"><i class="fas fa-trash-undo"></i></div>`)
                        $(`.delete-btn[data-id="${id}"]`).replaceWith(restoreBtn);
    
                        // cập nhật trạng thái
                        $(`.trangthai[data-id="${id}"]`).text('Ngừng hoạt động');
    
                        // toast + highlight row
                        toastAndHighlight(DELETE_MESSAGE, id)
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            });
    
            // phục hồi
            $(document).on('click', '.undelete-btn', function(){
                var id = $(this).attr('data-id');
                restore(id);
            });
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    var keyword = $(this).val().toLocaleLowerCase();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'admin/chinhanh/ajax-search',
                        type: 'POST',
                        data: {'keyword': keyword},
                        success: function(data){
                            const lst_data = $('#lst_data')
    
                            const searchResult = renderNewRow(page, data)
                            lst_data.append(searchResult);
    
                            if(lst_data.attr('data-loadmore') === 'done') {
                                lst_data.removeAttr('data-loadmore')
                            }
    
                            loadMoreFlag = keyword == '' ? false : true;
                            $('#loadmore').hide();
                        },
                        error: function() {
                            $('#loadmore').hide();
                            showAlertTop(errorMessage);
                        }
                    });
                }, 300);
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });
    
            function bindChiNhanh(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/admin/chinhanh/ajax-get-chinhanh',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);
                        $('select').attr('disabled', bool);
    
                        // gán dữ liệu cho modal
                        $('#address').val(data.diachi);
                        $('#tel').val(data.sdt);
                        $(`#province option[value="${data.id_tt}"]`).prop('selected', true);
                        $(`#status option[value="${data.trangthai}"]`).prop('selected', true);
    
                        // thiết lập nút gửi là cập nhật
                        $('#action-btn').attr('data-type', 'edit');
                        $('#action-btn').text('Cập nhật');
                        $('#action-btn').attr('data-id', id);
    
                        // ẩn/hiện nút thêm (cập nhật);
                        bool == false ? $('#action-btn').show() : $('#action-btn').hide();
    
                        // hiển thị modal
                        $('#modal').modal('show');
                    }, error: function() { showAlertTop(errorMessage) }
                });
            }
    
            function restore(id) {
                $('.loader').fadeIn();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/chinhanh/ajax-restore',
                    type: 'POST',
                    data: {'id': id},
                    success: function(){
                        $('.loader').fadeOut();
    
                        // thay nút
                        var deleteBtn = $(`<div data-id="${id}" class="delete-btn"><i class="fas fa-trash"></i></div>`)
                        $(`.undelete-btn[data-id="${id}"]`).replaceWith(deleteBtn);
    
                        // cập nhật trạng thái
                        $(`.trangthai[data-id="${id}"]`).text('Hoạt động');
    
                        // toast + highlight row
                        toastAndHighlight(EDIT_MESSAGE, id)
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                })
            }

            break
        }
        /*=======================================================================================================================
                                                            Tỉnh thành
        =======================================================================================================================*/
        case 'tinhthanh': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            // modal tạo mới
            $('.create-btn').off('click').click(function(){
                // gán dữ liệu cho modal
                $('#modal-title').text('Tạo mới tỉnh thành');
    
                // thiết lập nút gửi là thêm mới
                $('#action-btn').attr('data-type', 'create');
                $('#action-btn').text('Thêm');
    
                // hiển thị modal
                $('#modal').modal('show');
            });
    
            // modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chỉnh sửa tỉnh thành');
                bindTinhThanh(id);
            });
    
            // modal xóa
            $(document).on('click', '.delete-btn', function(){
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');
    
                // gán dữ liệu cho modal xóa
                $('#delete-content').html('Xóa <b>'+name+'?<b></b>');
                $('#delete-btn').attr('data-id', id);
                $('#delete-modal').modal('show');
            });
    
            // thêm|sửa
            $('#action-btn').click(function(){
                // bẫy lỗi
                var valiName = validateName($('#name'));
    
                // bẫy lỗi xong kiểm tra loại
                if(valiName){
                    $('.loader').fadeIn();
    
                    var data = {
                        'tentt': capitalize($('#name').val().trim())
                    };
                    // thêm mới
                    if($(this).attr('data-type') == 'create'){
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/tinhthanh',
                            type: 'POST',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
    
                                // tỉnh thành đã tồn tại
                                if(data == 'exists'){
                                    $('#name').addClass('required');
                                    $('#name').after('<span class="required-text">Tỉnh thành này đã tồn tại</span>');
                                    return;
                                }
                                $('#modal').modal('hide');
    
                                // thêm vào đầu danh sách
                                const newRow = renderNewRow(page, data.data)
                                $('#lst_data').prepend(newRow);
    
                                // toast + highlight row
                                toastAndHighlight(CREATE_MESSAGE, data.id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                    // chỉnh sửa
                    else {
                        var id = $(this).attr('data-id');
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/tinhthanh/' + id,
                            type: 'PUT',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
    
                                // tỉnh thành đã tồn tại
                                if(data == 'exists'){
                                    $('#name').addClass('required');
                                    $('#name').after('<span class="required-text">Tỉnh thành này đã tồn tại</span>');
                                    return;
                                }
                                
                                $('#modal').modal('hide');
    
                                // thay thế
                                const newRow = renderNewRow(page, data)
                                $(`tr[data-id="${id}"]`).replaceWith(newRow); 
                                
                                // toast + highlight row
                                toastAndHighlight(EDIT_MESSAGE, id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                }
            });
    
            // xóa
            $('#delete-btn').click(function(){
                var id = $(this).attr('data-id');
    
                $('.loader').fadeIn();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/tinhthanh/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');
    
                        // xóa dòng
                        $(`tr[data-id="${id}"]`).remove(); 
    
                        // toast
                        showToast(DELETE_MESSAGE, TOAST_DELETE_TYPE);
    
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            });
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    var keyword = $(this).val().toLocaleLowerCase();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'admin/tinhthanh/ajax-search',
                        type: 'POST',
                        data: {'keyword': keyword},
                        success: function(data){
                            const lst_data = $('#lst_data')
    
                            const searchResult = renderNewRow(page, data)
                            lst_data.append(searchResult);
    
                            if(lst_data.attr('data-loadmore') === 'done') {
                                lst_data.removeAttr('data-loadmore')
                            }
    
                            loadMoreFlag = keyword == '' ? false : true;
                            $('#loadmore').hide();
                        },
                        error: function() {
                            $('#loadmore').hide();
                            showAlertTop(errorMessage)
                        }
                    });
                }, 300);
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });
    
            $('#name').keyup(function(){
                removeRequried($(this));
            });
    
            // reset modal
            $('#modal').on('hidden.bs.modal', function(){
                $('#form').trigger('reset');
                $('input').attr('readonly', false);
            });
    
            function bindTinhThanh(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/admin/tinhthanh/ajax-get-tinhthanh',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);
                        $('select').attr('disabled', !bool);
    
                        // gán dữ liệu cho modal
                        $('#name').val(data.tentt);
                    
                        // thiết lập nút gửi là cập nhật
                        $('#action-btn').attr('data-type', 'edit');
                        $('#action-btn').text('Cập nhật');
                        $('#action-btn').attr('data-id', id);
    
                        // ẩn/hiện nút thêm (cập nhật);
                        bool == false ? $('#action-btn').show() : $('#action-btn').hide();
    
                        // hiển thị modal
                        $('#modal').modal('show');
                    },
                    error: function() { showAlertTop(errorMessage) }
                });
            }

            break
        }
        /*=======================================================================================================================
                                                            Voucher
        =======================================================================================================================*/
        case 'voucher': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            // modal tạo mới
            $('.create-btn').off('click').click(function(){
                // gán dữ liệu cho modal
                $('#modal-title').text('Tạo mới voucher');
    
                // thiết lập nút gửi là thêm mới
                $('#action-btn').attr('data-type', 'create');
                $('#action-btn').text('Thêm');
    
                // hiển thị modal
                $('#modal').modal('show');
            });
    
            // modal chi tiết
            $(document).on('click', '.info-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chi tiết voucher');
                bindVoucher(id, true);
            });
    
            // modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chỉnh sửa voucher');
                bindVoucher(id);
            });
    
            // modal xóa
            $(document).on('click', '.delete-btn', function(){
                var id = $(this).attr('data-id');
    
                // gán dữ liệu cho modal xóa
                $('#delete-content').text('Xóa voucher này ?');
                $('#delete-btn').attr('data-id', id);
                $('#delete-modal').modal('show');
            });
    
            // thêm|sửa
            $('#action-btn').click(function(){
                // bẫy lỗi
                var valiCode = validateCode($('#code'));
                var valiDiscount = validateDiscount($('#discount'));
                var valiQty = validateQty($('#qty'));
                var valiContent = validateVoucherContent($('#content'));
                var valiStart = validateDateStart($('#start'));
                var valiEnd = validateDateEnd($('#end'), $('#start'));
    
                // bẫy lỗi xong kiểm tra loại
                if(valiCode && valiDiscount && valiQty && valiContent && valiStart && valiEnd){
                    $('.loader').fadeIn();
    
                    var data = {
                        'code': $('#code').val(),
                        'noidung': $('#content').val(),
                        'chietkhau': ($('#discount').val()/100),
                        'dieukien': $('#condition').val(),
                        'ngaybatdau': $('#start').val(),
                        'ngayketthuc': $('#end').val(),
                        'sl': $('#qty').val(),
                    };
                    // thêm mới
                    if($(this).attr('data-type') == 'create'){
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/voucher',
                            type: 'POST',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
    
                                // voucher đã tồn tại
                                if(data == 'exists'){
                                    $('#code').addClass('required');
                                    $('#code').after('<span class="required-text">Voucher này đã tồn tại</span>');
                                    return;
                                }
                                $('#modal').modal('hide');
    
                                // thêm vào đầu danh sách
                                const newRow = renderNewRow(page, data.data)
                                $('#lst_data').prepend(newRow);
    
                                // toast + highlight row
                                toastAndHighlight(CREATE_MESSAGE, data.id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage);
                            }
                        });
                    }
                    // chỉnh sửa
                    else {
                        var id = $(this).attr('data-id');
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/voucher/' + id,
                            type: 'PUT',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
    
                                // tỉnh thành đã tồn tại
                                if(data == 'exists'){
                                    $('#code').addClass('required');
                                    $('#code').after('<span class="required-text">Voucher này đã tồn tại</span>');
                                    return;
                                }
                                
                                $('#modal').modal('hide');
    
                                // thay thế
                                const newRow = renderNewRow(page, data)
                                $(`tr[data-id="${id}"]`).replaceWith(newRow); 
                                
                                // toast + highlight row
                                toastAndHighlight(EDIT_MESSAGE, id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                }
            });
    
            // xóa
            $('#delete-btn').click(function(){
                var id = $(this).attr('data-id');
    
                $('.loader').fadeIn();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/voucher/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');
    
                        // xóa dòng
                        $(`tr[data-id="${id}"]`).remove();
    
                        // toast
                        showToast(DELETE_MESSAGE, TOAST_DELETE_TYPE);
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTOp(errorMessage)
                    }
                });
            });
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    var keyword = $(this).val().toLocaleLowerCase();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'admin/voucher/ajax-search',
                        type: 'POST',
                        data: {'keyword': keyword},
                        success: function(data){
                            const lst_data = $('#lst_data')
    
                            const searchResult = renderNewRow(page, data)
                            lst_data.append(searchResult);
    
                            if(lst_data.attr('data-loadmore') === 'done') {
                                lst_data.removeAttr('data-loadmore')
                            }
    
                            loadMoreFlag = keyword == '' ? false : true;
                            $('#loadmore').hide();
                        },
                        error: function() {
                            $('#loadmore').hide();
                            showAlertTop(errorMessage)
                        }
                    });
                }, 300);
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });
    
            $('#code').keyup(function(){
                removeRequried($(this));
                var upper = $(this).val().toUpperCase();
                $(this).val(upper);
            });
    
            $('#discount').change(function(){
                removeRequried($(this));
            });
    
            $('#condition').change(function(){
                removeRequried($(this));
            });
    
            $('#qty').change(function(){
                removeRequried($(this));
            });
    
            $('#content').keyup(function(){
                removeRequried($(this));
            });
    
            $('#start').change(function(){
                removeRequried($(this));
            });
    
            $('#end').change(function(){
                removeRequried($(this));
            });
    
            // reset modal
            $('#modal').on('hidden.bs.modal', function(){
                $('#form').trigger('reset');
                $('input, textarea').attr('readonly', false);
                $('select').attr('disabled', false);
                $('.required').removeClass('required')
                $('.required-text').remove();
                $('#status').show();
                $('label[for="status"]').show();
                $('#action-btn').show();
            });
    
            function bindVoucher(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/admin/voucher/ajax-get-voucher',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);
                        $('select').attr('disabled', bool);
    
                        // gán dữ liệu cho modal
                        $('#code').val(data.code);
                        $('#discount').val(data.chietkhau * 100);
                        $('#condition').val(data.dieukien);
                        $('#qty').val(data.sl);
                        $('#content').val(data.noidung);
                        $('#start').val(data.ngaybatdau);
                        $('#end').val(data.ngayketthuc);
                    
                        // thiết lập nút gửi là cập nhật
                        $('#action-btn').attr('data-type', 'edit');
                        $('#action-btn').text('Cập nhật');
                        $('#action-btn').attr('data-id', id);
    
                        // ẩn/hiện nút thêm (cập nhật);
                        bool == false ? $('#action-btn').show() : $('#action-btn').hide();
    
                        // hiển thị modal
                        $('#modal').modal('show');
                    },
                    error: function() { showAlertTop(errorMessage) }
                });
            }
    
            // bẫy lỗi code
            function validateCode(code) {
                if(code.hasClass('required')){
                    return false;
                }
    
                // chưa nhập
                if(code.val().trim() == ''){
                    code.addClass('required');
                    code.after('<span class="required-text">Vui lòng nhập Code</span>');
                    return false;
                }
    
                return true;
            }
    
            // bẫy lỗi số lượng voucher
            function validateQty(qty) {
                if(qty.hasClass('required')){
                    return false;
                }
    
                // chưa nhập
                if(qty.val() == ''){
                    qty.addClass('required');
                    qty.after('<span class="required-text">Vui lòng nhập số lượng</span>');
                    return false;
                }
    
                // giới hạn
                if(qty.val() > 10000) {
                    qty.addClass('required');
                    qty.after('<span class="required-text">Số lượng tối đa là 10000</span>');
                    return false;
                }
    
                return true;
            }
    
            // bẫy lỗi nội dung voucher
            function validateVoucherContent(content) {
                if(content.hasClass('required')){
                    return false;
                }
    
                // chưa nhập
                if(content.val().trim() == ''){
                    content.addClass('required');
                    content.after('<span class="required-text">Vui lòng nhập nội dung</span>');
                    return false;
                }
    
                return true;
            }

            break
        }
        /*=======================================================================================================================
                                                            Đơn hàng
        =======================================================================================================================*/
        case 'donhang': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            // modal chi tiết
            $(document).on('click', '.info-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chi tiết đơn hàng #'+id);
                bindOrder(id, true);
            });
    
            // modal hủy đơn hàng
            $(document).on('click', '.delete-btn', function(){
                var id = $(this).attr('data-id');
    
                // gán dữ liệu cho modal xóa
                $('#delete-content').html(`Hủy đơn hàng <b>#${id}</b>?`);
                $('#delete-btn').attr('data-id', id);
                $('#delete-btn').text('Hủy');
                $('.cancel-btn[data-bs-dismiss="modal"]').text('Đóng');
                $('#delete-modal').modal('show');
            });
    
            // xác nhận đơn hàng
            $(document).on('click', '.confirm-btn', function(){
                var id = $(this).attr('data-id');
                orderConfirmation(id);
            });
    
            // đơn hàng thành công
            $(document).on('click', '.success-btn', function(){
                var id = $(this).attr('data-id');
                successfulOrder(id);
            });
    
            // hủy đơn hàng
            $(document).on('click', '#delete-btn', function(){
                var id = $(this).attr('data-id');
    
                $('.loader').fadeIn()
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/donhang/' + id,
                    type: 'DELETE',
                    success: function(data){
                        console.log(data)
                        $('.loader').fadeOut()
                        $('#delete-modal').modal('hide');
                        
                        // cập nhật trạng thái
                        $(`.trangthaidonhang[data-id="${id}"]`).text('Đã hủy');
    
                        // ẩn nút xóa
                        $(`.delete-btn[data-id="${id}"]`).remove();
    
                        // ẩn nút xác nhận & thành công
                        $(`.confirm-btn[data-id="${id}"]`).remove();
                        $(`.success-btn[data-id="${id}"]`).remove();
    
                        // toast
                        showToast('Đã hủy đơn hàng', TOAST_DELETE_TYPE);
                        
                        // đánh dấu dòng được thêm/chỉnh sửa
                        highlightRow(id, DANGER)
                    },
                    error: function() {
                        $('.loader').fadeOut()
                        showAlertTop(errorMessage)
                    }
                })
            });
    
            $('.modal-body').bind('DOMSubtreeModified', function(){
                setTimeout(() => {
                    var height = $('#receiveMethod').height();
                    $('#paymentMethod').css('height', height);
                }, 200);
            });
    
            // reset modal
            $('#modal').on('hidden.bs.modal', function(){
                $('.modal-body[id="order-modal"]').children()[0].remove();
            });
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    if(Object.keys(arrFilterSort.filter).length != 0){
                        return filterSort()
                    } else {
                        const keyword = $(this).val().toLocaleLowerCase();
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/donhang/ajax-search',
                            type: 'POST',
                            data: {'keyword': keyword},
                            success: function(data){
                                const lst_data = $('#lst_data')
    
                                const searchResult = renderNewRow(page, data)
                                lst_data.append(searchResult);
    
                                if(lst_data.attr('data-loadmore') === 'done') {
                                    lst_data.removeAttr('data-loadmore')
                                }
    
                                loadMoreFlag = keyword == '' ? false : true
    
                                $('#loadmore').hide();
                            },
                            error: function() {
                                $('#loadmore').hide();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                }, 300);
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });
            $('#search').focus(function() {
                $('.filter-div').hide('blind');
                $('.sort-div').hide('blind');
            })
    
            var arrFilterSort = {
                filter: {},
                sort: $('[name="sort"]:checked').val(),
            };
            // show lọc
            $('#filter-donhang').click(function(){
                $('.filter-div').toggle('blind');
                $('.sort-div').hide('blind');
            });
    
            // show sắp xếp
            $('#sort-donhang').click(function(){
                $('.filter-div').hide('blind');
                $('.sort-div').toggle('blind');
            });
    
            // thêm bộ lọc
            $('[name="filter"]').change(function(){
                var obj = $(this).data('object');
                if(obj == 'paymentMethod'){
                    if($(this).is(':checked')){
                        if(arrFilterSort.filter.paymentMethod == null){
                            arrFilterSort.filter.paymentMethod = [];
                        }
                        arrFilterSort.filter.paymentMethod.push($(this).val());
                    } else {
                        var i = arrFilterSort.filter.paymentMethod.indexOf($(this).val());
                        arrFilterSort.filter.paymentMethod.splice(i, 1);
                        if(arrFilterSort.filter.paymentMethod.length == 0){
                            delete arrFilterSort.filter.paymentMethod;
                        }
                    }
                } else if(obj == 'receiveMethod'){
                    if($(this).is(':checked')){
                        if(arrFilterSort.filter.receiveMethod == null){
                            arrFilterSort.filter.receiveMethod = [];
                        }
                        arrFilterSort.filter.receiveMethod.push($(this).val());
                    } else {
                        var i = arrFilterSort.filter.receiveMethod.indexOf($(this).val());
                        arrFilterSort.filter.receiveMethod.splice(i, 1);
                        if(arrFilterSort.filter.receiveMethod.length == 0){
                            delete arrFilterSort.filter.receiveMethod;
                        }
                    }
                } else if(obj == 'status'){
                    if($(this).is(':checked')){
                        if(arrFilterSort.filter.status == null){
                            arrFilterSort.filter.status = [];
                        }
                        arrFilterSort.filter.status.push($(this).val());
                    } else {
                        var i = arrFilterSort.filter.status.indexOf($(this).val());
                        arrFilterSort.filter.status.splice(i, 1);
                        if(arrFilterSort.filter.status.length == 0){
                            delete arrFilterSort.filter.status;
                        }
                    }
                }
    
                filterSort();
            });
    
            // chọn sắp xếp
            $('[name="sort"]').change(function(){
                arrFilterSort.sort = $(this).val();
    
                filterSort();
            });
    
            // hiển thị chi tiết voucher
            $('.modal-body[id="order-modal"]').bind('DOMSubtreeModified', function() {
                $('.promotion-info-icon').mouseover(function(){
                    const id = $(this).data('id');
                    
                    showInfoVoucher(id , $(this));
                }).mouseleave(function(){
                    const id = $(this).data('id');
            
                    hideInfoVoucher(id);
                })
            })
    
            function orderConfirmation(id) {
                $('.loader').fadeIn();
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/donhang/ajax-order-confirmation',
                    type: 'POST',
                    data: {'id': id},
                    success: function(){
                        $('.loader').fadeOut();
    
                        // cập nhật trạng thái
                        $(`.trangthaidonhang[data-id="${id}"]`).text('Đã xác nhận');
    
                        // chuyển thành nút thành công
                        var successBtn = $(`<div data-id="${id}" class="success-btn"><i class="fas fa-box-check"></i></div>`);
                        $(`.confirm-btn[data-id="${id}"]`).replaceWith(successBtn);
    
                        // toast
                        showToast('Đã xác nhận đơn hàng', TOAST_SUCCESS_TYPE);
    
                        // đánh dấu dòng
                        highlightRow(id, SUCCESS);
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                })
            }
    
            function successfulOrder(id) {
                $('.loader').fadeIn();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/donhang/ajax-successful-order',
                    type: 'POST',
                    data: {'id': id}, 
                    success:function(){
                        $('.loader').fadeOut();
    
                        // cập nhật trạng thái
                        $(`.trangthaidonhang[data-id="${id}"]`).text('Thành công');
    
                        // ẩn nút thành công
                        $(`.success-btn[data-id="${id}"]`).remove();
    
                        // ẩn nút hủy đơn hàng
                        $(`.delete-btn[data-id="${id}"]`).remove()
    
                        // toast
                        showToast('Đơn hàng thành công', TOAST_SUCCESS_TYPE);
    
                        // đánh dấu dòng
                        highlightRow(id, SUCCESS)
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            }
    
            function bindOrder(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/admin/donhang/ajax-get-donhang',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // gán dữ liệu cho modal
                        const order = renderOrder(data)
                        $('.modal-body[id="order-modal"]').prepend(order);
                    
                        // thiết lập nút gửi là cập nhật
                        $('#action-btn').attr('data-type', 'edit');
                        $('#action-btn').text('Cập nhật');
                        $('#action-btn').attr('data-id', id);
    
                        // ẩn/hiện nút thêm (cập nhật);
                        bool == false ? $('#action-btn').show() : $('#action-btn').hide();
    
                        // hiển thị modal
                        $('#modal').modal('show');
                    },
                    error: function() { showAlertTop(errorMessage) }
                });
            }
    
            // danh sách kết quả lọc & sắp xếp
            function filterSort(){
                $('#lst_data').children().remove();
                $('#loadmore').show();
                const keyword = $('#search').val().toLocaleLowerCase();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/donhang/ajax-filter-sort',
                    type: 'POST',
                    data: {'arrFilterSort': arrFilterSort, 'keyword': keyword},
                    success: function(data){
                        const lst_data = $('#lst_data')
    
                        const result = renderNewRow(page, data)
                        lst_data.append(result);
    
                        if(Object.keys(arrFilterSort.filter).length == 0){
                            if(lst_data.attr('data-loadmore') === 'done') {
                                lst_data.removeAttr('data-loadmore');
                            }
                            loadMoreFlag = keyword == '' ? false : true
                            $('.filter-badge').hide();    
                        } else {
                            loadMoreFlag = true
                            $('.filter-badge').text(Object.keys(arrFilterSort.filter).length);
                            $('.filter-badge').show();
                        }
                        $('#loadmore').hide();
                    },
                    error: function() {
                        $('#loadmore').hide();
                        showAlertTop(errorMessage)
                    }
                })
            }
    
            function renderOrder(order) {
                // thông tin đơn hàng
                let orderInfo =
                    `<div class="row mb-40">
                        <div class="col-lg-6">
                            <div class="d-flex align-items-end mb-5">
                                <div>Trạng thái đơn hàng:</div>
                                ${order.trangthaidonhang != 'Đã hủy' ?
                                    `<div class="ml-10 fz-20 fw-600 success-color">${order.trangthaidonhang}</div>`
                                    :
                                    `<div class="ml-10 fz-20 fw-600 warning-color">${order.trangthaidonhang}</div>`
                                }
                            </div>
                            <div class="d-flex">
                                <div>Ngày mua:</div>
                                <div class="ml-10 fw-600">${order.thoigian}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="d-flex justify-content-end">
                                <div class="account-badge">
                                    <img src="${order.taikhoan.htdn == 'normal' ? `images/user/${order.taikhoan.anhdaidien}` : order.taikhoan.anhdaidien}" width="40px" class="circle-img">
                                    <div class="ml-10 mr-10 black">${order.taikhoan.hoten}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-50">
                        <div class="col-lg-6">
                            ${order.hinhthuc == 'Giao hàng tận nơi' ?
                                `<div class="mb-5 fw-600">Thông tin giao hàng</div>
                                <div id="receiveMethod">
                                    <div class="d-flex flex-column box-shadow p-20">
                                        <div class="fw-600 text-uppercase mb-5">${order.taikhoan_diachi.hoten}</div>
                                        <div class="d-flex fz-14 mb-5">
                                            <div class="gray-1">Địa chỉ:</div>
                                            <div class="ml-5 black">${order.taikhoan_diachi.diachi}, ${order.taikhoan_diachi.phuongxa}, ${order.taikhoan_diachi.quanhuyen}, ${order.taikhoan_diachi.tinhthanh}</div>
                                        </div>
                                        <div class="d-flex fz-14">
                                            <div class="gray-1">SĐT:</div>
                                            <div class="ml-5 black">${order.taikhoan_diachi.sdt}</div>
                                        </div>
                                    </div>
                                </div>`
                                : 
                                `<div class="mb-5 fw-600">Nhận tại cửa hàng</div>
                                <div id="receiveMethod">
                                    <div class="d-flex flex-column box-shadow p-20">
                                        <div class="d-flex fz-14 mb-5">
                                            <div class="gray-1">Địa chỉ:</div>
                                            <div class="ml-5 black">${order.chinhanh.diachi}</div>
                                        </div>
                                        <div class="d-flex fz-14">
                                            <div class="gray-1">SĐT:</div>
                                            <div class="ml-5 black">${order.chinhanh.sdt}</div>
                                        </div>
                                    </div>
                                </div>`
                            }
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-5 fw-600">Phương thức thanh toán</div>
                            <div id="paymentMethod">
                                <div class="box-shadow p-20 h-100 black">${order.pttt}</div>
                            </div>
                        </div>
                    </div>`
    
                // tạm tính
                let provisional = 0
                order.ctdh.map(val => {
                    return provisional += val.pivot.thanhtien
                })
    
                // danh sách sản phẩm
                let productList = order.ctdh.map((product) => {
                    return (
                        `<tr>
                            <td class="vertical-center">
                                <div class="d-flex pt-10 pb-10">
                                    <img src="images/phone/${product.hinhanh}" alt="" width="100px">
                                    <div class="ml-5">
                                        <div class="fw-600">${product.tensp}</div>
                                        <div>Ram: ${product.ram}</div>
                                        <div>Dung lượng: ${product.dungluong}</div>
                                        <div>Màu sắc: ${product.mausac}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">
                                    ${numberWithDot(product.gia)}<sup>đ</sup>
                                </div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${product.pivot.sl}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${product.pivot.giamgia ? `-${product.pivot.giamgia*100}%` : '0'}</div>
                            </td>
                            <td class="vertical-center">
                                <div class="pt-10 pb-10">${numberWithDot(product.pivot.thanhtien)}<sup>đ</sup></div>
                            </td>
                        </tr>`
                    )
                })
    
                // voucher
                let voucher = ''
                if(order.id_vc) {
                    voucher =
                        `<tr>
                            <td colspan="5" class="p-0">
                                <div class="d-flex">
                                    <div class="w-20 bg-gray-4 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-ticket-alt mr-10"></i>Mã giảm giá
                                    </div>
                                    
                                    <div class="w-25 p-10">
                                        <div class='account-voucher'>
                                            <div class='voucher-left-small'>
                                                <div class='voucher-left-small-content'>-${order.voucher.chietkhau*100}%</div>
                                            </div>
                                            <div class='voucher-right-small'>
                                                <b>${order.voucher.code}</b>
                                                <div data-id="${order.voucher.id}" class="relative promotion-info-icon">
                                                    <i class="fal fa-info-circle main-color-text fz-20"></i>
                                                    <div data-id="${order.voucher.id}" class='voucher-content'>
                                                        <table class='table'>
                                                            <tbody>
                                                                <tr>
                                                                    <td class='w-40'>Mã</td>
                                                                    <td><b>${order.voucher.code}</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class='w-40'>Nội dung</td>
                                                                    <td>${order.voucher.noidung}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class='w-40'>
                                                                        <div class='d-flex flex-column'>
                                                                            <span>Điều kiện:</span>
                                                                            ${order.voucher.dieukien != 0 ?
                                                                                `<ul class="mt-10">
                                                                                    <li>Áp dụng cho đơn hàng từ ${numberWithDot(order.voucher.dieukien)}<sup>đ</sup></li>
                                                                                </ul>` : ''
                                                                            }
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class='w-40'>Hạn sử dụng</td>
                                                                    <td>${order.voucher.ngayketthuc}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>`
                }
                    
                let orderProduct =
                    `<div class="row">
                        <div class="col-lg-12">
                            <table class="table box-shadow">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Giảm giá</th>
                                        <th>Tạm tính</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${productList}
                                    ${order.id_vc ? voucher : ''}
                                    <tr>
                                        <td class="vertical-center">
                                            <div class="pt-20 pb-20 pl-10">
                                                <div class="d-flex justify-content-between mb-10">
                                                    <div>Tạm tính:</div>
                                                    <div>${numberWithDot(provisional)}<sup>đ</sup></div>
                                                </div>
                                                <div class="d-flex justify-content-between mb-10">
                                                    <div>Mã giảm giá:</div>
                                                    <div class="main-color-text">${order.id_vc ? `-${order.voucher.chietkhau*100}%` : '0'}</div>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <div>Tổng tiền:</div>
                                                    <div class="fz-20 fw-600 red">${numberWithDot(order.tongtien)}<sup>đ</sup></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td colspan="4"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>`
                
                const html = `<div>${orderInfo + orderProduct}</div>`
    
                return html
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

            break
        }
        /*=======================================================================================================================
                                                           Bảo hành
        =======================================================================================================================*/
        case 'baohanh': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            // modal chi tiết
            $(document).on('click', '.info-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chi tiết bảo hành');
                bindBaoHanh(id);
            });
    
            function bindBaoHanh(id) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: '/admin/baohanh/ajax-get-baohanh',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // gán dữ liệu cho modal
                        $('#product-img').attr('src', `images/phone/${data.sanpham.hinhanh}`);
                        $('#product-name').text(data.sanpham.tensp);
                        $('#product-ram').text(data.sanpham.ram);
                        $('#product-capacity').text(data.sanpham.dungluong);
                        $('#product-color').text(data.sanpham.mausac);
                        $('#product-imei').text(data.imei);
    
                        $('#warranty').parent().parent().show();
                        $('#start').parent().parent().parent().show();
                        
                        if(data.trangthai == 1){
                            $('#warranty-status').text('Trong bảo hành');
                            $('#warranty-status').removeClass('warning-color').addClass('success-color');
                        } else if(data.trangthai === 'no'){
                            $('#warranty-status').text('Không bảo hành');
                            $('#warranty-status').removeClass('success-color').addClass('warning-color');
                            $('#warranty').parent().parent().hide();
                            $('#start').parent().parent().parent().hide();
                        } else {
                            $('#warranty-status').text('Hết hạn');
                            $('#warranty-status').removeClass('success-color').addClass('warning-color');
                        }
    
                        $('#warranty').text(data.baohanh);
                        $('#start').text(data.ngaymua);
                        $('#end').text(data.ngayketthuc);
    
                        // hiển thị modal
                        $('#modal').modal('show');
                    },
                    error: function() {
                        showAlertTop(errorMessage)
                    }
                });
            }
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    var keyword = $(this).val().toLocaleLowerCase();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'admin/baohanh/ajax-search',
                        type: 'POST',
                        data: {'keyword': keyword},
                        success: function(data){
                            const lst_data = $('#lst_data')
    
                            const searchResult = renderNewRow(page, data)
                            lst_data.append(searchResult);
    
                            if(lst_data.attr('data-loadmore') === 'done') {
                                lst_data.removeAttr('data-loadmore')
                            }
    
                            loadMoreFlag = keyword == '' ? false : true;
                            $('#loadmore').hide();
                        },
                        error: function() {
                            $('#loadmore').hide();
                            showAlertTop(errorMessage)
                        }
                    });
                }, 300);
                $('#lst_data').children().remove();
                $('#loadmore').show();
            });

            break
        }
        /*=======================================================================================================================
                                                            Slideshow
        =======================================================================================================================*/
        case 'slideshow': {
            // modal tạo mới
            $('.create-btn').off('click').click(function(){
                // gán dữ liệu cho modal
                $('#modal-title').text('Tạo mới Slide');

                // thiết lập nút gửi là thêm mới
                $('#action-btn').attr('data-type', 'create');
                $('#action-btn').text('Thêm');

                // hiển thị modal
                $('#modal').modal('show');
            });

            // modal chi tiết
            $(document).on('click', '.info-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chi tiết Slideshow');
                bindSlideshow(id, true);
            });

            // modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chỉnh sửa Slideshow');
                bindSlideshow(id);
            });

            // modal xóa
            $(document).on('click', '.delete-btn', function(){
                var id = $(this).attr('data-id');

                // gán dữ liệu cho modal xóa
                $('#delete-content').text('Xóa slide này ?');
                $('#delete-btn').attr('data-id', id);
                $('#delete-modal').modal('show');
            });

            // thêm|sửa
            $('#action-btn').click(function(){
                // bẫy lỗi
                var valiLink = validateLink($('#link'));
                var valiImage = validateImageSlideshow($('#image_inp'));

                // bẫy lỗi xong kiểm tra loại
                if(valiLink && valiImage){
                    $('.loader').fadeIn();

                    var data = {
                        'link': $('#link').val().trim(),
                        'hinhanh': $('#base64').val(),
                    };

                    // thêm mới
                    if($(this).attr('data-type') == 'create'){
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/slideshow',
                            type: 'POST',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                $('#modal').modal('hide');

                                // thêm vào đầu danh sách
                                const newRow = renderNewRow(page, data.data)
                                $('#lst_data').prepend(newRow);

                                // toast + highlight row
                                toastAndHighlight(CREATE_MESSAGE, data.id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                    // chỉnh sửa
                    else {
                        var id = $(this).attr('data-id');
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/slideshow/' + id,
                            type: 'PUT',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                $('#modal').modal('hide');

                                // thay thế
                                const newRow = renderNewRow(page, data)
                                $(`tr[data-id="${id}"]`).replaceWith(newRow); 
                                
                                // toast + highlight row
                                toastAndHighlight(EDIT_MESSAGE, id)
                            }
                        });
                    }
                }
            });

            // xóa
            $('#delete-btn').click(function(){
                var id = $(this).attr('data-id');

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/slideshow/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');

                        // xóa dòng
                        $(`tr[data-id="${id}"]`).remove();

                        // toast
                        showToast(DELETE_MESSAGE, TOAST_DELETE_TYPE);
                    },
                    error: function() {
                        $('.loader').fadeOut();
                        showAlertTop(errorMessage)
                    }
                });
            });

            // dialog chọn hình
            $('#choose_image').click(function(){
                $('#image_inp').click();
            });

            // chọn hình
            $('#image_inp').change(function(){
                if($(this).val() == ''){
                    return;
                }

                removeRequried($('#image-preview'));

                // kiểm tra file hình
                var fileName = this.files[0].name.split('.');
                var extend = fileName[fileName.length - 1];

                // kiểm tra có phải là hình ảnh không
                if(extend == 'jpg' || extend == 'jpeg' || extend == 'png'){
                    // Byte => KB => MB
                    const size = (this.files[0].size / BYTE) / BYTE
                    if(size > MAX_SIZE_IMAGE) {
                        showAlertTop(maxSizeImageMessage)
                        return
                    }

                    const imgURL = URL.createObjectURL(this.files[0])
                    $('#image-preview').attr('src', imgURL);
                    // url image => base64
                    getBase64FromUrl(imgURL)
                        .then(base64 => $('#base64').val(base64))
                }
                // không phải hình ảnh
                else{
                    showAlertTop('Bạn chỉ có thể upload hình ảnh');
                    $(this).val('');
                    $('#image-preview').attr('src', 'images/700x400.png');
                }
            });

            $('#link').keyup(function(){
                removeRequried($(this));
            });

            // reset modal
            $('#modal').on('hidden.bs.modal', function(){
                $('#form').trigger('reset');
                $('input').attr('readonly', false);
                $('.required').removeClass('required')
                $('.required-text').remove()
                $('#image-preview').attr('src', 'images/700x400.png');
                $('#base64').val('');
                $('#image_inp').val('');
                $('#choose_image').show();
                $('#action-btn').show();
            });

            function bindSlideshow(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/slideshow/ajax-get-slideshow',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);

                        // gán dữ liệu cho modal
                        $('#link').val(data.link);
                        $('#image-preview').attr('src', 'images/slideshow/' + data.hinhanh);
                        bool == true ? $('#choose_image').hide() : $('#choose_image').show();
                    
                        // thiết lập nút gửi là cập nhật
                        $('#action-btn').attr('data-type', 'edit');
                        $('#action-btn').text('Cập nhật');
                        $('#action-btn').attr('data-id', id);

                        // hiển thị modal
                        $('#modal').modal('show');
                    }
                });

                // ẩn/hiện nút thêm (cập nhật);
                bool == false ? $('#action-btn').show() : $('#action-btn').hide();
            }

            // bẫy lỗi link
            function validateLink(link) {
                if(link.hasClass('required')){
                    return false;
                }

                // chưa nhập
                if(link.val() == ''){
                    link.addClass('required');
                    link.after('<span class="required-text">Vui lòng nhập đường dẫn liên kết</span>');
                    return false;
                }

                return true;
            }

            // bẫy lỗi hình ảnh
            function validateImageSlideshow(image) {
                if($('#image-preview').hasClass('required')){
                    return false;
                }

                // chưa chọn
                if(image.val() == '' && $('#image-preview').attr('src') == 'images/700x400.png'){
                    $('#image-preview').addClass('required');
                    $('#image-preview').after('<span class="required-text">Vui lòng chọn hình ảnh</span>');
                    return false;
                }

                return true;
            }

            break
        }
        /*=======================================================================================================================
                                                            Banner
        =======================================================================================================================*/
        case 'banner': {
            // modal tạo mới
            $('.create-btn').off('click').click(function(){
                // gán dữ liệu cho modal
                $('#modal-title').text('Tạo mới Banner');

                // thiết lập nút gửi là thêm mới
                $('#action-btn').attr('data-type', 'create');
                $('#action-btn').text('Thêm');

                // hiển thị modal
                $('#modal').modal('show');
            });

            // modal chi tiết
            $(document).on('click', '.info-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chi tiết Banner');
                bindBanner(id, true);
            });

            // modal chỉnh sửa
            $(document).on('click', '.edit-btn', function(){
                var id = $(this).data('id');
                $('#modal-title').text('Chỉnh sửa Banner');
                bindBanner(id);
            });

            // modal xóa
            $(document).on('click', '.delete-btn', function(){
                var id = $(this).attr('data-id');

                // gán dữ liệu cho modal xóa
                $('#delete-content').text('Xóa banner này ?');
                $('#delete-btn').attr('data-id', id);
                $('#delete-modal').modal('show');
            });

            // thêm|sửa
            $('#action-btn').click(function(){
                // bẫy lỗi
                var valiLink = validateLink($('#link'));
                var valiImage = validateImageBanner($('#image_inp'));

                // bẫy lỗi xong kiểm tra loại
                if(valiLink && valiImage){
                    $('.loader').fadeIn();

                    var data = {
                        'link': $('#link').val(),
                        'hinhanh': $('#base64').val(),
                    };
                    // thêm mới
                    if($(this).attr('data-type') == 'create'){
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/banner',
                            type: 'POST',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                $('#modal').modal('hide');

                                // thêm vào đầu danh sách
                                const newRow = renderNewRow(page, data.data)
                                $('#lst_data').prepend(newRow);

                                // toast + highlight row
                                toastAndHighlight(CREATE_MESSAGE, data.id)
                            },
                            error: function() {
                                $('.loader').fadeOut();
                                showAlertTop(errorMessage)
                            }
                        });
                    }
                    // chỉnh sửa
                    else {
                        var id = $(this).attr('data-id');
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': X_CSRF_TOKEN
                            },
                            url: 'admin/banner/' + id,
                            type: 'PUT',
                            data: data,
                            success:function(data){
                                $('.loader').fadeOut();
                                $('#modal').modal('hide');

                                // thay thế
                                const newRow = renderNewRow(page, data)
                                $(`tr[data-id="${id}"]`).replaceWith(newRow); 
                                
                                // toast + highlight row
                                toastAndHighlight(EDIT_MESSAGE, id)
                            }
                        });
                    }
                }
            });

            // xóa
            $('#delete-btn').click(function(){
                $('.loader').fadeIn()

                var id = $(this).attr('data-id');

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/banner/' + id,
                    type: 'DELETE',
                    success:function(){
                        $('.loader').fadeOut();
                        $('#delete-modal').modal('hide');

                        // xóa dòng
                        $(`tr[data-id="${id}"]`).remove();

                        // toast
                        showToast(DELETE_MESSAGE, TOAST_DELETE_TYPE);
                    },
                    error: function() {
                        $('.loader').fadeOut()
                        showAlertTop(errorMessage)
                    }
                });
            });

            // dialog chọn hình
            $('#choose_image').click(function(){
                $('#image_inp').click();
            });

            // chọn hình
            $('#image_inp').change(function(){
                if($(this).val() == ''){
                    return;
                }

                removeRequried($('#image-preview'));

                // kiểm tra file hình
                var fileName = this.files[0].name.split('.');
                var extend = fileName[fileName.length - 1];

                // kiểm tra có phải là hình ảnh không
                if(extend == 'jpg' || extend == 'jpeg' || extend == 'png'){
                    // Byte => KB => MB
                    const size = (this.files[0].size / BYTE) / BYTE
                    if(size > MAX_SIZE_IMAGE) {
                        showAlertTop(maxSizeImageMessage)
                        return
                    }

                    const imgURL = URL.createObjectURL(this.files[0])
                    $('#image-preview').attr('src', imgURL);
                    // url image => base64
                    getBase64FromUrl(imgURL)
                        .then(base64 => $('#base64').val(base64))
                }
                // không phải hình ảnh
                else{
                    showAlertTop('Bạn chỉ có thể upload hình ảnh');
                    $(this).val('');
                    $('#image-preview').attr('src', 'images/700x400.png');
                }
            });

            $('#link').keyup(function(){
                removeRequried($(this));
            });

            // reset modal
            $('#modal').on('hidden.bs.modal', function(){
                $('#form').trigger('reset');
                $('input').attr('readonly', false);
                $('.required').removeClass('required')
                $('.required-text').remove()
                $('#image-preview').attr('src', 'images/700x400.png');
                $('#base64').val('');
                $('#image_inp').val('');
                $('#choose_image').show();
                $('#action-btn').show();
            });

            function bindBanner(id, bool = false) {
                // lấy dòng theo id gán vào modal
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': X_CSRF_TOKEN
                    },
                    url: 'admin/banner/ajax-get-banner',
                    type: 'POST',
                    data: {'id': id},
                    success:function(data){
                        // thiết lập quyền
                        $('input, textarea').attr('readonly', bool);

                        // gán dữ liệu cho modal
                        $('#link').val(data.link);
                        $('#image-preview').attr('src', `images/banner/${data.hinhanh}`);

                        if(bool) {
                            $('#choose_image').hide();
                            $('#action-btn').hide();
                        } else {
                            $('#choose_image').show();
                            $('#action-btn').show();
                        }
                    
                        // thiết lập nút gửi là cập nhật
                        $('#action-btn').attr('data-type', 'edit');
                        $('#action-btn').text('Cập nhật');
                        $('#action-btn').attr('data-id', id);

                        // hiển thị modal
                        $('#modal').modal('show');
                    },
                    error: function() {
                        showAlertTop(errorMessage)
                    }
                });
            }

            // bẫy lỗi link
            function validateLink(link) {
                if(link.hasClass('required')){
                    return false;
                }

                // chưa nhập
                if(link.val() == ''){
                    link.addClass('required');
                    link.after('<span class="required-text">Vui lòng nhập đường dẫn liên kết</span>');
                    return false;
                }

                return true;
            }

            // bẫy lỗi hình ảnh
            function validateImageBanner(image) {
                if($('#image-preview').hasClass('required')){
                    return false;
                }

                // chưa chọn
                if(image.val() == '' && $('#image-preview').attr('src') == 'images/500x120.png'){
                    $('#image-preview').addClass('required');
                    $('#image-preview').after('<span class="required-text">Vui lòng chọn hình ảnh</span>');
                    return false;
                }

                return true;
            }

            break
        }
        /*=======================================================================================================================
                                                            IMEI
        =======================================================================================================================*/
        case 'imei': {
            if(navigation == 'reload' || navigation == 'back_forward'){
                loadMoreFlag = true;
            }
    
            // tìm kiếm
            $('#search').keyup(function(){
                clearTimeout(timer);
                timer = setTimeout(() => {
                    var keyword = $(this).val().toLowerCase();

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': X_CSRF_TOKEN
                        },
                        url: 'admin/imei/ajax-search',
                        type: 'POST',
                        data: {keyword},
                        success: function(data){
                            const lst_data = $('#lst_data')
    
                            const searchResut = renderNewRow(page, data)
                            lst_data.append(searchResut);
    
                            if(lst_data.attr('data-loadmore') === 'done') {
                                lst_data.removeAttr('data-loadmore')
                            }
    
                            $('#loadmore').hide();
                        },
                        error: function() {
                            $('#loadmore').hide();
                            showAlertTop(errorMessage)
                        }
                    })
                }, 500);

                $('#lst_data').children().remove();
                $('#loadmore').show();
            }); 

            break
        }
    }
});