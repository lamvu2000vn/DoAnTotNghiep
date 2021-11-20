@extends("admin.layout")
@section("sidebar-banner") sidebar-link-selected @stop
@section("content-title") Banner @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-between align-items-center mb-20">
        {{-- create button --}}
        <div type="button" class="create-btn"><i class="fas fa-plus"></i></div>
    </div>

    {{-- table --}}
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Link</th>
                <th>Hình ảnh</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_data">
            @foreach ($banner as $key)
                <tr data-id="{{$key->id}}">
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->id}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->link}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">
                            <img src="{{$url_banner.$key->hinhanh}}" alt="" width="300px">
                        </div>
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
</div>

{{-- modal thêm|sửa --}}
<div class="modal fade" id="modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40">
                <form id="form">
                    {{-- link --}}
                    <div class="mb-3">
                        <label for="link" class="mb-5 fw-600">Link</label>
                        <input type="text" id="link" placeholder="Nhập đường dẫn liên kết">
                    </div>
                    {{-- hình ảnh --}}
                    <div class="mb-3">
                        <label for="image-1" class="mb-5 fw-600">Hình ảnh</label>
                        <div class="col-lg-8 mx-auto">
                            <img id="image-preview" src="images/500x120.png" alt="">
                            <input type="file" id="image_inp" class="none-dp" multiple accept="image/*">
                            <input type="hidden" id="base64">
                            <div id="choose_image" class="file-input-btn mt-5">Chọn hình</div>
                        </div>
                    </div>
                </form>
                <div class="d-flex justify-content-end mt-50">
                    <div class="checkout-btn" data-bs-dismiss="modal">Đóng</div>
                    <div id="action-btn" class="main-btn ml-10"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal xóa --}}
@include("user.content.modal.xoa-modal")

<div id="toast"></div>

@stop