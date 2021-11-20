@extends("admin.layout")
@section("sidebar-supplier") sidebar-link-selected @stop
@section("content-title") Nhà cung cấp @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-between align-items-center mb-20">
        {{-- create button --}}
        <div type="button" class="create-ncc-modal-show create-btn"><i class="fas fa-plus"></i></div>

        {{-- search --}}
        <div class='relative'>
            <div class="head-input-grp">
                <input type="text" id="search" placeholder="Tìm kiếm">
                <span class='input-icon-right'><i class="fal fa-search"></i></span>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nhà cung cấp</th>
                <th>Ảnh đại diện</th>
                <th>Địa chỉ</th>
                <th>SĐT</th>
                <th>Email</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_data">
            @foreach ($lst_supplier as $key)
                <tr data-id="{{$key['id']}}">
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key['id']}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key['tenncc']}}</div>
                    </td>
                    <td class="vertical-center w-10">
                        <div class="pt-10 pb-10">
                            <img src="{{$url_logo.$key['anhdaidien']}}" alt="">
                        </div>
                    </td>
                    <td class="vertical-center w-25">
                        <div class="pt-10 pb-10">{{$key['diachi']}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key['sdt']}}</div>
                    </td>
                    <td class="vertical-center w-10">
                        <div class="pt-10 pb-10">{{$key['email']}}</div>
                    </td>
                    <td class="vertical-center w-10">
                        <div data-id="{{$key['id']}}" class="trangthai pt-10 pb-10">{{$key['trangthai'] == 1 ? 'Hoạt động' : 'Ngừng kinh doanh'}}</div>
                    </td>
                    {{-- nút --}}
                    <td class="vertical-center w-10">
                        <div class="d-flex justify-content-start">
                            <div data-id="{{$key['id']}}" class="info-btn"><i class="fas fa-info"></i></div>
                            <div data-id="{{$key['id']}}" class="edit-ncc-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                            @if ($key['trangthai'] != 0)
                                <div data-id="{{$key['id']}}" data-name="{{$key['tenncc']}}" class="delete-ncc-btn delete-btn"><i class="fas fa-trash"></i></div>
                            @else
                                <div data-id="{{$key['id']}}" data-name="{{$key['tenncc']}}" class="undelete-btn"><i class="fas fa-trash-undo"></i></div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div id="loadmore" class="text-center"><div class="spinner-border loadmore" role="status"></div></div>
</div>

{{-- modal thêm|sửa --}}
<div class="modal fade" id="ncc-modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40">
                <form id="ncc-form">
                    <div class="row">
                        {{-- ảnh đại diện --}}
                        <div class="col-lg-5">
                            <label for="ncc_review_image" class="mb-5 fw-600">Ảnh đại diện</label>
                            <img id="ncc_review_image" src="images/320x320.png" alt="" class="circle-img mb-10">
                            <input type="file" id="ncc_image_inp" class="none-dp" accept="image/*">
                            <input type="hidden" id="ncc_image_base64">
                            <div id="ncc_choose_image" class="file-input-btn">Chọn hình</div>
                        </div>
                        <div class="col-lg-7">
                            {{-- tên ncc --}}
                            <div class="mb-3">
                                <label for="ncc_name" class="mb-5 fw-600">Tên nhà cung cấp</label>
                                <input type="text" id="ncc_name" placeholder="Nhập tên nhà cung cấp">
                            </div>
                            {{-- địa chỉ --}}
                            <div class="mb-3">
                                <label for="ncc_address" class="mb-5 fw-600">Địa chỉ</label>
                                <textarea id="ncc_address" rows="3" placeholder="Nhập địa chỉ"></textarea>
                            </div>
                            {{-- sdt --}}
                            <div class="mb-3">
                                <label for="ncc_tel" class="mb-5 fw-600">Số điện thoại</label>
                                <input type="text" id="ncc_tel" maxlength="10" placeholder="Nhập số điện thoại">
                            </div>
                            {{-- email --}}
                            <div class="mb-3">
                                <label for="ncc_email" class="mb-5 fw-600">Email</label>
                                <input type="email" id="ncc_email" placeholder="Nhập email">
                            </div>

                            {{-- trạng thái --}}
                            <div class="mb-5">
                                <label for="ncc_status" class="mb-5 fw-600">Trạng thái</label>
                                <select id="ncc_status">
                                    <option value="1" selected>Hoạt động</option>
                                    <option value="0">Ngừng kinh doanh</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-50">
                        <div class="checkout-btn" data-bs-dismiss="modal">Đóng</div>
                        <div id="action-ncc-btn" class="main-btn ml-10"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- modal xóa --}}
@include("user.content.modal.xoa-modal")

<div id="toast"></div>

@stop