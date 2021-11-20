<div class="modal fade" id="choose-color-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div type="button" class="btn-close" data-bs-dismiss="modal"></div>
                <div class="p-20">
                    {{-- tên sản phẩm --}}
                    <div id="choose-color-phone-name" class="fz-20 fw-600"></div>

                    {{-- giá --}}
                    <div class="d-flex align-items-center">
                        <div id="choose-color-promotion-price" class="red"></div>
                        <div id="choose-color-price" class="ml-20 gray-1 text-strike"></div>
                    </div><hr>

                    {{-- chọn màu --}}
                    <div class="fw-600">Chọn màu</div>
                    <div class="mb-30">
                        <div id="phone-color"></div>
                    </div>

                    <i id="max-qty-note" class="mb-10">* Số lượng mua tối đa là 5</i>
                    {{-- số lượng --}}
                    <div id="qty-div">
                        <div class="d-flex align-items-center flex-wrap mb-30">
                            <div class="fw-600 mr-20">Chọn số lượng</div>
                            <div class='cart-qty-input'>
                                {{-- tooltip thông báo --}}
                                <div class="tooltip-qty"></div>
                                {{-- số lượng tối đa có thể mua --}}
                                <input type="hidden" id="max-qty">
                    
                                <button type='button' data-component="color" class='update-qty minus'><i class="fas fa-minus"></i></button>
                                <b id="qty">1</b>
                                <button type='button' data-component="color" class='update-qty plus'><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    {{-- nút thêm giỏ hàng --}}
                    <div id="btn-add-cart" class="main-btn w-100"><i class="fas fa-cart-plus mr-10"></i>Thêm vào giỏ hàng</div>
                </div>
            </div>
        </div>
    </div>
</div>