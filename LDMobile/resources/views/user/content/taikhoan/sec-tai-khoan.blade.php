<?php
$user = session('user');
?>
<div class='row'>
    <div class='col-md-3'>
        @section("acc-info-active") account-sidebar-active @stop
        @include("user.content.taikhoan.sec-thanh-chuc-nang")
    </div>
    <div class='col-md-9'>
        <div class="account-div-border">
            <div class="row">
                <div class="col-lg-4">
                    {{-- avatar --}}
                    <div class='account-avatar-div'>
                        @if ($user->htdn == 'normal')
                        <div class='overlay-avatar'>
                            <input id='change-avt-inp' data-modal='avt' type="file" class="none-dp" accept="image/*">
                            <div id='btn-change-avt' class='account-change-img pointer-cs'>Thay đổi</div>
                        </div>
                        @endif
                        <img id='avt-img' src="{{ $user->htdn == 'normal' ? $url_user.$user->anhdaidien : $user->anhdaidien}}" alt="avatar" class='account-avt-img'>
                    </div>
                    {{-- họ tên --}}
                    <div class="mb-20">
                        @if ($user->htdn == 'normal')
                            <div class="d-flex align-items-center justify-content-center">
                                <div id="user_fullname" class="text-center fz-24 black">{{$user->hoten}}</div>
                                <div type="button" id='btn-change-info' class="ml-10"><i class="fas fa-user-edit"></i></div>
                            </div>
                            <div id="change-info-div" class="none-dp mt-10">
                                <div class="mb-5">
                                    <input type="text" name="new_fullname_inp" maxlength="100" placeholder="Họ và tên">
                                </div>
                                <div class="d-flex justify-content-end">
                                    <div type="button" id="change-fullname-btn" class="main-color-text">Cập nhật</div>
                                </div>
                            </div>
                        @else
                            <div class="text-center fz-24 black">{{$user->hoten}}</div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-8">
                    {{-- địa chỉ giao hàng --}}
                    <div class="fw-600 fz-20 mb-10">Thông tin giao hàng</div>
                    <div id="delivery-address">
                        @if ($addressDefault != null)
                            <div id="address-{{$addressDefault->id}}" data-default="true" class="white-bg p-20 border mb-30">
                                <div class="d-flex justify-content-between flex-wrap pb-10">
                                    <div class="d-flex">
                                        <b id="adr-fullname-{{$addressDefault->id}}" class="adr-full-name">{{ $addressDefault->hoten }}</b>
                                        @if ($addressDefault->macdinh == 1)
                                            <div class="d-flex align-items-center success-color ml-15"><i class="far fa-check-circle mr-5"></i>Đang sử dụng</div>
                                        @endif
                                    </div>
                                    <div class="d-flex">
                                        <div type="button" data-id="{{ $addressDefault->id }}" class="btn-edit-address main-color-text">Chỉnh sửa</div>
                                    </div>
                                </div>
            
                                <div class="mb-5">
                                    <span class="adr-content">
                                        {{$addressDefault->diachi.', '.$addressDefault->phuongxa.', '.$addressDefault->quanhuyen.', '.$addressDefault->tinhthanh}}
                                    </span>
                                </div>
            
                                <div id="adr-tel-{{$addressDefault->id}}" class="adr-tel">{{$addressDefault->sdt}}</div>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center white-bg p-20 border mb-30">
                                Bạn chưa có địa chỉ giao hàng.
                                <div id="new-address-show" data-default="true" type="button" class="main-color-text ml-5">Thêm địa chỉ</div>
                            </div>
                        @endif
                    </div>
                    {{-- đổi mật khẩu --}}
                    @if ($user->htdn == 'normal')
                        <div id='btn-change-pw' type="button" data-bs-toggle="modal" data-bs-target="#change-pw-modal" class="d-flex align-items-center main-color-text"><i class="fas fa-key mr-10"></i>Thay đổi mật khẩu</div>
                    @else
                        <div>Tài khoản liên kết: <b>{{$user->htdn}} <i class="fas fa-check-circle success-color ml-5"></i></b></div>
                    @endif
            
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal thêm|sửa địa chỉ --}}
@include("user.content.modal.dia-chi-modal")

{{-- modal cập nhật mật khẩu --}}
<div class="modal fade" id="change-pw-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div type="button" class="btn-close" data-bs-dismiss="modal"></div>
                <div class="p-50">
                    <form id="change-pw-form" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="old-pw" class="fw-600 mb-5">Mật khẩu cũ</label>
                            <input type="password" id='old_pw' placeholder="Nhập mật khẩu cũ">
                        </div>
                        <div class="mb-3">
                            <label for="new-pw" class="fw-600 mb-5">Mật khẩu mới</label>
                            <input type="password" id='new_pw' placeholder="Mật khẩu từ 6-16 ký tự">
                        </div>
                        <div class="mb-3">
                            <input type="password" id='retype_pw' placeholder="Nhập lại mật khẩu mới">
                        </div>
                        <div class="mb-3">
                            <div id="change-pw-btn" class="main-btn w-100">Cập nhật</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal thay đổi ảnh đại diện --}}
<div class="modal fade" id="change-avt" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-8 col-12">
                        <div class="m-10">
                            <img id='pre-avt-big' src="" alt="">
                            <div class="mt-20">
                                <div class="d-flex align-items-center">
                                    <b>Thu Phóng</b>
                                    <b class="ml-10 mr-10">|</b>
                                    <i id='reset-canvas' class="far fa-redo"></i>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-between mt-10">
                                        <span><i class="far fa-search-minus"></i></span>
                                        <div class="flex-fill pl-20 pr-20">
                                            <div class="slidecontainer">
                                                <input type="range" step='0.1' min="-1" max="1" value='0' class="slider" id="zoom-range">
                                            </div>
                                        </div>
                                        <span><i class="far fa-search-plus"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column col-lg-4 col-12">
                        <div class="m-10">
                            <div class="fw-600 text-end">Xem trước</div>
                            <div class="mt-20 mb-20">
                                <div class="preview-avt center-img"></div>
                            </div>
                            <div class="text-end">
                                <span class="reselect-img pointer-cs main-color-text">Chọn ảnh khác</span>
                            </div>
                            <hr>
                        </div>
                        <div class="mt-a">
                            <div class="d-flex flex-fill align-items-end justify-content-end">
                                <div class="cancel-btn mr-10" data-bs-dismiss="modal">Hủy</div>
                                <div class="crop-img main-btn p-10">Cập nhật</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast"></div>