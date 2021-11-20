@extends("admin.layout")
@section("sidebar-account-address") sidebar-link-selected @stop
@section("content-title")Tài Khoản Địa Chỉ @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-between align-items-center mb-20">
        {{-- create button --}}
        <div><i></i></div>
        {{-- filter & sort --}}
        <div class="d-flex">
            <div class="head-input-grp pb-20 w-70 ">
                <input type="text" class='head-search-input border' id="account-address-search" placeholder="Tìm kiếm id, họ tên, id_tk,...">
                <span  class='input-icon-right' id="submit-account-address-search"><i class="fal fa-search"></i></span>
            </div>
        </div>
    </div>

    {{-- table --}}
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID_TK</th>
                <th>Họ tên</th>
                <th>SDT</th>
                <th>Mặc định</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_account_address">
            @foreach ($listAccountAddress as $address)
            <tr data-id="{{$address->id}}">
                <td class="vertical-center w-10">{{$address->id}}</td>
                <td class="vertical-center w-25">{{$address->id_tk}}</td>
                <td class="vertical-center w-25">{{$address->hoten}}</td>
                <td class="vertical-center w-25">{{$address->sdt}}</td>
                <td class="vertical-center w-25">{{$address->macdinh}}</td>
                {{-- nút --}}
                <td class="vertical-center w-15">
                    <div class="d-flex justify-content-evenly">
                        <div data-id="{{$address->id}}" class="info-account-address-btn info-btn"><i class="fas fa-info"></i></div>
                        <div data-id="{{$address->id}}" data-object="accountaddress" class="delete-account-address-btn delete-btn">
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
<div class="modal fade" id="account-address-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scollable">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="account-address-modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40">
                {{-- danh sách mẫu sp --}}
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">ID_TK</label>
                        <input type="text" id='idtk' name="idtk" disabled>    
                    </div> 
                    
                    {{-- SDT --}}
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Họ Tên</label>
                        <input type='text' id='fullname' name="fullname" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Địa chỉ chi tiết</label>
                        <input type="text" id='address' name="address" disabled>    
                    </div> 
                    
                    {{-- SDT --}}
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Phường/Xã</label>
                        <input type='text' id='ward' name="ward" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Quận/Huyện</label>
                        <input type="text" id='district' name="district" disabled>    
                    </div> 
                    
                    {{-- SDT --}}
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Tỉnh/Thành</label>
                        <input type='text' id='city' name="city" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Số điện thoại</label>
                        <input type="text" id='phone' name="phone" disabled>    
                    </div> 
                    
                    {{-- SDT --}}
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Mặc định</label>
                        <input type='text' id='default' name="default" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Trạng thái</label>
                        <input type="text" id='status' name="status" disabled>    
                    </div> 
                
                </div>

                {{-- chọn hình ảnh --}}
                
                <div class="d-flex justify-content-end mt-50">
                    <div class="checkout-btn" data-bs-dismiss="modal">Đóng</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delete-account-address-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
                <div class="modal-body p-60">
                    <div id="delete-content" class="fz-20"></div>
                    <div class="mt-30 d-flex justify-content-between">
                        <div class="cancel-btn w-48" data-bs-dismiss="modal">Hủy</div>
                        <div id="delete-account-address-btn" data-id="" class="checkout-btn w-48">Xóa</div>
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