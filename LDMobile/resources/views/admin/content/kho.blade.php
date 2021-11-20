@extends("admin.layout")
@section("sidebar-warehouse") sidebar-link-selected @stop
@section("content-title") Kho @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-between align-items-center mb-20">
        {{-- create button --}}
        <div type="button" class="create-btn"><i class="fas fa-plus"></i></div>

        
        <div class="d-flex">
            {{-- search --}}
            <div class='relative mr-10'>
                <div class="head-input-grp">
                    <input type="text" id="search" placeholder="Tìm kiếm">
                    <span class='input-icon-right'><i class="fal fa-search"></i></span>
                </div>
            </div>
        
            {{-- filter --}}
            <div class="relative">
                <div id="filter-kho" class="filter-sort-btn"><i class="far fa-filter mr-5"></i>Bộ lọc</div>
                <div class="filter-badge"></div>
                <div class="filter-div">
                    <div class="mb-10 fw-600">Chi nhánh</div>
                    @foreach($lst_branch as $key)
                        <div class="mb-5">
                            <input type="checkbox" name="filter" id="{{'branch-'.$key->id}}" value="{{$key->id}}">
                            <label for="{{'branch-'.$key->id}}">{{$key->diachi}}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
    </div>

    {{-- table --}}
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Chi nhánh</th>
                <th>Sản phẩm</th>
                <th>Số lượng tồn</th>
                <th></th>
            </tr>
        </thead>
        <tbody data-id="{{$lst_branch[0]->id}}" id="lst_data">
            @foreach ($lst_warehouse as $key)
                <tr data-id="{{$key['id']}}">
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key['id']}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key['chinhanh']}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="d-flex pt-10 pb-10">
                            <img src="{{$url_phone.$key['sanpham']->hinhanh}}" alt="" width="80px">
                            <div class="ml-5 fz-14">
                                <div class="d-flex align-items-center fw-600">
                                    {{$key['sanpham']->tensp}} <i class="fas fa-circle ml-5 mr-5 fz-5"></i>{{$key['sanpham']->mausac}}
                                </div>
                                <div>Ram: {{$key['sanpham']->ram}}</div>
                                <div>Dung lượng: {{$key['sanpham']->dungluong}}</div>
                            </div>
                        </div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key['slton']}} Chiếc</div>
                    </td>
                    {{-- nút --}}
                    <td class="vertical-center w-10">
                        <div class="d-flex justify-content-start">
                            <div data-id="{{$key['id']}}" class="edit-btn"><i class="fas fa-pen"></i></div>
                            <div data-id="{{$key['id']}}" data-branch="{{$key['chinhanh']}}" class="delete-btn"><i class="fas fa-trash"></i></div>    
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
                    {{-- chi nhánh --}}
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="branch" class="mb-5 fw-600">Chi nhánh</label>
                            <select id="branch">
                                @foreach ($lst_branch as $key)
                                    <option value="{{$key->id}}">{{$key->diachi}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- sản phẩm & slton --}}
                    <div class="row mb-3">
                        <div class="col-lg-8">
                            <label for="product" class="mb-5 fw-600">Sản phẩm</label>
                            <div class="select">
                                <div id='product-selected' class="select-selected">
                                    <div id='product' class="d-flex"></div>
                                    <input type="hidden" id="product_id_inp">
                                    <i class="far fa-chevron-down fz-14"></i>
                                </div>
                                <div id='product-box' class="select-box">
                                    {{-- tìm kiếm --}}
                                    <div class="select-search">
                                        <input id='search-product' type="text" class="select-search-inp" placeholder="Nhập tên sản phẩm">
                                        <i class="select-search-icon far fa-search"></i>
                                    </div>

                                    {{-- option --}}
                                    <div id='list-product' class="select-option">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label for="qty_in_stock" class="mb-5 fw-600">Số lượng</label>
                            <input type="number" id="qty_in_stock" max="100" min="0" maxlength="3">
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