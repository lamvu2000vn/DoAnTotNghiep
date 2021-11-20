@extends("admin.layout")
@section("sidebar-evaluate") sidebar-link-selected @stop
@section("content-title") Đánh giá @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-between align-items-center mb-20">
        {{-- create button --}}
        <div><i></i></div>
        
        {{-- filter & sort --}}
        <div class="d-flex">
            <div class="head-input-grp pb-20 w-70 ">
                <input type="text" class='head-search-input border' id="review-search" placeholder="Tìm kiếm id, tai khoản, nội dung,...">
                <span  class='input-icon-right' id="submit-search-review"><i class="fal fa-search"></i></span>
            </div>
            <div class="filter-sort-btn mr-20" id="filter-review" style="margin-left:10px;height: 48px"><i class="far fa-filter mr-5"></i>Bộ lọc</div>
        </div>
    </div>

    {{-- table --}}
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID_TK</th>
                <th>Sản phẩm</th>
                <th>Nội dung</th>
                <th>Danh sách phản hồi</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="lst_review">
            @foreach ($listReview as $review)
            <tr data-id="{{$review->id}}">
                <td class="vertical-center w-10">{{$review->id}}</td>
                <td class="vertical-center w-10">{{$review->id_tk}}</td>
                <td class="vertical-center w-20">{{$review->tensp}}</td>
                <td class="vertical-center w-20">{{$review->noidung}}</td>
                <td class="vertical-center w-10">
                    <div class="d-flex justify-content-evenly">
                        <div data-id="{{$review->id}}" class="info-reply-btn info-reply-btn"><i class="far fa-list-alt"></i></div>
                    </div>
                </td>
                {{-- nút --}}
                <td class="vertical-center w-15">
                    <div class="d-flex justify-content-evenly">
                        <div data-id="{{$review->id}}" class="info-review-btn info-btn"><i class="fas fa-info"></i></div>
                        <div data-id="{{$review->id}}" data-object="review" class="delete-review-btn delete-btn">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="auto-load text-center">
        <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
            x="0px" y="0px" height="60" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
            <path fill="#078FDB"
                d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s"
                    from="0 50 50" to="360 50 50" repeatCount="indefinite" />
            </path>
        </svg>
    </div>
</div>
{{-- modal thêm|sửa banner --}}
<div class="modal fade" id="review-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scollable">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40">
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Người đánh giá</label>
                        <input type="text" id='fullname' name="fullname" disabled>    
                    </div> 
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Sản phẩm</label>
                        <input type='text' id='name-product' name="name-product" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Thời gian</label>
                        <input type="text" id='time' name="time" disabled>     
                    </div> 
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Nội dung</label>
                        <input type='text' id='content' name="content" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Số lượt thích</label>
                        <input type="text" id='like' name="like" disabled>    
                    </div> 
                    <div class="col-lg-6">
                        <label for="mausp_name" class="mb-5 fw-600">Đánh giá</label>
                        <input type='text' id='vote' name="vote" disabled>
                    </div>
                </div>
                <div class="row mb-3"><label for="mausp_name" class="mb-5 fw-600">Hình ảnh</label>
                    <div class="d-flex" id="lst_review_img">
                        <img src="images/phone/iphone_12_red.jpg" alt="" width="50px">
                        <img src="images/phone/iphone_12_red.jpg" alt="" width="50px">
                        <img src="images/phone/iphone_12_red.jpg" alt="" width="50px">
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-50">
                    <div class="checkout-btn" data-bs-dismiss="modal">Đóng</div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- modal thêm|sửa banner --}}
<div class="modal fade" id="reply-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scollable">
        <div class="modal-content">
            {{-- modal header --}}
            <div class="modal-header">
                <div id="reply-modal-title" class="fw-600 fz-22"></div>
            </div>
            <div class="modal-body p-40">
                {{-- danh sách mẫu sp --}}
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Nội dung</th>
                            <th>Thời gian</th>
                            <th></th>
                        </tr>
                    </thead>
                <tbody id="lst_reply">
                </tbody>
                </table>
                <div class="d-flex justify-content-end mt-50">
                    <div class="checkout-btn" data-bs-dismiss="modal">Đóng</div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- modal xóa --}}
<div class="modal fade" id="delete-review-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scollable">
        <div class="modal-content">
                <div class="modal-body p-60">
                    <div id="delete-content" class="fz-20"></div>
                    <div class="mt-30 d-flex justify-content-between">
                        <div class="cancel-btn w-48" data-bs-dismiss="modal">Hủy</div>
                        <div id="delete-review-btn" data-id="" class="checkout-btn w-48">Xóa</div>
                    </div>
                </div>
                <input type="hidden" id="id" name="id">
                <input type="hidden" id="object" name="object">
        </div>
    </div>
</div>
<div class="modal fade" id="filter-review-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div id="filter-modal-title" class="fw-600 fz-22"></div>
            </div>
                <div class="modal-body p-60">
                    <div id="filter-content" class="fz-20">
                        <div class="row mb-3">
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="mausp_name" class="mb-5 fw-600">Từ ngày</label>
                                    <input type="date" id='dateStart' 
                                    min="2000-01-01" max="2022-07-13">    
                                </div> 
                                
                                {{-- SDT --}}
                                <div class="col-lg-6">
                                    <label for="mausp_name" class="mb-5 fw-600">Đến ngày</label>
                                    <input type='date' id='dateEnd' value="2018-07-22"
                                    min="2000-01-01" max="2022-07-13">
                                </div>
                            </div>
                        </div>
                       
                    </div>

                    <div class="mt-30 d-flex justify-content-between">
                        <div class="cancel-btn w-48" data-bs-dismiss="modal">Hủy</div>
                        <div id="filter-review-btn" data-id="" class="checkout-btn w-48">Lọc</div>
                    </div>
                </div>
                <input type="hidden" id="id" name="id">
                <input type="hidden" id="object" name="object">
        </div>
    </div>
</div>
<div id="toast"></div>
{{-- modal xóa --}}
@stop