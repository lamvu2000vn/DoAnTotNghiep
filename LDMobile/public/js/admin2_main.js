$(window).on('load', function(){
    $('.loader').fadeOut();
});

$(function() {
    var page = 1;
    var top = null;
    
    function close_auto_load(){
        $('.auto-load').html("Không còn kết quả nào");
    };
    window.setTimeout(close_auto_load, 3000); 

    $(window).scroll(function(e){
        var scrollTop = $(window).scrollTop();
        var docHeight = $(document).height();
        var winHeight = $(window).height();
        var scrollPercent = (scrollTop) / (docHeight - winHeight);
        var scrollPercentRounded = Math.round(scrollPercent*100);

        if(scrollPercentRounded >= 90){
            $('#btn-scroll-top').css({
                '-ms-transform' : 'translateY(0)',
                'transform' : 'translateY(0)',
            });
            page++;
            if(top!=null){
                page = top;
            }
            if($("#lst_taikhoan").length){
                loadMoreList(page,'admin/taikhoan', $("#lst_taikhoan"));
            }
            if($("#lst_review").length){
                loadMoreList(page,'admin/danhgia', $("#lst_review"));
            }
            if($("#lst_cart").length){
                loadMoreList(page,'admin/giohang', $("#lst_cart"));
            }
            if($("#lst_wishlist").length){
                loadMoreList(page,'admin/spyeuthich', $("#lst_wishlist"));
            }
            if($("#lst_account_address").length){
                loadMoreList(page,'admin/taikhoandiachi', $("#lst_account_address"));
            }
            if($("#lst_notification").length){
                loadMoreList(page,'admin/thongbao', $("#lst_notification"));
            }
            if($("#lst_account_voucher").length){
                loadMoreList(page, 'admin/taikhoanvoucher', $("#lst_account_voucher"));
            }    
            
        }
    });
    function loadMoreList(page, urlPage, idList){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: urlPage,
            type: 'GET',
            data: {
                'page': page,
            },
            success:function(data){
                if (data.length == 0) {
                    top = page;
                    $('.auto-load').html("Không còn kết quả nào");
                    return;
                }
                idList.append(data);
            }
        });
    }
    // xử lý cuộn lên đầu trang
    $('#btn-scroll-top').on('click', function(){
        $(window).scrollTop(0);
        $('.auto-load').html('<svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"x="0px" y="0px" height="60" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve"><path fill="#078FDB"d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50"><animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s"from="0 50 50" to="360 50 50" repeatCount="indefinite" /></path></svg>');
    });
    var timer = null;
    $('#file-name').off('click').click(function(){
        $('#ful').click();
    });
    function readURL(input, idImg) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $(idImg).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }
    $('#ful').change(function () {
        readURL(this, '#imgPre');
    });
    // hiển thị toast
    function showToast(id){
        setTimeout(() => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                // xóa toast
                setTimeout(() => {
                    $(id).remove();
                },100);
    
                $(id).css({
                    'transform': 'translateY(100px)'
                });
            }, 5000);
    
            $(id).css({
                'transform': 'translateY(0)'
            });
        }, 200);
    }
    $('.create-banner-modal-show').off('click').click(function(){
        // gán dữ liệu cho modal
            $('#modal-title').text('Thêm banner');
    
            // thiết lập nút gửi là thêm mới
            $('#action-banner-btn').attr('data-type', 'create');
            $('#action-banner-btn').text('Thêm');
    
            // hiển thị modal
            $('#banner-modal').modal('show');
    });
    $('.create-warranty-modal-show').off('click').click(function(){
        // gán dữ liệu cho modal
            $('#modal-title').text('Thêm banner');
    
            // thiết lập nút gửi là thêm mới
            $('#action-warranty-btn').attr('data-type', 'create');
            $('#action-warranty-btn').text('Thêm');
    
            // hiển thị modal
            $('#warranty-modal').modal('show');
    });
     // hiển thị modal xóa hình ảnh
     $('.delete-warranty-btn').click(function(){
        // gán dữ liệu cho modal xóa
        $('#delete-content').text('Xóa bảo hành này?')
        $('#delete-btn').attr('data-object', 'warranty');
        $('#delete-btn').attr('data-id', $(this).data('id'));
        $('#delete-modal').modal('show');
    });

    $('.create-slideshow-modal-show').off('click').click(function(){
        // gán dữ liệu cho modal
            $('#modal-title').text('Thêm slideshow');
    
            // thiết lập nút gửi là thêm mới
            $('#action-slideshow-btn').attr('data-type', 'create');
            $('#action-slideshow-btn').text('Thêm');
    
            // hiển thị modal
            $('#slideshow-modal').modal('show');
    });
     // hiển thị modal xóa slideshow
     $('.delete-slideshow-btn').click(function(){
        // gán dữ liệu cho modal xóa
        $('#delete-content').text('Xóa Slideshow này?')
        $('#delete-btn').attr('data-object', 'slideshow');
        $('#delete-btn').attr('data-id', $(this).data('id'));
        $('#delete-modal').modal('show');
    });

//----------TÀI KHOẢN--------------------------------------------------------------------------------------------------------
    
    $('.create-taikhoan-modal-show').off('click').click(function(){
        // gán dữ liệu cho modal
            clearModal()
            $('#modal-title').text('Thêm Tài Khoản');
    
            // thiết lập nút gửi là thêm mới
            $('#action-taikhoan-btn').attr('data-type', 'create');
            $('#action-taikhoan-btn').text('Thêm');
    
            // hiển thị modal
            $('#taikhoan-modal').modal('show');
    });
    $('#filter-taikhoan').off('click').click(function(){
          
            $('#filter-modal-title').text('Lọc');
    
            $('#filter-account-btn').text('Lọc');
            $('#filter-account-btn').attr('data-user', $(this).data('user'));
            // hiển thị modal
            $('#filter-taikhoan-modal').modal('show');

    }); 
    $('#filter-account-btn').off('click').click(function(){
        $('.loader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/filterAccount',
            type: 'GET',
            data: {
                'formality': $('#hinhthuc').val(),
                'cate': $('#loaitk').val(),
                'status':$('#trangthaitk').val(),
                'idUser': $(this).data('user')
            },
            success:function(data){
                console.log(data);
                $('#filter-taikhoan-modal').modal('hide')
                $('.loader').fadeOut();                
                // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                 $('#lst_taikhoan').replaceWith(data);
            }
        });
    });
    $('body').on('click', '.delete-taikhoan-btn', function () {
        // gán dữ liệu cho modal xóa
        var currentUser = $(this).data('user');
        if(currentUser != $(this).data('id')){
            if($(this).data('status')==1){
                $('#delete-content').text('Khóa tài khoản này?')
                $('#lock-account-btn').attr('data-object', 'taikhoan');
                $('#lock-account-btn').attr('data-id', $(this).data('id'));
                $('#lock-account-btn').text('Khóa');
                $('#delete-taikhoan-modal').modal('show');
            }
        }
        
      
    });

    $('#phone').keyup(function(){
        if($(this).hasClass('required')){
            $(this).removeClass('required');
            $(this).next().remove();
        }
    });
    $('#phone').keyup(function(){
        if($(this).hasClass('required')){
            $(this).removeClass('required');
            $(this).next().remove();
        }
    });
    $('#fullname').keyup(function(){
        if($(this).hasClass('required')){
            $(this).removeClass('required');
            $(this).next().remove();
        }
    });
    $('#password').keyup(function(){
        if($(this).hasClass('required')){
            $(this).removeClass('required');
            $(this).next().remove();
        }
    });
    $('#confirmpassword').keyup(function(){
        if($(this).hasClass('required')){
            $(this).removeClass('required');
            $(this).next().remove();
        }
    });
    $("#name-search").on('keyup', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            $("#submit-search").click();
        }
    });

    function validateFormAccount(fullname, phone, password, confirmPassword, isCreateOrEdit){
        clearValidate(fullname);
        clearValidate(phone);
        clearValidate(password);
        clearValidate(confirmPassword);
        var v = true;
        var rexPhone =/^(\+84|0)+([3|5|7|8|9])+([0-9]{8})$/;
        if(fullname.val().length==0){
            fullname.addClass('required');
            var required = $('<span class="required-text">Họ tên không được để trống</span>');
            fullname.after(required);
            v = false;
        }
        if(isCreateOrEdit=="create"){
            if(password.val()==0){
                password.addClass('required');
                var required = $('<span class="required-text">Password không được để trống</span>');
                password.after(required);
                v = false;
            }
            if(confirmPassword.val()==0){
                confirmPassword.addClass('required');
                var required = $('<span class="required-text">Xác nhận password không được để trống</span>');
                confirmPassword.after(required);
                v = false;
            }else if(password.val() != confirmPassword.val()){
                confirmPassword.addClass('required');
                var required = $('<span class="required-text">Xác nhận password chưa khớp</span>');
                confirmPassword.after(required);
                v = false;
            }
            if(phone.val().length==0){
                phone.addClass('required');
                var required = $('<span class="required-text">SDT không được để trống</span>');
                phone.after(required);
                v = false;
            }else if(phone.val().length>10){
                phone.addClass('required');
                var required = $('<span class="required-text">SDT không hợp lệ</span>');
                phone.after(required);
                v = false;
            }else if(!phone.val().match(rexPhone)){
                phone.addClass('required');
                var required = $('<span class="required-text">SDT không hợp lệ</span>');
                phone.after(required);
                v = false;
            }else if(checkPhone(phone)){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/admin/checkPhone',
                    type: 'GET',
                    data: {
                        'sdt' : phone.val(),
                    },
                    success:function(data){
                        if(data>0){
                            phone.addClass('required');
                            var required = $('<span class="required-text">SDT đã được sử dụng</span>');
                            phone.after(required);
                        }else {
                            if(v==true){
                                $('#taikhoan-modal').modal('hide');
                                $('.loader').show();
                                addToAccount();
                            }
                           
                        }
                        
                    }
                });
            }
        }else if(v==true)editToAccount();
        
    }
    function clearValidate(id){
        if(id.hasClass('required')){
            id.removeClass('required');
            id.next().remove();
        }
    }
    function clearModal(){
        $("#idAccount").val("");
        $("#fullname").val("");
        $("#phone").val("");
        $("#email").val("");
        $("#loai_tk").val("0");
        $("#trangthai").val("1");
        $("#password").val("");
        $("#ful").val("");
        $("#confirmpassword").val("");
        $("#password").show();
        $("#confirmpassword").show();
        $("#title_password").show();
        $("#title_confirmpassword").show();
        $('#action-taikhoan-btn').show();
        $("#imgPre").prop('src', 'images/user/avatar-default.png');
        enabledModalEditTaiKhoan();
    }
    function enabledModalEditTaiKhoan(){
        $("#phone").prop('disabled', false);
        $("#fullname").prop('disabled', false);
        $("#phone").prop('disabled', false);
        $("#email").prop('disabled', false);
        $("#loai_tk").prop('disabled', false);
        $("#trangthai").prop('disabled', false);
        $("#password").prop('disabled', false);
        $("#ful").prop('disabled', false);
        $("#file-name").prop('disabled', false);
        $("#file-name").show();
        $('#action-taikhoan-btn').show();
    }
    function disabledModalEditTaiKhoan(){
        $("#fullname").prop('disabled', true);
        $("#phone").prop('disabled', true);
        $("#email").prop('disabled', true);
        $("#loai_tk").prop('disabled', true);
        $("#trangthai").prop('disabled', true);
        $("#password").prop('disabled', true);
        $("#ful").prop('disabled', true);
        $("#file-name").prop('disabled', true);
        $("#password").hide();
        $("#confirmpassword").hide();
        $("#title_password").hide();
        $("#title_confirmpassword").hide();
        $("#file-name").hide();
        $("#ful").hide();
    }
    function checkPhone(phone){
         return true;
    }

    $('#action-taikhoan-btn').click(function(){
        if($(this).attr('data-type') == 'create'){
                validateFormAccount($("#fullname"), $("#phone"), $("#password"), $("#confirmpassword"), "create");
        }else{
                validateFormAccount($("#fullname"), $("#phone"), $("#password"), $("#confirmpassword"), "edit");
        }
    });

    function addToAccount(){
        var file_data = $('#ful').prop('files')[0];
        var form_data = new FormData(); // Create a FormData object
        var hoten = $("#fullname").val(); 
        form_data.append('hoten', hoten);
        form_data.append('image', file_data);  
        form_data.append('sdt',  $("#phone").val());
        form_data.append('password',  $("#password").val());
        form_data.append('loaitk',  $("#loai_tk").val());
        form_data.append('trangthai',  $("#trangthai").val());
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/taikhoan',
            type: 'POST',
            data: form_data,
            processData: false, 
            contentType: false,
            success:function(data){
                $('.loader').fadeOut();
                //render dòng mới vào view
                $('#lst_taikhoan').append(data);
                
                //toast
                if($('#toast').children().length){
                    $('#toast').children().remove();
               }
               $('#toast').append('<span id="create-taikhoan-toast" class="alert-toast-right alert-toast-right-success">Thêm mới thành công</span>');
                showToast('#create-taikhoan-toast');
            }
        });
    }
    function editToAccount(){
        var id =  $("#idAccount").val();
        var idCurrent = $("#idAccountCurrent").val();
        var form_data = new FormData();
        var file_data = $('#ful').prop('files')[0];
        var hoten = $("#fullname").val(); 
        form_data.append('hoten', hoten);
        form_data.append('image', file_data);  
        form_data.append('sdt',  $("#phone").val());
        form_data.append('loaitk',  $("#loai_tk").val());
        form_data.append('trangthai',  $("#trangthai").val());
        enabledModalEditTaiKhoan();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/taikhoan/'+id+'?_method=PUT',
            type: 'POST',
            data: form_data,
            processData: false, 
            contentType: false,
            success:function(data){
                $('#taikhoan-modal').modal('hide');
                $('.loader').fadeOut();
                // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                $('tr[data-id="'+id+'"]').replaceWith(data[0]);
                
                // toast
                if($('#toast').children().length){
                    $('#toast').children().remove();
                }
                if(idCurrent==id){
                    $("#avatarHeaderUser").prop('src', 'images/user/'+ data[1].anhdaidien);
                    $("#avatarSideBarUser").prop('src', 'images/user/'+ data[1].anhdaidien);
                    $('#nameSideBar').text(data[1].hoten);
                    $('#nameHeader').text(data[1].hoten);
                }
                
                $('#toast').append('<span id="create-hinhanh-toast" class="alert-toast-right alert-toast-right-success">Chỉnh sửa thành công</span>');
                showToast('#create-hinhanh-toast');
            }
        });
    }

    $('body').on('click', '.edit-taikhoan-modal-show', function () {
        var id = $(this).data('id');
        $('#modal-title').text('Sửa Tài Khoản');
        $('#action-taikhoan-btn').attr('data-type', 'edit');
        $('#action-taikhoan-btn').text('Cập Nhật');
        // lấy dòng theo id gán vào modal
        enabledModalEditTaiKhoan();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/taikhoan/'+id,
            type: 'GET',
            data: {
                
            },
            success:function(data){
                $("#idAccount").val(data.id);
                $("#fullname").val(data.hoten);
                $("#phone").val(data.sdt);
                $("#email").val(data.email);
                $("#loai_tk").val(data.loaitk);
                $("#trangthai").val(data.trangthai);
                $("#password").hide();
                $("#confirmpassword").hide();
                $("#title_password").hide();
                $("#title_confirmpassword").hide();
                
                $("#phone").prop('readonly', true);
                if(data.htdn == "facebook" || data.htdn == "google" ){
                    disabledModalEditTaiKhoan()
                    $("#imgPre").prop('src', data.anhdaidien);
                }else $("#imgPre").prop('src', 'images/user/'+ data.anhdaidien);
            }
        });
        // hiển thị modal
        $('#taikhoan-modal').modal('show');
    });
  
    $('body').on('click', '.info-taikhoan-modal-show', function () {
        var id = $(this).data('id');
        $('#modal-title').text('Chi Tiết Tài Khoản');
        $('#action-taikhoan-btn').attr('data-type', 'info');
        $('#action-taikhoan-btn').text('Cập Nhật');
        $('#action-taikhoan-btn').hide();
        $("#idAccount").val("");
        disabledModalEditTaiKhoan();
        // lấy dòng theo id gán vào modal
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/taikhoan/'+id,
            type: 'GET',
            data: {
                
            },
            success:function(data){
                $("#idAccount").val(data.id);
                $("#fullname").val(data.hoten);
                $("#phone").val(data.sdt);
                $("#email").val(data.email);
                $("#loai_tk").val(data.loaitk);
                $("#trangthai").val(data.trangthai);
                $("#password").hide();
                $("#confirmpassword").hide();
                $("#title_password").hide();
                $("#title_confirmpassword").hide();
                if(data.htdn == "facebook" || data.htdn == "google" ){
                    $("#imgPre").prop('src', data.anhdaidien);
                }else $("#imgPre").prop('src', 'images/user/'+ data.anhdaidien);
                $("#phone").prop('readonly', true);
            }
        });
        // hiển thị modal
        $('#taikhoan-modal').modal('show');
    });
    $('#lock-account-btn').click(function(){
        var id =  $(this).data('id');
        $('.loader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/taikhoan/'+id,
            type: 'DELETE',
            data: {
             },
            success:function(data){
                console.log(data);
                $('#delete-taikhoan-modal').modal('hide');
                $('.loader').fadeOut();                
                // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                 $('tr[data-id="'+id+'"]').replaceWith(data);

                // toast
                if($('#toast').children().length){
                    $('#toast').children().remove();
                }
                $('#toast').append('<span id="create-hinhanh-toast" class="alert-toast-right alert-toast-right-danger">Khóa thành công</span>');
                showToast('#create-hinhanh-toast');
            }
        });
    })
    
    $('#submit-search').click(function(){
        $('.loader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/searchAccount',
            type: 'GET',
            data: {
                'search': $('#name-search').val(),
                'idUser': $(this).data('user')
            },
            success:function(data){
                console.log(data);
                $('.loader').fadeOut();                
                // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                 $('#lst_taikhoan').replaceWith(data);
            }
        });
    })
//----------TÀI KHOẢN------------------------------------------------------------------------------------------------

//----------ĐÁNH GIÁ----------------------------------------------------------------------------------------------
$('#filter-review').off('click').click(function(){
          
    $('#filter-modal-title').text('Lọc');

    // thiết lập nút gửi là thêm mới
    $('#filter-account-btn').attr('data-user', $(this).data('user'));
    $('#filter-account-btn').text('Lọc');

    // hiển thị modal
    $('#filter-review-modal').modal('show');


});
$('#filter-review-btn').off('click').click(function(){
    $('.loader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'admin/filterReview',
        type: 'GET',
        data: {
            'dateStart': $('#dateStart').val(),
            'dateEnd': $('#dateEnd').val(),
        },
        success:function(data){
           
            $('#filter-review-modal').modal('hide')
            $('.loader').fadeOut();                
            // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
             $('#lst_review').replaceWith(data);
        }
    });

});
$("#review-search").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        $("#submit-search-review").click();
    }
});
    $('body').on('click', '.info-reply-btn', function () {
    // gán dữ liệu cho modal
    $('#reply-modal-title').text('Phản hồi của đánh giá #'+$(this).data('id'));
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/phanhoi/'+$(this).data('id'),
            type: 'GET',
            success:function(data){
                $('#lst_reply').replaceWith(data);
            }
    });
    // hiển thị modal
    $('#reply-modal').modal('show');     
    });
    $('body').on('click', '.info-review-btn', function () {
        var id = $(this).data('id');
        $('#modal-title').text('Chi tiết đánh giá');
        // lấy dòng theo id gán vào modal
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/danhgia/'+id,
            type: 'GET',
            data: {
                
            },
            success:function(data){
                $('#fullname').val(data.name);
                $('#name-product').val(data.nameProduct);
                $('#time').val(data.thoigian);
                $('#content').val(data.noidung);
                $('#like').val(data.soluotthich);
                $('#vote').val(data.danhgia);
                var htmlImage ='<div class="d-flex" id="lst_review_img">';
                data.image.forEach(img => {
                    htmlImage += '<img src="images/evaluate/'+img.hinhanh+ '" alt="" width="15%" style="padding:10px">';
                });
                htmlImage +='</div>'
                console.log(htmlImage);
                $('#lst_review_img').replaceWith(htmlImage);
            }
        });
        // hiển thị modal
        $('#review-modal').modal('show');
    });
    $('.delete-review-btn').click(function(){
        // gán dữ liệu cho modal xóa
        $('#delete-content').text('Xóa đánh giá này?')
        $('#delete-review-btn').attr('data-object', 'review');
        $('#delete-review-btn').attr('data-id', $(this).data('id'));
        $('#delete-review-btn').text('Xóa');
        $('#delete-review-modal').modal('show');
    });
    $('#delete-review-btn').click(function (){
        var id =  $(this).attr('data-id');
        $('.loader').show();
        if($(this).data('object')=="review"){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: 'admin/danhgia/'+ id,
                type: 'DELETE',
                data: {
                 },
                success:function(data){
                    console.log(data);
                    $('#delete-review-modal').modal('hide');
                    $('.loader').fadeOut();                
                    // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                     $('tr[data-id="'+id+'"]').remove();
                    location.reload();
    
                    // toast
                    if($('#toast').children().length){
                        $('#toast').children().remove();
                    }
                    $('#toast').append('<span id="create-hinhanh-toast" class="alert-toast-right alert-toast-right-danger">Khóa thành công</span>');
                    showToast('#create-hinhanh-toast');
                }
            });
        }else{
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: 'admin/phanhoi/'+ id,
                type: 'DELETE',
                data: {
                 },
                success:function(data){
                    console.log(data);
                    $('#delete-review-modal').modal('hide');
                    $('.loader').fadeOut();                
                    // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                    $('tr[data-id="'+id+'"]').remove();
                    // toast
                    if($('#toast').children().length){
                        $('#toast').children().remove();
                    }
                    $('#toast').append('<span id="create-hinhanh-toast" class="alert-toast-right alert-toast-right-danger">Xóa thành công</span>');
                    showToast('#create-hinhanh-toast');
                }
            });
        }
       
    })
    $('#submit-search-review').click(function(){
        $('.loader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/searchReview',
            type: 'GET',
            data: {
                'search': $('#review-search').val()
            },
            success:function(data){
                console.log(data);
                $('.loader').fadeOut();                
                // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                 $('#lst_review').replaceWith(data);
            }
        });
    })
    $('body').on('click', '#delete-reply-btn', function () {
        // gán dữ liệu cho modal xóa
        $('#delete-content').text('Xóa Reply này?')
        $('#delete-review-btn').attr('data-object', 'reply');
        $('#delete-review-btn').attr('data-id', $(this).data('id'));
        $('#delete-review-btn').text('Xóa');
        $('#delete-review-modal').modal('show');
    });


//----------ĐÁNH GIÁ----------------------------------------------------------------------------------------------
//----------SAPHAM YEU THICH ----------------------------------------------------------------------------------------------------------
$('body').on('click', '.delete-wishList-btn', function () {
    // gán dữ liệu cho modal xóa
    $('#delete-content').text('Xóa dòng này?')
    $('#delete-wishlist-btn').attr('data-object', 'wishList');
    $('#delete-wishlist-btn').attr('data-id', $(this).data('id'));
    $('#delete-wishlist-btn').text('Xóa');
    $('#delete-wishlist-modal').modal('show');
    console.log($(this).data('id'));
});
$('#delete-wishlist-btn').click(function(){
    var id =  $(this).attr('data-id');
    console.log(id);
    $('.loader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'admin/spyeuthich/'+ id,
        type: 'DELETE',
        data: {
         },
        success:function(data){
            console.log(data);
            $('#delete-wishlist-modal').modal('hide');
            $('.loader').fadeOut();   
            $('tr[data-id="'+id+'"]').remove();
            // toast
            if($('#toast').children().length){
                $('#toast').children().remove();
            }
            $('#toast').append('<span id="create-hinhanh-toast" class="alert-toast-right alert-toast-right-danger">Xóa thành công</span>');
            showToast('#create-hinhanh-toast');
      
        }
    });
})
$("#wishlist-search").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        $("#submit-wishlist-search").click();
    }
});
$('#submit-wishlist-search').click(function (){
    $('.loader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'admin/searchWishList',
        type: 'GET',
        data: {
            'search': $('#wishlist-search').val()
        },
        success:function(data){
            console.log(data);
            $('.loader').fadeOut();                
            // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
             $('#lst_wishlist').replaceWith(data);
        }
    });
});
//----------SAPHAM YEU THICH ----------------------------------------------------------------------------------------------------------
//----------GIO HANG--------------------------------------------------------------------------------------------------------------------------------------------
$('body').on('click', '.delete-cart-btn', function () {
        // gán dữ liệu cho modal xóa
        $('#delete-content').text('Xóa dòng này?')
        $('#delete-cart-btn').attr('data-object', 'cart');
        $('#delete-cart-btn').attr('data-id', $(this).data('id'));
        $('#delete-cart-btn').text('Xóa');
        $('#delete-cart-modal').modal('show');
    });
    $('#delete-cart-btn').click(function(){
        var id =  $(this).attr('data-id');
        console.log(id);
        $('.loader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/giohang/'+ id,
            type: 'DELETE',
            data: {
             },
            success:function(data){
                console.log(data);
                $('#delete-cart-modal').modal('hide');
                $('.loader').fadeOut();   
                $('tr[data-id="'+id+'"]').remove();
                // toast
                if($('#toast').children().length){
                    $('#toast').children().remove();
                }
                $('#toast').append('<span id="create-hinhanh-toast" class="alert-toast-right alert-toast-right-danger">Xóa thành công</span>');
                showToast('#create-hinhanh-toast');
          
            }
        });
    })
    
$("#cart-search").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        $("#submit-cart-search").click();
    }
});
$('#submit-cart-search').click(function (){
    $('.loader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'admin/searchCart',
        type: 'GET',
        data: {
            'search': $('#cart-search').val()
        },
        success:function(data){
            console.log(data);
            $('.loader').fadeOut();                
            // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
             $('#lst_cart').replaceWith(data);
        }
    });
});
//----------GIO HANG--------------------------------------------------------------------------------------------------------------------------------------------
//----------TAI KHOAN VOUCHER------------------------------------------------------------------------------------------------------------------------------------------------------

$('body').on('click', '.info-account-address-btn', function () {
    var id =  $(this).attr('data-id');
    $('#account-address-modal-title').text('Chi tiết của #'+id);
   
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'admin/taikhoandiachi/'+ id,
        type: 'GET',
        data: {
         },
        success:function(data){
            $('#idtk').val(data.id_tk);
            $('#fullname').val(data.hoten);
            $('#address').val(data.diachi);
            $('#phone').val(data.sdt);
            $('#ward').val(data.phuongxa);
            $('#district').val(data.quanhuyen);
            $('#city').val(data.tinhthanh);
            $('#default').val(data.macdinh);
            $('#status').val(data.trangthai);
            $('#account-address-modal').modal('show');
            // toast
        }
    });

});
$('body').on('click', '.delete-account-voucher-btn', function () {
        // gán dữ liệu cho modal xóa
        $('#delete-content').text('Xóa voucher này?')
        $('delete-account-voucher-btn').attr('data-object', 'accountaddress');
        $('#delete-account-voucher-btn').attr('data-id', $(this).data('id'));
        $('#delete-account-voucher-btn').text('Xóa');
        $('#delete-account-voucher-modal').modal('show');
    });
$('#delete-account-voucher-btn').click(function(){
        var id =  $(this).attr('data-id');
        console.log(id);
        $('.loader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/taikhoanvoucher/'+ id,
            type: 'DELETE',
            data: {
             },
            success:function(data){
                $('#delete-account-voucher-modal').modal('hide');
                $('.loader').fadeOut();   
                $('tr[data-id="'+id+'"]').remove();
                // toast
                if($('#toast').children().length){
                    $('#toast').children().remove();
                }
                $('#toast').append('<span id="create-hinhanh-toast" class="alert-toast-right alert-toast-right-danger">Xóa thành công</span>');
                showToast('#create-hinhanh-toast');
          
            }
        });
    });

$("#account-voucher-search").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        $("#submit-account-voucher-search").click();
    }
});
$('#submit-account-voucher-search').click(function (){
    $('.loader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'admin/searchAccountVoucher',
        type: 'GET',
        data: {
            'search': $('#account-voucher-search').val()
        },
        success:function(data){
            console.log(data);
            $('.loader').fadeOut();                
            // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
             $('#lst_account_voucher').replaceWith(data);
        }
    });
});
//----------TAI KHOAN VOUCHER------------------------------------------------------------------------------------------------------------------------------------------------------    
//---------TAI KHOAN DIA CHI-------------------------------------------------------------------------------------------------------------------------------------------------------------------------  
$("#account-address-search").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        $("#submit-account-address-search").click();
    }
});
$('#submit-account-address-search').click(function (){
    $('.loader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'admin/searchAccountAddress',
        type: 'GET',
        data: {
            'search': $('#account-address-search').val()
        },
        success:function(data){
            console.log(data);
            $('.loader').fadeOut();                
            // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
             $('#lst_account_address').replaceWith(data);
        }
    });
});
$('body').on('click', '.delete-account-address-btn', function () {
    // gán dữ liệu cho modal xóa
    $('#delete-content').text('Xóa địa chỉ này?')
    $('#delete-account-address-btn').attr('data-object', 'accountaddress');
    $('#delete-account-address-btn').attr('data-id', $(this).data('id'));
    $('#delete-account-address-btn').text('Xóa');
    $('#delete-account-address-modal').modal('show');
});
$('#delete-account-address-btn').click(function(){
    var id =  $(this).attr('data-id');
    console.log(id);
    $('.loader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'admin/taikhoandiachi/'+ id,
        type: 'DELETE',
        data: {
         },
        success:function(data){
            $('#delete-account-address-modal').modal('hide');
            $('.loader').fadeOut();   
            $('tr[data-id="'+id+'"]').remove();
            // toast
            if($('#toast').children().length){
                $('#toast').children().remove();
            }
            $('#toast').append('<span id="create-hinhanh-toast" class="alert-toast-right alert-toast-right-danger">Xóa thành công</span>');
            showToast('#create-hinhanh-toast');
      
        }
    });
});
//---------TAI KHOAN DIA CHI-------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//---------THONG BAO-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$('.create-notification-modal-show').off('click').click(function(){
        // gán dữ liệu cho modal
        clearModalNotification();
            $('#modal-title').text('Thêm thông báo');
    
            // thiết lập nút gửi là thêm mới
            $('#action-notification-btn').attr('data-type', 'create');
            $('#action-notification-btn').text('Thêm');
    
            // hiển thị modal
            $('#notification-modal').modal('show');
    });
$('#action-notification-btn').off('click').click(function(){  
    if($(this).attr('data-type') == 'create'){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/thongbao',
            type: 'POST',
            data: {
                'title': $('#title').val(),
                'content': $('#content').val(),
                'account': $('#account').val(),
             },
            success:function(data){
                $('#notification-modal').modal('hide');
                $('.loader').fadeOut();   
                $('#lst_notification').append(data)
                // toast
                //toast
                if($('#toast').children().length){
                    $('#toast').children().remove();
               }
               $('#toast').append('<span id="create-taikhoan-toast" class="alert-toast-right alert-toast-right-success">Thêm mới thành công</span>');
                showToast('#create-taikhoan-toast');
          
            }
        });
    }else{
        var id = $('#idNotification').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/thongbao/'+ id,
            type: 'PUT',
            data: {
                'title': $('#title').val(),
                'content': $('#content').val(),
                'account': $('#account').val(),
                'status': $('#status').val(),
             },
            success:function(data){
                $('#notification-modal').modal('hide');
                $('.loader').fadeOut();   
                // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                $('tr[data-id="'+id+'"]').replaceWith(data);
                
                // toast
                if($('#toast').children().length){
                    $('#toast').children().remove();
                }
                $('#toast').append('<span id="create-hinhanh-toast" class="alert-toast-right alert-toast-right-success">Chỉnh sửa thành công</span>');
                showToast('#create-hinhanh-toast');
          
            }
        });
    }
        
    });
    
$('body').on('click', '.delete-notification-btn', function () {
        // gán dữ liệu cho modal xóa
        $('#delete-content').text('Xóa thông báo này?')
        $('#delete-notification-btn').attr('data-object', 'accountaddress');
        $('#delete-notification-btn').attr('data-id', $(this).data('id'));
        $('#delete-notification-btn').text('Xóa');
        $('#delete-notification-modal').modal('show');
    });
$('body').on('click', '#delete-notification-btn', function () {
    var id =  $(this).attr('data-id');
    $('.loader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'admin/thongbao/'+ id,
        type: 'DELETE',
        data: {
         },
        success:function(data){
            $('#delete-notification-modal').modal('hide');
            $('.loader').fadeOut();   
            $('tr[data-id="'+id+'"]').remove();
            // toast
            if($('#toast').children().length){
                $('#toast').children().remove();
            }
            $('#toast').append('<span id="create-hinhanh-toast" class="alert-toast-right alert-toast-right-danger">Xóa thành công</span>');
            showToast('#create-hinhanh-toast');
      
        }
    });
    });
    $('body').on('click', '.edit-notification-modal-show', function () {
        var id = $(this).attr('data-id');
        $('#modal-title').text('Sửa thông báo #'+id);
            // thiết lập nút gửi là thêm mới
            $('#action-notification-btn').attr('data-type', 'edit');
            $('#action-notification-btn').text('Sửa');
    $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/thongbao/'+ id,
            type: 'GET',
            data: {
                
             },
            success:function(data){
                $('#idNotification').val(data.id);
                $('#status').attr("hidden", false);
                $('#title').val(data.tieude);
                $('#content').val(data.noidung);
                $('#account').val(data.id_tk);
                $("#status option[value="+data.trangthaithongbao+"]").prop('selected', true);
                $('#notification-modal').modal('show');
            }
        });
            // hiển thị modal         
    });
    function clearModalNotification(){
        $('#title').val("");
        $('#content').val("");
        $('#account').val("1");
        $('#title').attr("disabled", false);
        $('#content').attr("disabled", false);
        $('#account').attr("disabled", false);
        $('#status').attr("hidden", true);
    }
    function disableNotification(){
        $('#title').attr("disabled", true);
        $('#content').attr("disabled", true);
        $('#account').attr("disabled", true);
        $('#status').attr("disabled", true);
        $('#status').attr("hidden", false);
    }
    $('#filter-thongbao').click(function (){
        $('#filter-modal-title').text('Lọc');
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        today = yyyy + '/' + mm + '/' + dd;
            // thiết lập nút gửi là thêm mới
            $('#filter-account-btn').text('Lọc');
    
            // hiển thị modal
            $('#filter-notification-modal').modal('show');
         
            $('#dateEnd').val(today);
    });
    $('#filter-notification-btn').click(function (){
        $('.loader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/filterNotification',
            type: 'GET',
            data: {
                'dateStart': $('#dateStart').val(),
                'dateEnd': $('#dateEnd').val(),
                'status': $('#status-notification').val(),
            },
            success:function(data){
               
                $('#filter-notification-modal').modal('hide')
                $('.loader').fadeOut();                
                // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                 $('#lst_notification').replaceWith(data);
            }
        });
    });  
    $("#notification-search").on('keyup', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            $("#submit-search-notification").click();
        }
    });
    $('#submit-search-notification').click(function (){
        $('.loader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'admin/searchNotification',
            type: 'GET',
            data: {
                'search': $('#notification-search').val()
            },
            success:function(data){
                $('.loader').fadeOut();                
                // thay thế dòng hiện tại bằng dòng mới chỉnh sửa
                 $('#lst_notification').replaceWith(data);
            }
        });
    });
//---------THONG BAO-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
});