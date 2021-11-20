@extends("admin.layout")
@section("sidebar-branch") sidebar-link-selected @stop
@section("content-title") Chi nhánh @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-between align-items-center mb-20">
        {{-- create button --}}
        <div type="button" class="create-btn"><i class="fas fa-plus"></i></div>

        {{-- search --}}
        <div class='relative mr-10'>
            <div class="head-input-grp">
                <input type="text" id="search" placeholder="Tìm kiếm">
                <span class='input-icon-right'><i class="fal fa-search"></i></span>
            </div>
        </div>
    </div>

    {{-- table --}}
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Chi nhánh</th>
                <th>SĐT</th>
                <th>Tỉnh thành</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_data">
            @foreach ($lst_branch as $key)
                <tr data-id="{{$key->id}}">
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->id}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->diachi}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->sdt}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->tinhthanh}}</div>
                    </td>
                    <td class="vertical-center">
                        <div data-id="{{$key->id}}" class="trangthai pt-10 pb-10">{{$key->trangthai == 1 ? 'Hoạt động' : 'Ngừng hoạt động'}}</div>
                    </td>
                    {{-- nút --}}
                    <td class="vertical-center w-10">
                        <div class="d-flex justify-content-start">
                            <div data-id="{{$key->id}}" class="edit-btn"><i class="fas fa-pen"></i></div>
                            @if ($key->trangthai != 0)
                                <div data-id="{{$key->id}}" class="delete-btn"><i class="fas fa-trash"></i></div>
                            @else
                                <div data-id="{{$key->id}}" class="undelete-btn"><i class="fas fa-trash-undo"></i></div>
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
<div class="modal fade" id="modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40">
                <form id="form">
                    {{-- địa chỉ --}}
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="address" class="mb-5 fw-600">Địa chỉ</label>
                            <textarea id="address" rows="3" maxlength="200" placeholder="Nhập địa chỉ"></textarea>
                        </div>
                    </div>
                    {{-- sdt & tỉnh thành --}}
                    <div class="row mb-3">
                        <div class="col-lg-4">
                            <label for="tel" class="mb-5 fw-600">Số điện thoại</label>
                            <input type="text" id="tel" maxlength="10" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="col-lg-4">
                            <label for="province" class="mb-5 fw-600">Tỉnh thành</label>
                            <select id="province">
                                @foreach ($lst_province as $key)
                                    <option value="{{$key->id}}">{{$key->tentt}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label for="status" class="mb-5 fw-600">Trạng thái</label>
                            <select id="status">
                                <option value="1" selected>Hoạt động</option>
                                <option value="0">Ngừng hoạt động</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-50">
                        <div class="checkout-btn" data-bs-dismiss="modal">Đóng</div>
                        <div id="action-btn" class="main-btn ml-10"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="toast"></div>

{{-- modal xóa --}}
@include("user.content.modal.xoa-modal")

@stop