@extends("admin.layout")
@section("sidebar-product") sidebar-link-selected @stop
@section("content-title") Sản phẩm @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-between align-items-center mb-20">
        {{-- create button --}}
        <div type="button" class="create-btn"><i class="fas fa-plus"></i></div>

        {{-- filter & sort --}}
        <div class="d-flex">
            {{-- search --}}
            <div class='relative mr-10'>
                <div class="head-input-grp">
                    <input type="text" id="search" placeholder="Tìm kiếm">
                    <span class='input-icon-right'><i class="fal fa-search"></i></span>
                </div>
            </div>
            {{-- filter --}}
            <div class="relative mr-10">
                <div id="filter-sanpham" class="filter-sort-btn"><i class="far fa-filter mr-5"></i>Bộ lọc</div>
                <div class="filter-badge"></div>
                <div class="filter-div">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-10 fw-600">Ram</div>
                            @foreach ($lst_ram as $i => $key)
                                <div class="mb-5">
                                    <input type="checkbox" name="filter" data-object="ram" id="{{'ram-'.$i}}" value="{{$key->ram}}">
                                    <label for="{{'ram-'.$i}}">{{$key->ram}}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-10 fw-600">Dung lượng</div>
                            @foreach ($lst_capacity as $i => $key)
                                <div class="mb-5">
                                    <input type="checkbox" name="filter" data-object="capacity" id="{{'capacity-'.$i}}" value="{{$key->dungluong}}">
                                    <label for="{{'capacity-'.$i}}">{{$key->dungluong}}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-10 fw-600">Trạng thái</div>
                            <div class="mb-5">
                                <input type="checkbox"name="filter" data-object="status" id="status-1" value="1">
                                <label for="status-1">Kinh doanh</label>
                            </div>
                            <div class="mb-5">
                                <input type="checkbox"name="filter" data-object="status" id="status-0" value="0">
                                <label for="status-0">Ngừng kinh doanh</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- sort --}}
            <div class="relative">
                <div id="sort-sanpham" class="filter-sort-btn"><i class="far fa-sort mr-5"></i>Sắp xếp</div>
                <div class="sort-badge"></div>
                <div class="sort-div">
                    <div class="mb-5">
                        <input type="radio" name="sort" id="id-asc" value="id-asc" checked>
                        <label for="id-asc">ID tăng dần</label>
                    </div>
                    <div class="mb-5">
                        <input type="radio" name="sort" id="id-desc" value="id-desc">
                        <label for="id-desc">ID giảm dần</label>
                    </div>
                    <div class="mb-5">
                        <input type="radio" name="sort" id="price-asc" value="price-asc">
                        <label for="price-asc">Giá từ thấp tới cao</label>
                    </div>
                    <div class="mb-5">
                        <input type="radio" name="sort" id="price-desc" value="price-desc">
                        <label for="price-desc">Giá cao tới thấp</label>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sản phẩm</th>
                <th>Màu sắc</th>
                <th>Ram</th>
                <th>Dung lượng</th>
                <th>Giá</th>
                <th>Khuyến mãi</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_data">
            @foreach ($lst_product as $key)
                <tr data-id="{{$key->id}}">
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->id}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->tensp}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->mausac}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->ram}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->dungluong}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{number_format($key->gia, 0, '', '.')}}<sup>đ</sup></div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->id_km ? ($key->khuyenmai*100).'%' : 'Không có'}}</div>
                    </td>
                    <td class="vertical-center">
                        <div data-id="{{$key->id}}" class="trangthai pt-10 pb-10">{{$key->trangthai == 1 ? 'Kinh doanh' : 'Ngừng kinh doanh'}}</div>
                    </td>
                    {{-- nút --}}
                    <td class="vertical-center w-10">
                        <div class="d-flex justify-content-start">
                            <div data-id="{{$key->id}}" class="info-btn"><i class="fas fa-info"></i></div>
                            <div data-id="{{$key->id}}" class="edit-btn"><i class="fas fa-pen"></i></div>
                            @if ($key->trangthai == 1)
                                <div data-id="{{$key->id}}" data-name="{{$key->tensp.' '.$key->dungluong.' - '.$key->ram.' Ram - '.$key->mausac}}" class="delete-btn"><i class="fas fa-trash"></i></div>    
                            @else
                                <div data-id="{{$key->id}}" data-name="{{$key->tensp.' '.$key->dungluong.' - '.$key->ram.' Ram - '.$key->mausac}}" class="undelete-btn"><i class="fas fa-trash-undo"></i></div>
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
                <div type="button" class="btn-close" data-bs-dismiss="modal"></div>
            </div>
            <div class="modal-body p-40">
                <form id="sanpham-form">
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            {{-- tên mẫu --}}
                            <div class="mb-3">
                                <label for="sanpham_model" class="mb-5 fw-600">Mẫu sản phẩm</label>
                                <select id="sanpham_model"></select>
                            </div>
                            {{-- màu sắc --}}
                            <div class="mb-3">
                                <label for="sanpham_color" class="mb-5 fw-600">Màu sắc</label>
                                <input type="text" id="sanpham_color" placeholder="VD: Đen">
                            </div>
                            {{-- ram & dung lượng --}}
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="sanpham_ram" class="mb-5 fw-600">Ram</label>
                                    <select id="sanpham_ram">
                                        @foreach ($lst_ram as $ram)
                                            <option value="{{$ram->ram}}">{{$ram->ram}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label for="sanpham_capacity" class="mb-5 fw-600">Dung lượng</label>
                                    <select id="sanpham_capacity">
                                        @foreach ($lst_capacity as $capacity)
                                            <option value="{{$capacity->dungluong}}">{{$capacity->dungluong}}</option>
                                        @endforeach
                                        <option value="1 TB">1 TB</option>
                                    </select>
                                </div>
                            </div>
                            {{-- giá & khuyến mãi --}}
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="sanpham_price" class="mb-5 fw-600">Giá</label>
                                    <input type="number" id="sanpham_price" min="0" max="100000000" placeholder="VD: 10000000">
                                </div>
                                <div class="col-lg-6">
                                    <label for="sanpham_promotion" class="mb-5 fw-600">Khuyến mãi</label>
                                    <select id="sanpham_promotion">
                                        <option value="">Không có</option>
                                        @foreach ($lst_promotion as $key)
                                            <option value="{{$key['id']}}">{{$key['chietkhau']*100 . '%'}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- thông số --}}
                            <div class="mb-3">
                                <label for="sanpham_specifications" class="mb-5 fw-600">Thông số kỹ thuật</label>
                                <select id="sanpham_specifications">
                                </select>
                            </div>
                            {{-- trạng thái --}}
                            <div class="mb-3">
                                <label for="sanpham_status" class="mb-5 fw-600">Trạng thái</label>
                                <select id="sanpham_status">
                                    <option value="1" selected>Kinh doanh</option>
                                    <option value="0">Ngừng kinh doanh</option>
                                </select>
                            </div>
                        </div>
                        {{-- hình ảnh --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <div class="fw-600 mb-10">Chọn hình ảnh</div>
                                <div class="image-list"></div>
                            </div>
                        </div>
                    </div>

                    <div id="create-specifications-div" class="row mb-3">
                        <label for="create-specifications-div" class="mb-5 fw-600">Tạo File thông số kỹ thuật</label>
                        <div class="col-lg-12">
                            <div class="border p-20" style="border-radius: 5px">
                                {{-- màn hình --}}
                                <div class="row mb-3">
                                    <div class="col-lg-4 fw-600 fz-24">Màn hình</div>
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label for="cong_nghe_mh" class="mb-5 fw-600">Công nghệ màn hình</label>
                                            <input type="text" id="cong_nghe_mh">
                                        </div>
                                        <div class="mb-3">
                                            <label for="do_phan_giai" class="mb-5 fw-600">Độ phân giải</label>
                                            <input type="text" id="do_phan_giai">
                                        </div>
                                        <div class="mb-3">
                                            <label for="ty_le_mh" class="mb-5 fw-600">Tỷ lệ màn hình</label>
                                            <input type="number" id="ty_le_mh" placeholder="VD: 6.7">
                                        </div>
                                        <div class="mb-3">
                                            <label for="kinh_cam_ung" class="mb-5 fw-600">Kính cảm ứng</label>
                                            <input type="text" id="kinh_cam_ung">
                                        </div>
                                    </div>
                                </div><hr>
                                {{-- camera sau --}}
                                <div class="row mb-3">
                                    <div class="col-lg-4 fw-600 fz-24">Camera sau</div>
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label for="cam_sau_do_phan_giai" class="mb-5 fw-600">Độ phân giải</label>
                                            <input type="text" id="cam_sau_do_phan_giai">
                                        </div>
                                        <div class="mb-3">
                                            <label for="cam_sau_quay_phim" class="mb-5 fw-600">Quay phim</label>
                                            <textarea id="cam_sau_quay_phim" rows="3" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="cam_sau_den_flash" class="mb-5 fw-600">Đèn flash</label>
                                            <input type="text" id="cam_sau_den_flash">
                                        </div>
                                        <div class="mb-3">
                                            <label for="cam_sau_tinh_nang" class="mb-5 fw-600">Tính năng</label>
                                            <textarea id="cam_sau_tinh_nang" rows="3" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                    </div>
                                </div><hr>
                                {{-- camera trước --}}
                                <div class="row mb-3">
                                    <div class="col-lg-4 fw-600 fz-24">Camera trước</div>
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label for="cam_truoc_do_phan_giai" class="mb-5 fw-600">Độ phân giải</label>
                                            <input type="text" id="cam_truoc_do_phan_giai">
                                        </div>
                                        <div class="mb-3">
                                            <label for="cam_truoc_tinh_nang" class="mb-5 fw-600">Tính năng</label>
                                            <textarea id="cam_truoc_tinh_nang" rows="3" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                    </div>
                                </div><hr>
                                {{-- HĐH CPU --}}
                                <div class="row mb-3">
                                    <div class="col-lg-4 fw-600 fz-24">Hệ điều hành & CPU</div>
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label for="HDH" class="mb-5 fw-600">Hệ điều hành</label>
                                            <input type="text" id="HDH">
                                        </div>
                                        <div class="mb-3">
                                            <label for="CPU" class="mb-5 fw-600">CPU</label>
                                            <input type="text" id="CPU">
                                        </div>
                                        <div class="mb-3">
                                            <label for="CPU_speed" class="mb-5 fw-600">Tốc độ xử lý</label>
                                            <input type="text" id="CPU_speed">
                                        </div>
                                        <div class="mb-3">
                                            <label for="GPU" class="mb-5 fw-600">GPU</label>
                                            <input type="text" id="GPU">
                                        </div>
                                    </div>
                                </div><hr>
                                {{-- Lưu trữ --}}
                                <div class="row mb-3">
                                    <div class="col-lg-4 fw-600 fz-24">Lưu trữ</div>
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label for="RAM" class="mb-5 fw-600">Ram</label>
                                            <input type="text" id="RAM">
                                        </div>
                                        <div class="mb-3">
                                            <label for="bo_nho_trong" class="mb-5 fw-600">Bộ nhớ trong</label>
                                            <input type="text" id="bo_nho_trong">
                                        </div>
                                        <div class="mb-3">
                                            <label for="bo_nho_con_lai" class="mb-5 fw-600">Bộ nhớ còn lại (khả dụng)</label>
                                            <input type="text" id="bo_nho_con_lai">
                                        </div>
                                        <div class="mb-3">
                                            <label for="the_nho" class="mb-5 fw-600">Thẻ nhớ</label>
                                            <input type="text" id="the_nho">
                                        </div>
                                    </div>
                                </div><hr>
                                {{-- Kết nối --}}
                                <div class="row mb-3">
                                    <div class="col-lg-4 fw-600 fz-24">Kết nối</div>
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label for="mang_mobile" class="mb-5 fw-600">Mạng di dộng</label>
                                            <input type="text" id="mang_mobile">
                                        </div>
                                        <div class="mb-3">
                                            <label for="SIM" class="mb-5 fw-600">SIM</label>
                                            <input type="text" id="SIM">
                                        </div>
                                        <div class="mb-3">
                                            <label for="wifi" class="mb-5 fw-600">Wifi</label>
                                            <textarea id="wifi" rows="3" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="GPS" class="mb-5 fw-600">GPS</label>
                                            <textarea id="GPS" rows="2" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="bluetooth" class="mb-5 fw-600">Bluetooth</label>
                                            <textarea id="bluetooth" rows="3" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="cong_sac" class="mb-5 fw-600">Cổng sạc</label>
                                            <input type="text" id="cong_sac">
                                        </div>
                                        <div class="mb-3">
                                            <label for="jack_tai_nghe" class="mb-5 fw-600">Jack tai nghe</label>
                                            <input type="text" id="jack_tai_nghe">
                                        </div>
                                        <div class="mb-3">
                                            <label for="ket_noi_khac" class="mb-5 fw-600">Kết nối khác</label>
                                            <textarea id="ket_noi_khac" rows="3" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                    </div>
                                </div><hr>
                                {{-- Thiết kế trọng lượng --}}
                                <div class="row mb-3">
                                    <div class="col-lg-4 fw-600 fz-24">Thiết kế & trọng lượng</div>
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label for="thiet_ke" class="mb-5 fw-600">Thiết kế</label>
                                            <input type="text" id="thiet_ke">
                                        </div>
                                        <div class="mb-3">
                                            <label for="chat_lieu" class="mb-5 fw-600">Chất liệu</label>
                                            <input type="text" id="chat_lieu">
                                        </div>
                                        <div class="mb-3">
                                            <label for="kich_thuoc" class="mb-5 fw-600">Kích thước</label>
                                            <input type="text" id="kich_thuoc">
                                        </div>
                                        <div class="mb-3">
                                            <label for="khoi_luong" class="mb-5 fw-600">Khối lượng</label>
                                            <input type="text" id="khoi_luong">
                                        </div>
                                    </div>
                                </div><hr>
                                {{-- Pin --}}
                                <div class="row mb-3">
                                    <div class="col-lg-4 fw-600 fz-24">Pin</div>
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label for="loai" class="mb-5 fw-600">Loại pin</label>
                                            <input type="text" id="loai">
                                        </div>
                                        <div class="mb-3">
                                            <label for="dung_luong" class="mb-5 fw-600">Dung lượng</label>
                                            <input type="text" id="dung_luong">
                                        </div>
                                        <div class="mb-3">
                                            <label for="cong_nghe" class="mb-5 fw-600">Công nghệ</label>
                                            <textarea id="cong_nghe" rows="3" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                    </div>
                                </div><hr>
                                {{-- Tiện ích --}}
                                <div class="row mb-3">
                                    <div class="col-lg-4 fw-600 fz-24">Tiện ích</div>
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label for="bao_mat" class="mb-5 fw-600">Bảo mật</label>
                                            <textarea id="bao_mat" rows="3" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tinh_nang_khac" class="mb-5 fw-600">Tính năng khác</label>
                                            <textarea id="tinh_nang_khac" rows="3" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="ghi_am" class="mb-5 fw-600">Ghi âm</label>
                                            <input type="text" id="ghi_am">
                                        </div>
                                        <div class="mb-3">
                                            <label for="xem_phim" class="mb-5 fw-600">Xem phim</label>
                                            <textarea id="xem_phim" rows="3" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nghe_nhac" class="mb-5 fw-600">Nghe nhạc</label>
                                            <textarea id="nghe_nhac" rows="3" placeholder="Các thuộc tính cách nhau bằng phím Enter"></textarea>
                                        </div>
                                    </div>
                                </div><hr>
                                {{-- Thông tin khác --}}
                                <div class="row mb-3">
                                    <div class="col-lg-4 fw-600 fz-24">Thông tin khác</div>
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label for="thoi_diem_ra_mat" class="mb-5 fw-600">Thời điểm ra mắt</label>
                                            <input type="month" id="thoi_diem_ra_mat">
                                        </div>
                                    </div>
                                </div>
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