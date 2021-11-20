@extends("admin.layout")
@section("sidebar-warranty") sidebar-link-selected @stop
@section("content-title") Bảo hành @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-end align-items-center mb-20">
        {{-- search --}}
        <div class='relative'>
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
                <th>IMEI</th>
                <th>Ngày mua</th>
                <th>Ngày kết thúc</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_data">
            @foreach ($lst_warranty as $key)
                <tr data-id="{{$key->id}}">
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->id}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->imei}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->ngaymua}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->ngayketthuc}}</div>
                    </td>
                    {{-- nút --}}
                    <td class="vertical-center w-5">
                        <div class="d-flex justify-content-start">
                            <div data-id="{{$key->id}}" class="info-btn"><i class="fas fa-info"></i></div>
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
                <div class="row">
                    {{-- sản phẩm --}}
                    <div class="col-lg-6 mb-20">
                        {{-- hình ảnh --}}
                        <div class="mb-20">
                            <img id="product-img" src="" alt="" class="w-90 center-img">
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-center">
                            {{-- tên --}}
                            <div id="product-name" class="fz-26 fw-600 mb-10 text-center"></div>
                            <div class="d-flex mb-10">
                                {{-- màu sắc --}}
                                <div class="d-flex mr-20">
                                    <div class="gray-1">Màu sắc:</div>
                                    <div id="product-color" class="ml-5 fw-600"></div>
                                </div>
                                {{-- ram --}}
                                <div class="d-flex ">
                                    <div class="gray-1">Ram:</div>
                                    <div id="product-ram" class="ml-5 fw-600"></div>
                                </div>
                            </div>
                            <div class="d-flex mb-10">
                                {{-- dung lượng --}}
                                <div class="d-flex mr-20">
                                    <div class="gray-1">Dung lượng:</div>
                                    <div id="product-capacity" class="ml-5 fw-600"></div>
                                </div>
                                <div class="d-flex">
                                    <div class="gray-1">IMEI:</div>
                                    <div id="product-imei" class="ml-5 fw-600"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- bảo hành --}}
                    <div class="col-lg-6">
                        <div class="fz-22 fw-600 d-flex align-items-center mb-10">
                            <i class="fas fa-shield-check mr-10"></i>Bảo hành
                        </div>
                        <div class="d-flex align-items-end mb-5">
                            <div class="gray-1">Trạng thái bảo hành:</div>
                            <div id="warranty-status" class="ml-10 fz-20 fw-600"></div>
                        </div>
                        <div class="mb-5">
                            <div class="d-flex">
                                <div class="gray-1">Bảo hành:</div>
                                <div id="warranty" class="ml-5 fw-600"></div>
                            </div>
                        </div>
                        <div class="mb-5">
                            <div class="d-flex">
                                <div class="d-flex">
                                    <div class="gray-1">Bắt đầu:</div>
                                    <div id="start" class="ml-5 fw-600"></div>
                                </div>
                                <div class="d-flex ml-20">
                                    <div class="gray-1">Kết thúc:</div>
                                    <div id="end" class="ml-5 fw-600"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-50">
                    <div class="checkout-btn" data-bs-dismiss="modal">Đóng</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast"></div>

@stop