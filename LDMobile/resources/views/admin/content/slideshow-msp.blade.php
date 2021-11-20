@extends("admin.layout")
@section("sidebar-slideshow-msp") sidebar-link-selected @stop
@section("content-title") Sideshow MSP @stop
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
                <th>Mẫu sản phẩm</th>
                <th>Hình ảnh</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_data">
            @foreach ($lst_slide as $i => $key)
                <tr data-id="{{$key['id_msp']}}">
                    <td class="vertical-center w-50">
                        <div class="pt-10 pb-10">{{$key['tenmau']}}</div>
                    </td>
                    <td class="vertical-center">
                        <div data-id="{{$key['id_msp']}}" class="qty-image pt-10 pb-10">{{count($key['slide']) . ' Hình'}}</div>
                    </td>
                    {{-- nút --}}
                    <td class="vertical-center w-10">
                        <div class="d-flex justify-content-start">
                            <div data-id="{{$key['id_msp']}}" class="info-btn"><i class="fas fa-info"></i></div>
                            <div data-id="{{$key['id_msp']}}" class="edit-btn"><i class="fas fa-pen"></i></div>
                            @if (count($key['slide']) != 0)
                            <div data-id="{{$key['id_msp']}}" data-name="{{$key['tenmau']}}" class="delete-btn"><i class="fas fa-trash"></i></div>    
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
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40">
                <form id="form">
                    {{-- mẫu sp --}}
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="model" class="mb-5 fw-600">Mẫu sản phẩm</label>
                            <select id="model"></select>
                        </div>
                    </div>
                    {{-- hình ảnh --}}
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="d-flex">
                                <label for="image_inp" class="mb-5 fw-600">Hình ảnh</label>
                                <b id="qty-image" class="ml-5"></b>
                            </div>
                            <input type="file" id="image_inp" class="none-dp" multiple accept="image/*">
                            <div class="image-preview-div">
                                <div class="row"></div>
                            </div>
                            
                            <div id="choose_image" class="file-input-btn mt-5">Chọn hình</div>
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