@extends("admin.layout")
@section("sidebar-promotion") sidebar-link-selected @stop
@section("content-title") Khuyến mãi @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-between align-items-center mb-20">
        {{-- create button --}}
        <div type="button" class="create-btn"><i class="fas fa-plus"></i></div>

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
                <th>Khuyến mãi</th>
                <th>Nội dung</th>
                <th>Chiết khẩu</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_data">
            @foreach ($lst_promotion as $key)
                <tr data-id="{{$key->id}}">
                    <td class="vertical-center w-5">
                        <div class="pt-10 pb-10">{{$key->id}}</div>
                    </td>
                    <td class="vertical-center w-15">
                        <div class="pt-10 pb-10">{{$key->tenkm}}</div>
                    </td>
                    <td class="vertical-center w-24">
                        <div class="pt-10 pb-10">{{$key->noidung}}</div>
                    </td>
                    <td class="vertical-center w-10">
                        <div class="pt-10 pb-10">{{($key->chietkhau*100).'%'}}</div>
                    </td>
                    <td class="vertical-center w-10">
                        <div class="pt-10 pb-10">{{$key->ngaybatdau}}</div>
                    </td>
                    <td class="vertival-center w-11">
                        <div class="pt-10 pb-10">{{$key->ngayketthuc}}</div>
                    </td>
                    <td class="vertical-center w-10">
                        <div data-id="{{$key->id}}" class="trangthai pt-10 pb-10">{{$key->trangthai == '1' ? 'Hoạt động' : 'Hết hạn'}}</div>
                    </td>
                    {{-- nút --}}
                    <td class="vertical-center w-15">
                        <div class="d-flex justify-content-start">
                            <div data-id="{{$key->id}}" class="info-btn"><i class="fas fa-info"></i></div>
                            <div data-id="{{$key->id}}" class="edit-btn"><i class="fas fa-pen"></i></div>
                            <div data-id="{{$key->id}}" class="delete-btn">
                                <i class="fas fa-trash"></i>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div id="loadmore" class="text-center"><div class="spinner-border loadmore" role="status"></div></div>
</div>

{{-- modal thêm|sửa mẫu sp --}}
<div class="modal fade" id="khuyenmai-modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40">
                <form id="khuyenmai-form">
                    <div class="row mb-3">
                        {{-- tên km & chiết khấu --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="khuyenmai_name" class="mb-5 fw-600">Tên khuyến mãi</label>
                                <input type="text" id="khuyenmai_name" placeholder="Nhập tên khuyến mãi">
                            </div>
                            <div>
                                <label for="khuyenmai_discount" class="mb-5 fw-600">Chiết khấu</label>
                                <input type="number" id="khuyenmai_discount" min="1" max="100" placeholder="VD: 10">
                            </div>
                        </div>
                        {{-- nội dung --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="khuyenmai_content" class="mb-5 fw-600">Nội dung</label>
                                <textarea id="khuyenmai_content" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    {{-- ngày bắt đầu & ngày kết thúc --}}
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="khuyenmai_start" class="mb-5 fw-600">Ngày bắt đầu</label>
                            <input type="date" id="khuyenmai_start">
                        </div>
                        <div class="col-lg-6">
                            <label for="khuyenmai_end" class="mb-5 fw-600">Ngày kết thúc</label>
                            <input type="date" id="khuyenmai_end">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-50">
                        <div class="checkout-btn" data-bs-dismiss="modal">Đóng</div>
                        <div id="action-khuyenmai-btn" class="main-btn ml-10"></div>
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