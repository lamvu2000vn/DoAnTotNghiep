@extends("admin.layout")
@section("sidebar-notification") sidebar-link-selected @stop
@section("content-title")Thông báo @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-between align-items-center mb-20">
        {{-- create button --}}
        <div type="button" class="create-notification-modal-show create-btn"><i class="fas fa-plus"></i></div>

        {{-- filter & sort --}}
        <div class="d-flex">
            <div class="head-input-grp pb-20 w-70 ">
                <input type="text" class='head-search-input border' id="notification-search" placeholder="Tìm kiếm id, id_tk,...">
                <span  class='input-icon-right' id="submit-search-notification"><i class="fal fa-search"></i></span>
            </div>
            <div id="filter-thongbao" class="filter-sort-btn mr-20" style="margin-left:10px;height: 48px"><i class="far fa-filter mr-5"></i>Bộ lọc</div>
        </div>
    </div>

    {{-- table --}}
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID_TK</th>
                <th>Tiêu đề</th>
                <th>Nội dung</th>
                <th>Thời gian</th>
                <th>Trạng thái thông báo</th>
            </tr>
        </thead>
        <tbody id="lst_notification">
            @foreach ($listNotification as $notification)
            <tr data-id="{{$notification->id}}">
                <td class="vertical-center w-10">{{$notification->id}}</td>
                <td class="vertical-center w-20">{{$notification->id_tk}}</td>
                <td class="vertical-center w-25">{{$notification->tieude}}</td>
                <td class="vertical-center w-20">{{$notification->noidung}}</td>
                <td class="vertical-center w-10">{{$notification->thoigian}}</td>
                <td class="vertical-center w-20" style="color:{{$notification->trangthaithongbao==1? "green" : "red"}}">{{$notification->trangthaithongbao==1? "Đã đọc" : "Chưa đọc"}}</td>
                {{-- nút --}}
                <td class="vertical-center w-15">
                    <div class="d-flex justify-content-evenly">
                        <div data-id="{{$notification->id}}" class="edit-notification-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                        <div data-id="{{$notification->id}}" data-object="notification" class="delete-notification-btn delete-btn">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
<div class="modal fade" id="notification-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scollable">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40">
                {{-- danh sách mẫu sp --}}
                {{-- họ tên --}}
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Tiêu đề</label>
                        <input type="text" id='title' name="title" maxlength="150" placeholder="Tiêu đề" required>    
                        <input type="text" id='idNotification' hidden name="idNotification" >    
                    </div> 
                    
                    {{-- SDT --}}
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Nội dung</label>
                        <input type='text' id='content' name="content" placeholder='Nội dung' required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Cho id tài khoản</label>
                    <select id='account' name="account" aria-label="Default select example">
                        @foreach ($listAccount as $account)
                        <option value="{{$account->id}}">{{$account->id}}</option>
                        @endforeach
                    </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Trạng thái thông báo</label>
                    <select id='status' hidden name="account" aria-label="Default select example">
                        <option value="0">Chưa đọc</option>
                        <option value="1">Đã đọc</option>
                    </select>
                    </div>
                    
                </div>
                {{-- chọn hình ảnh --}}
                
                <div class="d-flex justify-content-end mt-50">
                    <div class="checkout-btn" data-bs-dismiss="modal">Đóng</div>
                    <div id="action-notification-btn" class="main-btn ml-10"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete-notification-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
                <div class="modal-body p-60">
                    <div id="delete-content" class="fz-20"></div>
                    <div class="mt-30 d-flex justify-content-between">
                        <div class="cancel-btn w-48" data-bs-dismiss="modal">Hủy</div>
                        <div id="delete-notification-btn" data-id="" class="checkout-btn w-48">Xóa</div>
                    </div>
                </div>
                <input type="hidden" id="id" name="id">
                <input type="hidden" id="object" name="object">
        </div>
    </div>
</div>
<div class="modal fade" id="filter-notification-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div id="filter-modal-title" class="fw-600 fz-22"></div>
            </div>
                <div class="modal-body p-60">
                    <div id="filter-content" class="fz-20">
                        <div class="row mb-3">
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="mausp_name" class="mb-5 fw-600">Từ ngày</label>
                                    <input type="date" id='dateStart' 
                                    min="2000-01-01" max="2022-07-13">    
                                </div> 
                                
                                {{-- SDT --}}
                                <div class="col-lg-6">
                                    <label for="mausp_name" class="mb-5 fw-600">Đến ngày</label>
                                    <input type='date' id='dateEnd' value="2018-07-22"
                                    min="2000-01-01" max="2022-07-13">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="mausp_name" class="mb-5 fw-600">Trạng thái thông báo</label>
                            <select id='status-notification' name="status"  aria-label="Default select example">
                                <option value="0">Chưa đọc</option>
                                <option value="1">Đã đọc</option>
                            </select>
                            </div>
                        </div>
                       
                    </div>

                    <div class="mt-30 d-flex justify-content-between">
                        <div class="cancel-btn w-48" data-bs-dismiss="modal">Hủy</div>
                        <div id="filter-notification-btn" data-id="" class="checkout-btn w-48">Lọc</div>
                    </div>
                </div>
                <input type="hidden" id="id" name="id">
                <input type="hidden" id="object" name="object">
        </div>
    </div>
</div>
<div id="toast"></div>
@include("user.content.modal.xoa-modal")
@stop