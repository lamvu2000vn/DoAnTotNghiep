@extends("admin.layout")
@section("sidebar-account") sidebar-link-selected @stop
@section("content-title") Tài khoản @stop
@section("content")

    <div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-between align-items-center mb-20">
        {{-- create button --}}
        <div type="button" class="create-taikhoan-modal-show create-btn"><i class="fas fa-plus"></i></div>
        
        {{-- filter & sort --}}
        <div class="d-flex">
            <div class="head-input-grp pb-20 w-70 ">
                <input type="text" class='head-search-input border' id="name-search" placeholder="Tìm kiếm id, email, sdt,...">
                <span  class='input-icon-right' data-user="{{session('user')->id}}" id="submit-search"><i class="fal fa-search"></i></span>
            </div>
            <div id="filter-taikhoan" data-user="{{session('user')->id}}" class="filter-sort-btn mr-20" style="margin-left:10px;height: 48px"><i class="far fa-filter mr-5"></i>Bộ lọc</div>
        </div>
        <input id="page" value="1" hidden>
    </div>

    {{-- table --}}
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Loại tài khoản</th>
                <th>Hình thức</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody id="lst_taikhoan">
            @foreach ($listAccount as $account)
            <tr data-id="{{$account->id}}">
                <td class="vertical-center w-10">{{$account->id}}</td>
                <td class="vertical-center w-20">{{$account->sdt ?? "null"}}</td>
                <td class="vertical-center w-20">{{$account->email ?? "null"}}</td>
                <td class="vertical-center w-20">{{$account->loaitk==1? "admin": "user"}}</td>
                <td class="vertical-center w-10">{{$account->htdn}}</td>
                <td class="vertical-center w-50" style="color: {{$account->trangthai==1? "green" : "red"}}">{{$account->trangthai==1? "Hoạt động" : "Khóa"}}</td>
                {{-- nút --}}
                <td class="vertical-center w-15">
                    <div class="d-flex justify-content-evenly">
                        <div data-id="{{$account->id}}" class="info-taikhoan-modal-show info-btn"><i class="fas fa-info"></i></div>
                        <div data-id="{{$account->id}}" class="edit-taikhoan-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                        @if(session('user')->id != $account->id)
                        <div data-id="{{$account->id}}" data-status="{{$account->trangthai}}" class="delete-taikhoan-btn delete-btn" style="background-color: {{$account->trangthai==1? "red" : "gray"}}">
                            <i class="fas fa-trash"></i>
                        </div>
                        @else 
                        <div data-id="{{$account->id}}" data-status="{{$account->trangthai}}" data-user="{{session('user')->id}}" class="delete-taikhoan-btn delete-btn" style="background-color: gray">
                            <i class="fas fa-trash"></i>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    
    </table>
    <!-- Data Loader -->
    <div class="auto-load text-center">
        <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
            x="0px" y="0px" height="60" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
            <path fill="#078FDB"
                d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s"
                    from="0 50 50" to="360 50 50" repeatCount="indefinite" />
            </path>
        </svg>
    </div>
</div>
{{-- modal thêm|sửa hình ảnh --}}
<div class="modal fade" id="taikhoan-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scollable">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40">
                {{-- danh sách mẫu sp --}}
                    {{-- enter phone number --}}
                    <div id='enter-information'>
                        <center>
                        <div class="mb-3">
                            <input id='idAccount' hidden>
                            <input id='idAccountCurrent' value="{{session('user')->id}}" hidden>
                            <div class="form-group">
                                <img id="imgPre" width="10%" src="{{asset('images/user/avatar-default.png')}}" alt="no img" class="img-thumbnail" />
                            </div>
                            <input style="margin-left:220px; margin-top:18px;display:none" type="file" name="anhdaidien" id="ful" />
                            <label for="file" id="file-name" style="margin-top:18px">
                                <span class="file-box"></span>
                                <span class="file-button">
                                  <i class="fa fa-upload" aria-hidden="true"></i>
                                  Chọn hình
                                </span>
                              </label>
                        </div>
                        </center>
                        {{-- họ tên --}}
                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <label for="mausp_name" class="mb-5 fw-600">Họ và tên</label>
                                <input type="text" id='fullname' name="fullname" placeholder="Họ và tên" required>    
                            </div> 
                            
                            {{-- SDT --}}
                            <div class="col-lg-6">
                                <label for="mausp_name" class="mb-5 fw-600">Số điện thoại</label>
                                <input type='text' id='phone' name="phone" maxlength="10" placeholder='Số điện thoại' required>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="mausp_name" class="mb-5 fw-600">Email</label>
                            <input type='text' id='email' name="email" placeholder='Email'>
                        </div>
                        </div>
                        <div class="row mb-3">
                        {{-- password --}}
                            <div class="col-lg-6">
                                <label id="title_password" class="mb-5 fw-600">Password</label>
                                <input type='password' id='password' name="password" maxlength="16" placeholder='Mật khẩu' required>
                            </div>
                            {{-- confirm password --}}
                            <div class="col-lg-6">
                                <label id="title_confirmpassword" class="mb-5 fw-600">Xác nhận password</label>
                                <input type='password' id='confirmpassword' name="confirmpassword" placeholder='Nhập lại mật khẩu' required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <label for="mausp_name" class="mb-5 fw-600">Loại tài khoản</label>
                            <select id='loai_tk' name="loai_tk" aria-label="Default select example">
                                <option value="0">user</option>
                                <option value="1">admin</option>
                            </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="mausp_name" class="mb-5 fw-600">Trạng Thái</label>
                            <select id='trangthai' name="trangthai"  aria-label="Default select example">
                                <option value="1">Hoạt động</option>
                                <option value="0">Khóa</option>
                            </select>
                            </div>
                        </div>
                    </div>  
                    <div class="d-flex justify-content-end mt-50">
                        <div class="checkout-btn" id="close-account" data-bs-dismiss="modal">Đóng</div>
                        <div id="action-taikhoan-btn" data-user="" class="main-btn ml-10"></div>
                    </div>
            </div>
        </div>
    </div>
</div>
{{-- modal xóa --}}
<div class="modal fade" id="delete-taikhoan-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
                <div class="modal-body p-60">
                    <div id="delete-content" class="fz-20"></div>
                    <div class="mt-30 d-flex justify-content-between">
                        <div class="cancel-btn w-48" data-bs-dismiss="modal">Hủy</div>
                        <div id="lock-account-btn" data-id="" class="checkout-btn w-48">Xóa</div>
                    </div>
                </div>
                <input type="hidden" id="id" name="id">
                <input type="hidden" id="object" name="object">
        </div>
    </div>
</div>
<div class="modal fade" id="filter-taikhoan-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div id="filter-modal-title" class="fw-600 fz-22"></div>
            </div>
                <div class="modal-body p-60">
                    <div id="filter-content" class="fz-20">
                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <label for="mausp_name" class="mb-5 fw-600">Hình thức</label>
                            <select id='loaitk' name="hinhthuc"  aria-label="Default select example">
                                <option value="0">user</option>
                                <option value="1">admin</option>
                             
                            </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="mausp_name" class="mb-5 fw-600">Hình thức</label>
                            <select id='trangthaitk' name="hinhthuc"  aria-label="Default select example">
                                <option value="1">Hoạt động</option>
                                <option value="0">Khóa</option>
                            </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="mausp_name" class="mb-5 fw-600">Hình thức</label>
                        <select id='hinhthuc' name="hinhthuc"  aria-label="Default select example">
                            <option value="normal">normal</option>
                            <option value="google">google</option>
                            <option value="facebook">facebook</option>
                        </select>
                        </div>
                    </div>

                    <div class="mt-30 d-flex justify-content-between">
                        <div class="cancel-btn w-48" data-bs-dismiss="modal">Hủy</div>
                        <div id="filter-account-btn" data-user="" data-id="" class="checkout-btn w-48">Xóa</div>
                    </div>
                </div>
                <input type="hidden" id="id" name="id">
                <input type="hidden" id="object" name="object">
        </div>
    </div>
</div>
<div id="toast"></div>

@stop