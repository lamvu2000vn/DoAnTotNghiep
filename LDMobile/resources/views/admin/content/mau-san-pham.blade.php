@extends("admin.layout")
@section("sidebar-product-model") sidebar-link-selected @stop
@section("content-title") Mẫu sản phẩm @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-between align-items-center mb-30">
        {{-- create button --}}
        <div type="button" class="create-btn"><i class="fas fa-plus"></i></div>

        {{-- filter --}}
        <div class="d-flex align-items-center">
            {{-- search --}}
            <div class='relative mr-10'>
                <div class="head-input-grp">
                    <input type="text" id="search" placeholder="Tìm kiếm">
                    <span class='input-icon-right'><i class="fal fa-search"></i></span>
                </div>
            </div>
            
            <div class="relative">
                <div id="filter-mausp" class="filter-sort-btn"><i class="far fa-filter mr-5"></i>Bộ lọc</div>
                <div class="filter-badge"></div>
                <div class="filter-div">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="fw-600 mb-10">Nhà cung cấp</div>
                            @foreach ($lst_supplier as $key)
                                <div class="mb-5">
                                    <input type="checkbox" name="filter" data-object="supplier" id="{{'supplier-'.$key->id}}" value="{{$key->id}}">
                                    <label for="{{'supplier-'.$key->id}}">{{$key->tenncc}}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-lg-6">
                            <div class="fw-600 mb-10">Trạng thái</div>
                            <div class="mb-5">
                                <input type="checkbox" name="filter" data-object="status" id="status-1" value="1">
                                <label for="status-1">Kinh doanh</label>
                            </div>
                            <div class="mb-5">
                                <input type="checkbox" name="filter" data-object="status" id="status-0" value="0">
                                <label for="status-0">Ngừng kinh doanh</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên mẫu</th>
                <th>Nhà cung cấp</th>
                <th>Bảo hành</th>
                <th>Địa chỉ bảo hành</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_data">
            @foreach ($lst_model as $key)
                <tr data-id="{{$key->id}}">
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->id}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->tenmau}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->nhacungcap}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->baohanh}}</div>
                    </td>
                    <td class="vertical-center w-30">
                        <div class="pt-10 pb-10">{{$key->diachibaohanh}}</div>
                    </td>
                    <td class="vertical-center w-15">
                        <div data-id="{{$key->id}}" class="trangthai pt-10 pb-10">{{$key->trangthai == '1' ? 'Kinh doanh' : 'Ngừng kinh doanh'}}</div>
                    </td>
                    {{-- nút --}}
                    <td class="vertical-center w-10">
                        <div class="d-flex justify-content-start">
                            <div data-id="{{$key->id}}" class="info-btn"><i class="fas fa-info"></i></div>
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

{{-- modal thêm|sửa mẫu sp --}}
<div class="modal fade" id="modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40" style="overflow-x: hidden">
                <form id="mausp-form">
                    {{-- tên mẫu & nhà cung cấp --}}
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="mausp_name" class="mb-5 fw-600">Tên mẫu sản phẩm</label>
                            <input type="text" id="mausp_name" placeholder="Nhập tên mẫu sản phẩm">
                        </div>
                        <div class="col-lg-6">
                            <label for="mausp_supplier" class="mb-5 fw-600">Nhà cung cấp</label>
                            <select id="mausp_supplier">
                                @foreach ($lst_supplier as $key)
                                    <option value="{{$key->id}}">{{$key->tenncc}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- youtube & bảo hành & địa chỉ bảo hành & trạng thái --}}
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="mausp_youtube" class="mb-5 fw-600">Youtube ID</label>
                            <input type="text" id="mausp_youtube" placeholder="VD: afi59b3ngusb">
                            <div class="mt-5">
                                <iframe id="youtube_iframe" height="200" class="w-100 none-dp" allowfullscreen src=""></iframe>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="mausp_warranty" class="mb-5 fw-600">Bảo hành</label>
                                <select id="mausp_warranty">
                                    <option value="" selected>Không bảo hành</option>
                                    <option value="12 Tháng">12 Tháng</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="mausp_warranty_address" class="mb-5 fw-600">Địa chỉ bảo hành</label>
                                <textarea id="mausp_warranty_address" rows="2" placeholder="Nhập địa chỉ bảo hành"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="mausp_status" class="mb-5 fw-600">Trạng thái</label>
                                <select id="mausp_status">
                                    <option value="1" selected>Kinh doanh</option>
                                    <option value="0">Ngừng kinh doanh</option>
                                </select>
                            </div>
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