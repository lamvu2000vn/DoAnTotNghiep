@extends("admin.layout")
@section("sidebar-voucher") sidebar-link-selected @stop
@section("content-title") Voucher @stop
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
                <th>Code</th>
                <th>Chiết khấu</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Số lượng</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_data">
            @foreach ($lst_voucher as $key)
                <tr data-id="{{$key->id}}">
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->id}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->code}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->chietkhau*100 . '%'}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->ngaybatdau}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->ngayketthuc}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->sl}}</div>
                    </td>
                    <td class="vertical-center">
                        <div data-id="{{$key->id}}" class="trangthai pt-10 pb-10">{{$key->trangthai == 1 ? 'Hoạt động' : 'Hết hạn'}}</div>
                    </td>
                    {{-- nút --}}
                    <td class="vertical-center w-10">
                        <div class="d-flex justify-content-start">
                            <div data-id="{{$key->id}}" class="info-btn"><i class="fas fa-info"></i></div>
                            <div data-id="{{$key->id}}" class="edit-btn"><i class="fas fa-pen"></i></div>
                            <div data-id="{{$key->id}}" class="delete-btn"><i class="fas fa-trash"></i></div>
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40">
                <form id="form">
                    {{-- code & chiết khấu & điều kiện & số lượng --}}
                    <div class="row mb-3">
                        <div class="col-lg-4">
                            <label for="code" class="mb-5 fw-600">Code</label>
                            <input type="text" id="code" maxlength="20" placeholder="VD: ABCD">
                        </div>
                        <div class="col-lg-2">
                            <label for="discount" class="mb-5 fw-600">Chiết khẩu</label>
                            <input type="number" id="discount" min="1" max="100" placeholder="VD: 10">
                        </div>
                        <div class="col-lg-4">
                            <label for="condition" class="mb-5 fw-600">Điều kiện</label>
                            <input type="number" id="condition" placeholder="Đơn hàng tối thiểu">
                        </div>
                        <div class="col-lg-2">
                            <label for="qty" class="mb-5 fw-600">Số lượng</label>
                            <input type="number" id="qty">
                        </div>
                    </div>

                    {{-- nội dung --}}
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="content" class="mb-5 fw-600">Nội dung</label>
                            <textarea id="content" rows="3" placeholder="Nhập nội dung voucher"></textarea>
                        </div>
                    </div>

                    {{-- ngày bắt đầu & ngày kết thúc & trạng thái --}}
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="start" class="mb-5 fw-600">Ngày bắt đầu</label>
                            <input type="date" id="start">
                        </div>
                        <div class="col-lg-6">
                            <label for="end" class="mb-5 fw-600">Ngày kết thúc</label>
                            <input type="date" id="end">
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

{{-- modal xóa --}}
@include("user.content.modal.xoa-modal")

<div id="toast"></div>

@stop